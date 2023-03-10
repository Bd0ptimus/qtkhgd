<?php

namespace App\Admin\Services;

use App\Admin\Repositories\SchoolClassRepository;
use App\Admin\Repositories\SchoolRepository;
use App\Admin\Repositories\StaffRepository;
use App\Admin\Repositories\TimetableRepository;
use App\Admin\Repositories\LinkingStaffSchoolRepository;
use App\Models\ClassSubject;
use Exception;
use App\Admin\Helpers\ListHelper;
use App\Admin\Repositories\ClassLessonRepository;
use App\Admin\Repositories\ClassTimetableRepository;
use App\Models\ClassLesson;
use App\Models\Timetable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimetableService
{
    protected $timetableRepo;
    protected $schoolRepo;
    protected $staffRepo;
    protected $classRepo;
    protected $classLessonRepo;

    public function __construct(
        TimetableRepository $repo,
        SchoolRepository $schoolRepo,
        StaffRepository $staffRepo,
        SchoolClassRepository $classRepo,
        ClassLessonRepository $classLessonRepo,
        LinkingStaffSchoolRepository $linkingStaffSchoolRepo
    ) {
        $this->timetableRepo = $repo;
        $this->schoolRepo = $schoolRepo;
        $this->staffRepo = $staffRepo;
        $this->classRepo = $classRepo;
        $this->classLessonRepo = $classLessonRepo;
        $this->linkingStaffSchoolRepo = $linkingStaffSchoolRepo;
    }

    public function findByStaff($schoolId, $staffId)
    {
        return $this->timetableRepo->findByStaff($schoolId, $staffId);
    }

    public function findActiveTimetableBySchool($schoolId)
    {
        return $this->timetableRepo->findActiveTimetableBySchool($schoolId);
    }

    public function findBySchool($schoolId)
    {
        return $this->timetableRepo->findBySchoolId($schoolId);
    }

    public function findById($timetableId)
    {
        return $this->timetableRepo->findById($timetableId, ['*'], ['classLessons', 'classLessons.classSubject', 'classLessons.classSubject.staff', 'classLessons.classSubject.subject']);
    }

    public function view($timetableId)
    {
        $timetable = $this->findById($timetableId);
        $lessons = [];
        foreach ($timetable->classLessons as $lesson) {
            $lessons[$lesson->class_id][$lesson->slot] = $lesson;
        }
        return [
            'timetable' => $timetable,
            'lessons' => $lessons
        ];
    }

    //T???o m???ng c??c gi??? tr???ng cho Gi??o vi??n.  $arrSlotGV
    //T???o mang c??c slot c???a 1 l???p trong tu???n $arrSlotLophoc
    //Build m???ng c??c ti???t h???c c???a 1 l???p trong tu???n. $arrLession
    //Foreach t???ng ti???t h???c $arrLession, l???y ra gi??? tr???ng c???a $arrSlotGV, l???y ra $arrSlotLophoc. Merge gi??? tr???ng, l???y random k???t qu???. 
    // Sau khi l???y ra slot c???a GV v?? l???p h???c th?? g???n $Lession cho slot, c???p nh???t l???i slot tr???ng cho gi??o vi??n.  
    public function autoGenerateTimetable($schoolId)
    {
        DB::beginTransaction();
        try {
            $school = $this->schoolRepo->findById($schoolId, ['*'], ['branches', 'classes', 'staffs', 'classes.classSubjects', 'classes.classSubjects.subject']);
            $classes = $school->classes;
            $timetable = $this->timetableRepo->create([
                'school_id' => $schoolId,
                'school_brand_id' => $school->branches[0]->id,
                'from_date' => date('Y-m-d', time())
            ]);

            $checkClassSubject = $this->classRepo->checkAllClassSubjectIfValid($classes);
            if ($checkClassSubject['success'] == false) {
                return $checkClassSubject;
            }
            // format classes
            foreach ($classes as $index => $class) {
                $classes[$index]->slots = $this->classRepo->getLessonSlotByGrade($class->grade);
                $classes[$index]->lessonsInWeek = $this->classRepo->getLessonsInWeek($class);
            }
            //Nh??n vi??n li??n k???t
            //D??nh cho nh??n vi??n tr?????ng kh??c li??n k???t ?????n tr?????ng hi???n t???i
            $infoLinkingStaffsIn = $this->staffRepo->getLinkingStaff($schoolId, 'additional_school_id');  //L???y danh s??ch c??c nh??n vi??n ??ang li??n k???t v???i tr?????ng hi???n t???i: Data l???y ???????c l?? lo???i StaffLinkingSchool
            $staffsLinkedIn = [];// array l??u tr??? lo???i Staff
            foreach ($infoLinkingStaffsIn as $index => $linkingStaff) {
                $linkingStaff->staff->slots = $this->staffRepo->takeSlotlinkingStaffIn($infoLinkingStaffsIn, $linkingStaff); //l???y c??c available slot c???a nh??n vi??n li??n k???t ????
                $listAssignedClass = [];
                foreach ($linkingStaff->staff->assignedClassSubject as $assigned) {
                    $assignedId = $assigned->class_id;
                    $exists = $classes->firstWhere('id', $assignedId);
                    if ($exists !== null) {
                        $listAssignedClass[] = $assigned;
                        $linkingStaff->staff->classesSlot = $this->classRepo->getLessonSlotByGrade($assigned->class->grade);
                    }
                }
                $linkingStaff->staff->classes = $listAssignedClass;
                $staffsLinkedIn[] = $linkingStaff->staff;
            }
            foreach ($staffsLinkedIn as $linkedStaff) {
                foreach ($linkedStaff->classes as $lesson) {
                    $staffs = $staffsLinkedIn;
                    $staffSubject = $this->linkingStaffSchoolRepo->takeLinkingStaffSubject($staffs);
                    $teacher = ListHelper::findObjectInArray($staffSubject, 'subject_id', $lesson->subject->id, 'staff_id');
                    if ($teacher['object']) {
                        foreach ($staffs as $staff) {
                            if ($staff->id == $teacher['object']->staff_id) {
                                $teacherAvailableSlots = $staff->slots;
                            }
                        }
                    } else {
                        $teacherAvailableSlots = array();
                    }
                    if (!empty($teacherAvailableSlots)) {
                        //L???y ra c??c slot c??n tr???ng c???a l???p
                        $classAvailableSlots = $linkedStaff->classesSlot;
                        //L???y ra ds c??c slot kh???p c???a gi??o vi??n v?? l???p v?? ch???n 1 slot b???t k???
                        $availableSlots = $this->availableSlots($teacherAvailableSlots, $classAvailableSlots);
                        if (empty($availableSlots)) {
                            break;
                        }
                        $selectedSlot = array_rand($availableSlots);

                        //G??n m??n h???c cho slot ???????c ch???n
                        $this->classLessonRepo->create([
                            'class_id' => $lesson->class_id,
                            'timetable_id' => $timetable->id,
                            'class_subject_id' => $lesson->id,
                            'slot' => $availableSlots[$selectedSlot]
                        ]);
                        //G??? slot v???a ch???n kh???i ds slot c???a gi??o vi??n
                        unset($teacherAvailableSlots[array_search($availableSlots[$selectedSlot], $teacherAvailableSlots)]);
                        $staffs[$teacher['index']]->slots = $teacherAvailableSlots;

                        //G??? slot v???a ch???n kh???i ds slot c???a l???p
                        unset($classAvailableSlots[array_search($availableSlots[$selectedSlot], $classAvailableSlots)]);
                        // g??? slot ???? assign cho gi??o vi??n li??n k???t trong c??c l???p
                        foreach ($classes as $class) {
                            if ($class->id == $lesson->class_id) {
                                $classSlots = $class->slots;
                                unset($classSlots[array_search($availableSlots[$selectedSlot], $classSlots)]);
                                $class->slots = $classSlots;
                            }
                        }
                    }
                }
            }
            //Nh??n vi??n cu??? tr?????ng
            $staffsLocal = $school->staffs;
            foreach ($staffsLocal as $index => $staff) {
                if ($staff->is_linking_staff == 1) {
                    //D??nh cho nh??n vi??n c???a tr?????ng hi???n t???i ??ang c?? li??n k???t v???i tr?????ng kh??c
                    $staffsLocal[$index]->linkingInfo =  $staff->linkingSchools;
                    $staffsLocal[$index]->slots = $this->staffRepo->getLessonForLinkingStaffTo($staff);
                    continue;
                }

                if($staff->has_baby==1 || $staff->pregnant==1 ){
                    $staffsLocal[$index]->slots = $this->staffRepo->getLessonSlotForPregnantOrWithChild($staffsLocal[$index]->slots ?? $this->staffRepo->getLessonSlotByStaff());
                    continue;
                }
                //D??nh cho nh??n vi??n kh??ng li??n k???t
                $staffsLocal[$index]->slots = $this->staffRepo->getLessonSlotByStaff();
            }
            $staffsLocal = $staffsLocal->values();
            foreach ($classes as $index => $class) {
                Log::info('Class : ' . $class);
                foreach ($class->lessonsInWeek as $lesson) {
                    for ($staffType = 1; $staffType < 3; $staffType++) {
                        //$staffType : lo???i nh??n vi??n - 0: T???t c??? nh??n vi??n li??n k???t
                        //                              1: Nh??n vi??n ch??nh th???c c???a tr?????ng ddang mang thai ho???c c?? con nh???
                        //                              2: Nh??n vi??n th?????ng kh??c c???a tr?????ng
                        //Lo???i nh??n vi??n cho bi???t m???c ????? ??u ti??n c???a nh??n vi??n trong vi???c s???p x???p th???i kh??a bi???u.
                        //staffType c??ng nh??? -> m???c ????? ??u ti??n c??ng cao

                        //---*--- Hi???n t???i ????? v??ng l???p b???t ?????u t??? 1 v?? ch??a m??? ch??? ????? s???p x???p table cho nh??n vi??n c???a tr????ngf kh??c ??ang c?? li??n k???t
                        /////////////////?????i $staffType = 0 cho for() ????? b???t ?????u s???p x???p l???ch cho nh??n vi??n tr?????ng kh??c c?? li??n k???t v???i tr?????ng hi???n t???i

                        //T??m gi??o vi??n gi???ng d???y m??n h???c. L???y ra ds c??c slot c??n tr???ng c???a gi??o vi??n
                        Log::info('Lesson : ' . $lesson);

                        if ($staffType == 0) {
                            $staffs = $staffsLinkedIn;
                            $staffSubject = $this->linkingStaffSchoolRepo->takeLinkingStaffSubject($staffs);
                            $teacher = ListHelper::findObjectInArray($staffSubject, 'subject_id', $lesson->subject->id, 'staff_id');
                            if ($teacher['object']) {
                                foreach ($staffs as $staff) {
                                    if ($staff->id == $teacher['object']->staff_id) {
                                        $teacherAvailableSlots = $staff->slots;
                                    }
                                }
                            } else {
                                $teacherAvailableSlots = array();
                            }
                        } elseif ($staffType == 1) {
                            $staffs = $staffsLocal;
                            $teacher = ListHelper::findObjectInArray($staffs, 'id', $lesson->staff_id, 'id');
                            $teacherAvailableSlots = $teacher['object'] ? $teacher['object']->slots : array();
                        }
                        if (!empty($teacherAvailableSlots)) {
                            //L???y ra c??c slot c??n tr???ng c???a l???p
                            $classAvailableSlots = $class->slots;
                            //L???y ra ds c??c slot kh???p c???a gi??o vi??n v?? l???p v?? ch???n 1 slot b???t k???
                            $availableSlots = $this->availableSlots($teacherAvailableSlots, $classAvailableSlots);
                            if (empty($availableSlots)) {
                                break;
                            }
                            $selectedSlot = array_rand($availableSlots);

                            //G??n m??n h???c cho slot ???????c ch???n
                            $this->classLessonRepo->create([
                                'class_id' => $class->id,
                                'timetable_id' => $timetable->id,
                                'class_subject_id' => $lesson->id,
                                'slot' => $availableSlots[$selectedSlot]
                            ]);
                            //G??? slot v???a ch???n kh???i ds slot c???a gi??o vi??n
                            unset($teacherAvailableSlots[array_search($availableSlots[$selectedSlot], $teacherAvailableSlots)]);
                            $staffs[$teacher['index']]->slots = $teacherAvailableSlots;

                            //G??? slot v???a ch???n kh???i ds slot c???a l???p
                            unset($classAvailableSlots[array_search($availableSlots[$selectedSlot], $classAvailableSlots)]);
                            $classes[$index]->slots = $classAvailableSlots;
                            break;
                        }
                    }
                }
            }
            // K???t th??c gi??o vi??n H??? Linh li??n k???t ??c d???y 3 ti???t ???? ch??? d???nh
            // th???i kh??a bi???u : https://prnt.sc/XHnDjMshFglM
            // database : https://prnt.sc/o91WfKmBAw7k

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            if (env('APP_ENV') !== 'production') dd($ex);
            return [
                'success' => false,
                'message' => "C???u h??nh s??? l?????ng ti???t h???c kh??ng ph?? h???p. Vui l??ng ki???m tra l???i"
            ];
        }

        return [
            'success' => true,
            'message' => "T???o th???i kho?? bi???u t??? ?????ng th??nh c??ng"
        ];
    }




    public function availableSlots($teacherAvailableSlots, $classAvailableSlots)
    {
        $list1 =          ['tue_1', 'wed_1', 'thu_1', 'fri_1', 'sat_1'];
        $list2 = ['mon_2', 'tue_2', 'wed_2', 'thu_2', 'fri_2', 'sat_2'];
        $list3 = ['mon_3', 'tue_3', 'wed_3', 'thu_3', 'fri_3', 'sat_3'];
        $list4 = ['mon_4', 'tue_4', 'wed_4', 'thu_4', 'fri_4', 'sat_4'];
        $list5 = ['mon_5', 'tue_5', 'wed_5', 'thu_5', 'fri_5'];
        $list6 = ['mon_6', 'tue_6', 'wed_6', 'thu_6', 'fri_6', 'sat_6'];
        $list7 = ['mon_7', 'tue_7', 'wed_7', 'thu_7', 'fri_7', 'sat_7'];
        $list8 = ['mon_8', 'tue_8', 'wed_8', 'thu_8', 'fri_8', 'sat_8'];
        $list9 = ['mon_9', 'tue_9', 'wed_9', 'thu_9', 'fri_9', 'sat_9'];
        foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $index) {
            $availableSlots = array_intersect($teacherAvailableSlots, $classAvailableSlots, ${'list' . $index});
            if (count($availableSlots) > 0) return $availableSlots;
        }
    }
}
