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

    //Tạo mảng các giờ trống cho Giáo viên.  $arrSlotGV
    //Tạo mang các slot của 1 lớp trong tuần $arrSlotLophoc
    //Build mảng các tiết học của 1 lớp trong tuần. $arrLession
    //Foreach từng tiết học $arrLession, lấy ra giờ trống của $arrSlotGV, lấy ra $arrSlotLophoc. Merge giờ trống, lấy random kết quả. 
    // Sau khi lấy ra slot của GV và lớp học thì gắn $Lession cho slot, cập nhật lại slot trống cho giáo viên.  
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
            //Nhân viên liên kết
            //Dành cho nhân viên trường khác liên kết đến trường hiện tại
            $infoLinkingStaffsIn = $this->staffRepo->getLinkingStaff($schoolId, 'additional_school_id');  //Lấy danh sách các nhân viên đang liên kết với trường hiện tại: Data lấy được là loại StaffLinkingSchool
            $staffsLinkedIn = [];// array lưu trữ loại Staff
            foreach ($infoLinkingStaffsIn as $index => $linkingStaff) {
                $linkingStaff->staff->slots = $this->staffRepo->takeSlotlinkingStaffIn($infoLinkingStaffsIn, $linkingStaff); //lấy các available slot của nhân viên liên kết đó
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
                        //Lấy ra các slot còn trống của lớp
                        $classAvailableSlots = $linkedStaff->classesSlot;
                        //Lấy ra ds các slot khớp của giáo viên và lớp và chọn 1 slot bất kỳ
                        $availableSlots = $this->availableSlots($teacherAvailableSlots, $classAvailableSlots);
                        if (empty($availableSlots)) {
                            break;
                        }
                        $selectedSlot = array_rand($availableSlots);

                        //Gán môn học cho slot được chọn
                        $this->classLessonRepo->create([
                            'class_id' => $lesson->class_id,
                            'timetable_id' => $timetable->id,
                            'class_subject_id' => $lesson->id,
                            'slot' => $availableSlots[$selectedSlot]
                        ]);
                        //Gỡ slot vừa chọn khỏi ds slot của giáo viên
                        unset($teacherAvailableSlots[array_search($availableSlots[$selectedSlot], $teacherAvailableSlots)]);
                        $staffs[$teacher['index']]->slots = $teacherAvailableSlots;

                        //Gỡ slot vừa chọn khỏi ds slot của lớp
                        unset($classAvailableSlots[array_search($availableSlots[$selectedSlot], $classAvailableSlots)]);
                        // gỡ slot đã assign cho giáo viên liên kết trong các lớp
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
            //Nhân viên cuả trường
            $staffsLocal = $school->staffs;
            foreach ($staffsLocal as $index => $staff) {
                if ($staff->is_linking_staff == 1) {
                    //Dành cho nhân viên của trường hiện tại đang có liên kết với trường khác
                    $staffsLocal[$index]->linkingInfo =  $staff->linkingSchools;
                    $staffsLocal[$index]->slots = $this->staffRepo->getLessonForLinkingStaffTo($staff);
                    continue;
                }

                if($staff->has_baby==1 || $staff->pregnant==1 ){
                    $staffsLocal[$index]->slots = $this->staffRepo->getLessonSlotForPregnantOrWithChild($staffsLocal[$index]->slots ?? $this->staffRepo->getLessonSlotByStaff());
                    continue;
                }
                //Dành cho nhân viên không liên kết
                $staffsLocal[$index]->slots = $this->staffRepo->getLessonSlotByStaff();
            }
            $staffsLocal = $staffsLocal->values();
            foreach ($classes as $index => $class) {
                Log::info('Class : ' . $class);
                foreach ($class->lessonsInWeek as $lesson) {
                    for ($staffType = 1; $staffType < 3; $staffType++) {
                        //$staffType : loại nhân viên - 0: Tất cả nhân viên liên kết
                        //                              1: Nhân viên chính thức của trường ddang mang thai hoặc có con nhỏ
                        //                              2: Nhân viên thường khác của trường
                        //Loại nhân viên cho biết mức độ ưu tiên của nhân viên trong việc sắp xếp thời khóa biểu.
                        //staffType càng nhỏ -> mức độ ưu tiên càng cao

                        //---*--- Hiện tại để vòng lặp bắt đầu từ 1 vì chưa mở chế độ sắp xếp table cho nhân viên của trươngf khác đang có liên kết
                        /////////////////đổi $staffType = 0 cho for() để bắt đầu sắp xếp lịch cho nhân viên trường khác có liên kết với trường hiện tại

                        //Tìm giáo viên giảng dạy môn học. Lấy ra ds các slot còn trống của giáo viên
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
                            //Lấy ra các slot còn trống của lớp
                            $classAvailableSlots = $class->slots;
                            //Lấy ra ds các slot khớp của giáo viên và lớp và chọn 1 slot bất kỳ
                            $availableSlots = $this->availableSlots($teacherAvailableSlots, $classAvailableSlots);
                            if (empty($availableSlots)) {
                                break;
                            }
                            $selectedSlot = array_rand($availableSlots);

                            //Gán môn học cho slot được chọn
                            $this->classLessonRepo->create([
                                'class_id' => $class->id,
                                'timetable_id' => $timetable->id,
                                'class_subject_id' => $lesson->id,
                                'slot' => $availableSlots[$selectedSlot]
                            ]);
                            //Gỡ slot vừa chọn khỏi ds slot của giáo viên
                            unset($teacherAvailableSlots[array_search($availableSlots[$selectedSlot], $teacherAvailableSlots)]);
                            $staffs[$teacher['index']]->slots = $teacherAvailableSlots;

                            //Gỡ slot vừa chọn khỏi ds slot của lớp
                            unset($classAvailableSlots[array_search($availableSlots[$selectedSlot], $classAvailableSlots)]);
                            $classes[$index]->slots = $classAvailableSlots;
                            break;
                        }
                    }
                }
            }
            // Kết thúc giáo viên Hạ Linh liên kết đc dạy 3 tiết đã chỉ dịnh
            // thời khóa biểu : https://prnt.sc/XHnDjMshFglM
            // database : https://prnt.sc/o91WfKmBAw7k

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            if (env('APP_ENV') !== 'production') dd($ex);
            return [
                'success' => false,
                'message' => "Cấu hình số lượng tiết học không phù hợp. Vui lòng kiểm tra lại"
            ];
        }

        return [
            'success' => true,
            'message' => "Tạo thời khoá biểu tự động thành công"
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
