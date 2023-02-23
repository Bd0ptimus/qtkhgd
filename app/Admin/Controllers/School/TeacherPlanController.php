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
            $this->teacherPlanService->addHistory($plan, Admin::user()->name." tạo mới kế hoạch");
            return redirect()->route('school.staff.plan.index', ['school_id' => $schoolId, 'staffId' => $staffId, 'rgId' => $rgId])->with('success', 'Đã lưu kế hoạch');
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
            $this->teacherPlanService->addHistory($teacherPlan, Admin::user()->name." Chỉnh sửa kế hoạch");
            return redirect()->back()->with('success', 'Đã lưu kế hoạch');
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
            return redirect()->back()->with('error', 'Tổ chuyên môn chưa có tổ trưởng');
        }
        DB::beginTransaction();
        try {
            $teacherLesson->update([
                'status' =>PLAN_SUBMITTED,
            ]);
            $objectType=TeacherLesson::class;
            $objectId=$lessonId;
            
            // Tạo task và thông báo cho tổ trưởng chuyên môn
            $this->taskService->create([
                "title" => "Duyệt kế hoạch bài giảng của $staff->fullname",
                "description" => "Duyệt kế hoạch bài giảng  của $staff->fullname "." bai hoc ".$teacherLesson->bai_hoc,
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
        
        $this->teacherLessonService->addHistory($teacherLesson,Admin::user()->name." gửi kế hoạch bài giảng lên tổ trưởng chuyên môn", $staffId);
        return redirect()->back()->with('success', 'Đã gửi kế hoạch bài giảng tới tổ trưởng chuyên môn');
    }

    public function lessonDeny(Request $request,$schoolId, $staffId, $rgId, $lessonId){
        $teacherLesson = TeacherLesson::find($lessonId);
        $owner = AdminUser::where('username', SchoolStaff::find($staffId)->staff_code)->first();
        $notifcationTitle = "Kế hoạch #{$teacherLesson->id} chưa được duyệt";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "Bài giảng chưa được duyệt");
        return redirect()->back()->with('success', 'Đã thông báo chưa duyệt bài giảng');
    }

    public function lessonApprove(Request $request,$schoolId, $staffId, $rgId, $lessonId){
        $teacherLesson = TeacherLesson::find($lessonId);
        $teacherLesson->update([
            'status' =>PLAN_APPROVED,
        ]);
        $this->teacherLessonService->addHistory($teacherLesson,Admin::user()->name." Đã duyệt Bài giảng", $staffId);
        $owner = AdminUser::where('username', SchoolStaff::find($staffId)->staff_code)->first();
        $notifcationTitle = "Tổ trưởng đã duyệt kế hoạch #{$teacherLesson->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "Bài giảng đã được duyệt");
        return redirect()->back()->with('success', 'Đã duyệt bài giảng');
    }

    public function submit (Request $request,$schoolId, $staffId, $rgId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $teacherPlan = $this->teacherPlanService->findById($planId);
        $leaderAccountId = [$regularGroup->leaderAccount()->id??null];
        if(!$leaderAccountId[0]){
            return redirect()->back()->with('error', 'Tổ chuyên môn chưa có tổ trưởng');
        }
        DB::beginTransaction();
        try {
            $this->teacherPlanService->update($planId, [
                'status' => PLAN_SUBMITTED
            ]);
            
            // Tạo task và thông báo cho tổ trưởng chuyên môn
            $this->taskService->create([
                "title" => "Duyệt kế hoạch giáo dục của $staff->fullname",
                "description" => "Duyệt kế hoạch giáo dục của $staff->fullname ".GRADES[$teacherPlan->grade],
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
        
        $this->teacherPlanService->addHistory($teacherPlan, Admin::user()->name." gửi kế hoạch lên tổ trưởng chuyên môn");
        return redirect()->back()->with('success', 'Đã gửi kế hoạch tới tổ trưởng chuyên môn');
    }

    public function approve (Request $request,$schoolId, $staffId, $rgId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $teacherPlan = $this->teacherPlanService->findById($planId);
        
        $this->teacherPlanService->update($planId, [
            'status' => PLAN_APPROVED
        ]);

        $this->teacherPlanService->addHistory($teacherPlan, Admin::user()->name." Đã duyệt kế hoạch");
        $owner = AdminUser::where('username', $teacherPlan->staff->staff_code)->first();
        $notifcationTitle = "Tổ trưởng đã duyệt kế hoạch #{$teacherPlan->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "Kế hoạch đã được duyệt");
        return redirect()->back()->with('success', 'Đã duyệt kế hoạch');
    }

    public function addReview(Request $request,$schoolId, $staffId, $rgId, $planId) {
        $plan = $this->teacherPlanService->findById($planId);
        $plan->update(['status' => PLAN_INREVIEW]);
        $this->teacherPlanService->addHistory($plan, Admin::user()->name." thêm nhận xét \r\n: {$request->notes}");
        $owner = AdminUser::where('username', $plan->staff->staff_code)->first();
        $notifcationTitle = "Tổ trưởng đã thêm nhận xét cho kế hoạch #{$plan->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, $request->notes);
        return redirect()->back()->with('success', 'Đã thêm nhận xét');
    }

    public function lessons(Request $request, $schoolId, $staffId) {
        $school = $this->schoolService->findById($schoolId);
        $staff = $this->staffService->findById($staffId);
        $teacherPlans = $this->teacherPlanService->findApprovedPlanByStaff($staffId);
       
        if(count($teacherPlans) == 0) {
            return redirect()->back()->with('error', "Giáo viên {$staff->fullname} Hiện tại chưa có kế hoạch nào được duyệt");
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
        $notifcationTitle = Admin::user()->name." đã thêm nhận xét cho bài giảng #{$lesson->id}";
        $this->teacherPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, $request->notes);
        return redirect()->back()->with('success', 'Đã thêm nhận xét');
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
        return redirect()->back()->with('success', 'Đã cập nhật bài giảng');
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
        return redirect()->back()->with('success', 'Đã sử dụng mẫu bài giảng từ hệ thống');
    }
    
    public function download(Request $request,$schoolId, $staffId, $rgId, $planId) {
        if (empty($planId) || empty($rgId) || empty($schoolId )) return redirect()->back()->with('error', 'Thiếu thông tin kế hoạch');
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
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

}
