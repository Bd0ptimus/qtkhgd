<?php

namespace App\Admin\Controllers\School;

use App\Admin\Admin;
use App\Admin\Helpers\ListHelper;
use App\Admin\Models\AdminUser;
use App\Admin\Services\CommonService;
use App\Admin\Services\FileUploadService;
use App\Admin\Services\RegularGroupService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\StaffService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TaskService;
use App\Admin\Services\TeacherPlanService;
use App\Admin\Services\TeacherLessonService;
use App\Http\Controllers\Controller;
use App\Models\LessonSample;
use App\Models\TeacherLesson;
use App\Models\TeacherPlan;
use App\Models\SchoolStaff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TeacherPlanController extends Controller
{

    protected $commonService;
    protected $teacherPlanService;
    protected $schoolService;
    protected $staffService;
    protected $rgService;
    protected $subjectService;
    protected $taskService;
    protected  $fileUploadService;
    protected $teacherLessonService;

    public function __construct(
        CommonService $commonService,
        SchoolService $schoolService, 
        TeacherPlanService $teacherPlanService,
        StaffService $staffService,
        RegularGroupService $rgService,
        SubjectService $subjectService,
        TaskService $taskService,
        FileUploadService $fileUploadService,
        TeacherLessonService $teacherLessonService
    )
    {
        $this->commonService = $commonService;
        $this->teacherPlanService = $teacherPlanService;
        $this->schoolService = $schoolService;
        $this->staffService = $staffService;
        $this->rgService = $rgService;
        $this->subjectService = $subjectService;
        $this->taskService = $taskService;
        $this->fileUploadService = $fileUploadService;
        $this->teacherLessonService = $teacherLessonService;
    }

    public function allStaff($id)
    {   
        return view('admin.school.staff.plan.list', $this->teacherPlanService->allStaffBySchool($id));
    }

    public function all($schoolId, $staffId) {
        $data['school'] = $this->schoolService->findById($schoolId);
        $date['schoolId'] = $schoolId;
        $data['staff'] = $this->staffService->findById($staffId);
        $data['canManage'] = $this->teacherPlanService->checkIfCanMange($staffId);
        $data['staffGroups'] = $staffId ? $this->rgService->allByStaff($staffId) : [];
        
        return view('admin.school.staff.plan.all', $data);
    }

    public function index($schoolId, $staffId, $rgId) {

        $data['school'] = $this->schoolService->findById($schoolId);
        $date['schoolId'] = $schoolId;
        $data['staff'] = $this->staffService->findById($staffId);
        $data['canManage'] = $this->teacherPlanService->checkIfCanMange($staffId);
        $data['staffGroups'] = $staffId ? $this->rgService->allByStaff($staffId) : [];
        $data['regularGroup'] = $this->rgService->findByGroupId($rgId);
        $data['teacherPlans'] = $this->teacherPlanService->findByStaffAndGroup($staffId, $rgId);
        if(in_array( $data['school']->school_type, [SCHOOL_MN, SCHOOL_TH])) {
            $data['grades'] = array_intersect($data['staff']->staffGrades->pluck('grade')->toArray(), $data['regularGroup']->groupGrades->pluck('grade')->toArray());
            $data['subjects'] = $this->subjectService->getAllBySchool($schoolId)->pluck('name', 'id')->toArray();
        } else {
            $data['grades'] = $data['staff']->staffGrades->pluck('grade')->toArray();
            $data['subjects'] = array_intersect($data['staff']->subjects->pluck('name', 'id')->toArray(), $data['regularGroup']->subjects->pluck('name', 'id')->toArray());
        }
        return view('admin.school.staff.plan.index', $data);
    }

    public function create(Request $request, $schoolId, $staffId, $rgId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $regularGroup = $this->rgService->findByGroupId($rgId);

        if(in_array( $school->school_type, [SCHOOL_MN, SCHOOL_TH])) {
            $gradeOptions = array_intersect($staff->staffGrades->pluck('grade')->toArray(), $regularGroup->groupGrades->pluck('grade')->toArray());
            $subjectOptions = $this->subjectService->getAllBySchool($schoolId)->pluck('name', 'id')->toArray();
        } else {
            $gradeOptions = $staff->staffGrades->pluck('grade')->toArray();
            $subjectOptions = array_intersect($staff->subjects->pluck('name', 'id')->toArray(), $regularGroup->subjects->pluck('name', 'id')->toArray());
        }

        if ($request->isMethod('post')) {
            $plan = $this->teacherPlanService->create($request->all());
            $this->teacherPlanService->addHistory($plan, Admin::user()->name." t???o m???i k??? ho???ch");
            return redirect()->route('school.staff.plan.index', ['school_id' => $schoolId, 'staffId' => $staffId, 'rgId' => $rgId])->with('success', '???? l??u k??? ho???ch');
        }

        $path = "";
        
        switch($school->school_type) {
            case 1:
                $path = '/public/templates/staff/plan/tieu_hoc.html';
                $view = 'admin.school.staff.plan.tieu_hoc'; break;
            case 2:
                $path = '/public/templates/staff/plan/thcs.html';
                $view = 'admin.school.staff.plan.thcs'; break;
            case 2:
                $path = '/public/templates/staff/plan/thpt.html';
                $view = 'admin.school.staff.plan.thpt'; break;
            case 6:
                $path = '/public/templates/staff/plan/mam_non.html';
                $view = 'admin.school.staff.plan.mam_non'; break;
            default:
                $path = '/public/templates/staff/plan/thpt.html';
                $view = 'admin.school.staff.plan.thpt'; break;
        }

        $lessonTemplate = file_get_contents(base_path($path), false);
        $monthYears = ListHelper::listMonth();

        return view($view, [    
            'school' => $school,
            'staff' => $staff,
            'regularGroup' => $regularGroup,
            'gradeOptions' => $gradeOptions,
            'subjectOptions' => $subjectOptions,
            'lessonTemplate' => $lessonTemplate ?? "",
            'canManage' => $this->teacherPlanService->checkIfCanMange($staffId),
            'monthYears' => $monthYears,
            'onCreate'=>true,
        ]);
    }

    public function edit(Request $request,$schoolId, $staffId, $rgId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $teacherPlan = $this->teacherPlanService->findById($planId);

        if ($request->isMethod('post')) {
            $this->teacherPlanService->update($planId, $request->all());
            $this->teacherPlanService->addHistory($teacherPlan, Admin::user()->name." Ch???nh s???a k??? ho???ch");
            return redirect()->back()->with('success', '???? l??u k??? ho???ch');
        }

        if(in_array( $school->school_type, [SCHOOL_MN, SCHOOL_TH])) {
            $gradeOptions = array_intersect($staff->staffGrades->pluck('grade')->toArray(), $regularGroup->groupGrades->pluck('grade')->toArray());
            $subjectOptions = $this->subjectService->getAllBySchool($schoolId)->pluck('name', 'id')->toArray();
        } else {
            $gradeOptions = $staff->staffGrades->pluck('grade')->toArray();
            $subjectOptions = array_intersect($staff->subjects->pluck('name', 'id')->toArray(), $regularGroup->subjects->pluck('name', 'id')->toArray());
        }

        switch($school->school_type) {
            case 1:
                $path = '/public/templates/staff/plan/tieu_hoc.html';
                $view = 'admin.school.staff.plan.tieu_hoc'; break;
            case 2:
                $path = '/public/templates/staff/plan/thcs.html';
                $view = 'admin.school.staff.plan.thcs'; break;
            case 2:
                $path = '/public/templates/staff/plan/thpt.html';
                $view = 'admin.school.staff.plan.thpt'; break;
            case 6:
                $path = '/public/templates/staff/plan/mam_non.html';
                $view = 'admin.school.staff.plan.mam_non'; break;
            default:
                $path = '/public/templates/staff/plan/thpt.html';
                $view = 'admin.school.staff.plan.thpt'; break;
        }

        $lessonTemplate = file_get_contents(base_path($path), false);
        $monthYears = ListHelper::listMonth();

        return view($view, [    
            'school' => $school,
            'staff' => $staff,
            'regularGroup' => $regularGroup,
            'teacherPlan' => $teacherPlan,
            'gradeOptions' => $gradeOptions,
            'subjectOptions' => $subjectOptions,
            'canManage' => $this->teacherPlanService->checkIfCanMange($staffId),
            'lessonTemplate' => $lessonTemplate ?? "",
            'monthYears' => $monthYears,
        ]);
    }

    public function lessonSubmit(Request $request,$schoolId, $staffId, $rgId,$lessonId){
        $staff = $this->staffService->findById($staffId);
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $leaderAccountId = [$regularGroup->leaderAccount()->id??null];
        $teacherLesson = TeacherLesson::find($lessonId);
        $objectType=null;
        $objectId=null;
        if(!$leaderAccountId[0]){
            return redirect()->back()->with('error', 'T??? chuy??n m??n ch??a c?? t??? tr?????ng');
        }
        DB::beginTransaction();
        try {
            $teacherLesson->update([
                'status' =>PLAN_SUBMITTED,
            ]);
            $objectType=TeacherLesson::class;
            $objectId=$lessonId;
            
            // T???o task v?? th??ng b??o cho t??? tr?????ng chuy??n m??n
            $this->taskService->create([
                "title" => "Duy???t k??? ho???ch b??i gi???ng c???a $staff->fullname",
                "description" => "Duy???t k??? ho???ch b??i gi???ng  c???a $staff->fullname "." bai hoc ".$teacherLesson->bai_hoc,
                "priority" => "high",
                "start_date" => date('d/m/Y', time()),
                "due_date" => null,
                "creator_id" => Admin::user()->id,
                "assignee_ids" => $leaderAccountId,//[$regularGroup->leaderAccount()->id ?? null],
                "object_type" => $objectType,
                "object_id" => $objectId
            ]);
            DB::commit();
        } catch (Exception $ex) {
            if(env('APP_ENV') !== 'production') dd($ex);
            DB::rollback();
            Log::error($ex->getMessage(), [
                'process' => '[create group plan]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
        
        $this->teacherLessonService->addHistory($teacherLesson,Admin::user()->name." g???i k??? ho???ch b??i gi???ng l??n t??? tr?????ng chuy??n m??n", $staffId);
        return redirect()->back()->with('success', '???? g???i k??? ho???ch b??i gi???ng t???i t??? tr?????ng chuy??n m??n');
    }

    public function lessonDeny(Request $request,$schoolId, $staffId, $rgId, $lessonId){
        $teacherLesson = TeacherLesson::find($lessonId);
        $owner = AdminUser::where('username', SchoolStaff::find($staffId)->staff_code)->first();
        $notifcationTitle = "K??? ho???ch #{$teacherLesson->id} ch??a ???????c duy???t";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "B??i gi???ng ch??a ???????c duy???t");
        return redirect()->back()->with('success', '???? th??ng b??o ch??a duy???t b??i gi???ng');
    }

    public function lessonApprove(Request $request,$schoolId, $staffId, $rgId, $lessonId){
        $teacherLesson = TeacherLesson::find($lessonId);
        $teacherLesson->update([
            'status' =>PLAN_APPROVED,
        ]);
        $this->teacherLessonService->addHistory($teacherLesson,Admin::user()->name." ???? duy???t B??i gi???ng", $staffId);
        $owner = AdminUser::where('username', SchoolStaff::find($staffId)->staff_code)->first();
        $notifcationTitle = "T??? tr?????ng ???? duy???t k??? ho???ch #{$teacherLesson->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "B??i gi???ng ???? ???????c duy???t");
        return redirect()->back()->with('success', '???? duy???t b??i gi???ng');
    }

    public function submit (Request $request,$schoolId, $staffId, $rgId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $teacherPlan = $this->teacherPlanService->findById($planId);
        $leaderAccountId = [$regularGroup->leaderAccount()->id??null];
        if(!$leaderAccountId[0]){
            return redirect()->back()->with('error', 'T??? chuy??n m??n ch??a c?? t??? tr?????ng');
        }
        DB::beginTransaction();
        try {
            $this->teacherPlanService->update($planId, [
                'status' => PLAN_SUBMITTED
            ]);
            
            // T???o task v?? th??ng b??o cho t??? tr?????ng chuy??n m??n
            $this->taskService->create([
                "title" => "Duy???t k??? ho???ch gi??o d???c c???a $staff->fullname",
                "description" => "Duy???t k??? ho???ch gi??o d???c c???a $staff->fullname ".GRADES[$teacherPlan->grade],
                "priority" => "high",
                "start_date" => date('d/m/Y', time()),
                "due_date" => null,
                "creator_id" => Admin::user()->id,
                "assignee_ids" => $leaderAccountId,//[$regularGroup->leaderAccount()->id ?? null],
                "object_type" => TeacherPlan::class,
                "object_id" => $teacherPlan->id
            ]);
            DB::commit();
        } catch (Exception $ex) {
            if(env('APP_ENV') !== 'production') dd($ex);
            DB::rollback();
            Log::error($ex->getMessage(), [
                'process' => '[create group plan]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
        
        $this->teacherPlanService->addHistory($teacherPlan, Admin::user()->name." g???i k??? ho???ch l??n t??? tr?????ng chuy??n m??n");
        return redirect()->back()->with('success', '???? g???i k??? ho???ch t???i t??? tr?????ng chuy??n m??n');
    }

    public function approve (Request $request,$schoolId, $staffId, $rgId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $teacherPlan = $this->teacherPlanService->findById($planId);
        
        $this->teacherPlanService->update($planId, [
            'status' => PLAN_APPROVED
        ]);

        $this->teacherPlanService->addHistory($teacherPlan, Admin::user()->name." ???? duy???t k??? ho???ch");
        $owner = AdminUser::where('username', $teacherPlan->staff->staff_code)->first();
        $notifcationTitle = "T??? tr?????ng ???? duy???t k??? ho???ch #{$teacherPlan->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "K??? ho???ch ???? ???????c duy???t");
        return redirect()->back()->with('success', '???? duy???t k??? ho???ch');
    }

    public function addReview(Request $request,$schoolId, $staffId, $rgId, $planId) {
        $plan = $this->teacherPlanService->findById($planId);
        $plan->update(['status' => PLAN_INREVIEW]);
        $this->teacherPlanService->addHistory($plan, Admin::user()->name." th??m nh???n x??t \r\n: {$request->notes}");
        $owner = AdminUser::where('username', $plan->staff->staff_code)->first();
        $notifcationTitle = "T??? tr?????ng ???? th??m nh???n x??t cho k??? ho???ch #{$plan->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, $request->notes);
        return redirect()->back()->with('success', '???? th??m nh???n x??t');
    }

    public function lessons(Request $request, $schoolId, $staffId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $teacherPlans = $this->teacherPlanService->findApprovedPlanByStaff($staffId);
       
        if(count($teacherPlans) == 0) {
            return redirect()->back()->with('error', "Gi??o vi??n {$staff->fullname} Hi???n t???i ch??a c?? k??? ho???ch n??o ???????c duy???t");
        }

        $selectedPlan = $request->planId ? $this->teacherPlanService->findById($request->planId) : $teacherPlans[0];
        
        $lessons = TeacherLesson::where('teacher_plan_id', $selectedPlan->id)->with(['histories', 'histories.createdBy'])->paginate(10);
        
        $planOwner = AdminUser::where('username', $selectedPlan->staff->staff_code)->first();
        switch($school->school_type) {
            case 1:
                $path = '/public/templates/staff/plan/tieu_hoc.html';
                $view = 'admin.school.staff.lesson.tieu_hoc'; break;
            case 2:
                $path = '/public/templates/staff/plan/thcs.html';
                $view = 'admin.school.staff.lesson.thcs'; break;
            case 2:
                $path = '/public/templates/staff/plan/thpt.html';
                $view = 'admin.school.staff.lesson.thpt'; break;
            case 6:
                $path = '/public/templates/staff/plan/mam_non.html';
                $view = 'admin.school.staff.lesson.mam_non'; break;
            default:
                $path = '/public/templates/staff/plan/thpt.html';
                $view = 'admin.school.staff.lesson.thpt'; break;
        }
        

        $lessonNames = [];
        foreach($selectedPlan->lessons as $lesson){
            array_push($lessonNames, $lesson->ten_bai_hoc);
        }
        $lessonTemplate = file_get_contents(base_path($path), false);
        $sampleLessons = LessonSample::where([
            'grade' => $selectedPlan->grade,
            'subject_id' => $selectedPlan->subject_id
        ])->where(function($query) use ($lessonNames){
            foreach($lessonNames as $lessonName){
                $query->orWhere('title', 'like', '%'.$lessonName.'%');
            }
        })->get();
        $groupLeader = $selectedPlan->regularGroup->leader()->first();
        return view($view, [    
            'school' => $school,
            'staff' => $staff,
            'teacherPlans' => $teacherPlans,
            'teacherPlan' => $selectedPlan,
            'planOwner' => $planOwner,
            'groupLeader'=>$groupLeader,
            'canManage' => $this->teacherPlanService->checkIfCanMange($staffId),
            'lessonTemplate' => $lessonTemplate ?? "",
            'sampleLessons' => $sampleLessons,
            'lessons' => $lessons,
            'monthYears' => ListHelper::listMonth(),
        ]);
    }

    public function addLessonReview(Request $request,$schoolId, $staffId, $lessonId) {
        $lesson = TeacherLesson::with(['plan', 'plan.staff'])->find($lessonId);
        $this->teacherPlanService->addLessonHistory($lesson, $request->notes ?? '');
        $owner = AdminUser::where('username', $lesson->plan->staff->staff_code)->first();
        $notifcationTitle = Admin::user()->name." ???? th??m nh???n x??t cho b??i gi???ng #{$lesson->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, $request->notes);
        return redirect()->back()->with('success', '???? th??m nh???n x??t');
    }

    public function editLesson(Request $request,$schoolId, $staffId, $lessonId) {
        $lesson = TeacherLesson::with(['plan', 'plan.staff'])->find($lessonId);
       /*  if($request->file) {
            $fileUpload = $this->fileUploadService->uploadImportFiles($request, 'ebooks', []);
            dd($fileUpload);
            $lesson->ppt = $fileUpload->getOriginalContent()['filepath'];
        } else dd(1); */
        $lesson->update([
            'content' => $request->content,
            //'ppt' => $request->ppt ,
            'video_tbs' => $request->video_tbs ,
            'game_simulator' => $request->game_simulator ,
            'diagram_simulator' => $request->diagram_simulator ,
            'homeworks' => $request->homeworks ,
            'advanced_exercise' => $request->advanced_exercise ,
            'test_question' => $request->test_question ,
            'game_content' => $request->game_content ,
        ]);
        return redirect()->back()->with('success', '???? c???p nh???t b??i gi???ng');
    }

    public function selectLessonSample(Request $request,$schoolId, $staffId, $lessonId) {
        $sampleLesson = LessonSample::with(['homesheet','exercise'])->find($request->sampleId);
        $teacherLesson = TeacherLesson::find($lessonId);
        $teacherLesson->update([
            'content' => $sampleLesson->content,
            'diagram_simulator'=>$sampleLesson->diagram_simulator,
            'game_simulator'=>$sampleLesson->game_simulator,
            'video_tbs'=>$sampleLesson->video_thiet_bi_so,
            'homeworks'=>$sampleLesson->homesheet->content??'',
            'advanced_exercise'=>$sampleLesson->exercise->content??'',
        ]);
        return redirect()->back()->with('success', '???? s??? d???ng m???u b??i gi???ng t??? h??? th???ng');
    }
    
    public function download(Request $request,$schoolId, $staffId, $rgId, $planId) {
        if (empty($planId) || empty($rgId) || empty($schoolId )) return redirect()->back()->with('error', 'Thi???u th??ng tin k??? ho???ch');
        $school = $this->schoolService->findById($schoolId);
        $teacherPlan = $this->teacherPlanService->findById($planId);
    
        return $this->teacherPlanService->download($school, $teacherPlan);
    }

    public function delete($schoolId, $staffId, $rgId, $planId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                TeacherPlan::destroy($planId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => '???? c?? l???i']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}
