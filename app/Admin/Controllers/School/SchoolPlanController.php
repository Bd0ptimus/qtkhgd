<?php

namespace App\Admin\Controllers\School;

use App\Admin\Admin;
use App\Admin\Services\ImportWordService;
use App\Admin\Services\SchoolPlanService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Models\SchoolPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SchoolPlanController extends Controller
{
    protected $schoolService;
    protected $schoolPlanService;
    protected $importWordService;
    protected $subjectService;
    protected $taskService;
    
    public function __construct(
        SchoolService $schoolService,
        SchoolPlanService $schoolPlanService,
        ImportWordService $importWordService,
        SubjectService $subjectService,
        TaskService $taskService
    ) {
        $this->schoolService = $schoolService;
        $this->schoolPlanService = $schoolPlanService;
        $this->importWordService = $importWordService;
        $this->subjectService = $subjectService;
        $this->taskService = $taskService;
    }

    public function index($schoolId) {
        return view('admin.school.school_plan.index', [
            'school' => $this->schoolService->findById($schoolId),
            'schoolPlans' => $this->schoolPlanService->get($schoolId)
        ]);
    }

    public function create(Request $request, $schoolId) {
        if ($request->isMethod('post')) {
            $schoolPlan = $this->schoolPlanService->create($request->all());
            return redirect()->route('school.school_plan.edit', ['id' => $schoolId, 'planId' => $schoolPlan->id ])->with('success', 'Đã lưu kế hoạch');
        }

        $defaultValues = [
            'khung_thoi_gian_44' => "Thực hiện Quyết định số ......../QĐ-UBND ngày ...../...../20.... của chủ tịch UBND tỉnh .................. về kế hoạch thời gian năm học..........<br/>cụ thể đối với giáo dục tiểu học:<br/>Ngày tựu trường: Thứ ......, ngày ....../...../20.....<br/>Ngày khai giảng: ngày ..../..../20.....<br/>Học kì I: ....... Tuần Từ ngày ...../...../20.... đến trước ngày ...../...../20....<br/>Học kì II: ....... Tuần Từ ngày ...../...../20.... đến trước ngày ...../...../20....<br/>Ngày bế giảng năm học: Từ ngày ...../...../20....",
            'thoi_gian_to_chuc_theo_tuan' => file_get_contents(base_path('public/templates/plan/thoi_gian_to_chuc_theo_tuan.html')),
        ];
        $school = $this->schoolService->findById($schoolId);
        $view = $school->school_type == 1 ? 'admin.school.school_plan.full_form' : 'admin.school.school_plan.upload_form';
        return view($view, [
            'school' => $school,
            'defaultValues' => $defaultValues,
            'subjects' => $this->subjectService->getAllBySchool($schoolId),
            'create' => true,
        ]);
    }

    public function upload(Request $request, $schoolId) {
        // $request->validate([
        //     'file'=> 'required|mimes:docx,doc|max:5120',
        // ], [
        //     'file.required' => trans('validation.required'),
        //     'file.mimes' => trans('validation.mimes'),
        //     'file.max' => trans('validation.max'),
        // ]);
        $content = $this->importWordService->getFileContent($request->file('file'), storage_path("app/public/import_school_plan_{$schoolId}.html"));

        $schoolPlan = SchoolPlan::create([
            'school_id' => $schoolId,
            'content' => $content
        ]);

        return redirect()->route('school.school_plan.edit', ['id' => $schoolId, 'planId' => $schoolPlan->id ])->with('success', 'Đã lưu kế hoạch');
    }

    public function edit(Request $request, $schoolId, $planId) {
        $data['schoolPlan'] = $this->schoolPlanService->findById($planId);
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['subjects'] = $this->subjectService->getAllBySchool($schoolId);
        $data['defaultValues'] = [
            'khung_thoi_gian_44' => "Thực hiện Quyết định số ......../QĐ-UBND ngày ...../...../20.... của chủ tịch UBND tỉnh .................. về kế hoạch thời gian năm học..........<br/>cụ thể đối với giáo dục tiểu học:<br/>Ngày tựu trường: Thứ ......, ngày ....../...../20.....<br/>Ngày khai giảng: ngày ..../..../20.....<br/>Học kì I: ....... Tuần Từ ngày ...../...../20.... đến trước ngày ...../...../20....<br/>Học kì II: ....... Tuần Từ ngày ...../...../20.... đến trước ngày ...../...../20....<br/>Ngày bế giảng năm học: Từ ngày ...../...../20....",
            'thoi_gian_to_chuc_theo_tuan' => file_get_contents(base_path('public/templates/plan/thoi_gian_to_chuc_theo_tuan.html')),
        ];
        if ($request->isMethod('post')) {
            $this->schoolPlanService->update($planId, $request->all());
            return redirect()->route('school.school_plan.edit', ['id' => $schoolId, 'planId' => $planId ])->with('success', 'Đã lưu kế hoạch');
        }
        $view = $data['school']->school_type == 1 && empty($data['schoolPlan']->content) ? 'admin.school.school_plan.full_form' : 'admin.school.school_plan.content_form';

        return view($view, $data);
    }

    public function view($schoolId, $timetableId) {
        $data = $this->timetableService->view($timetableId);
        $data['school'] = $this->schoolService->findById($schoolId);
        return view('admin.school.timetable.view', $data);
    }

    public function delete($schoolId, $planId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                SchoolPlan::destroy($planId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function submit($schoolId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        DB::beginTransaction();
        try{ 
            $this->schoolPlanService->update($planId, [
                'status' => PLAN_SUBMITTED
            ]);
            $this->taskService->create([
                "title" => "Duyệt kế hoạch giáo dục của trường $school->school_name",
                "description" => "Duyệt kế hoạch giáo dục của trường $school->school_name",
                "priority" => "high",
                "start_date" => date('d/m/Y', time()),
                "due_date" => null,
                "creator_id" => Admin::user()->id,
                "assignee_ids" => [$school->accountTruongPhong()->id ?? null],
                "object_type" => SchoolPlan::class,
                "object_id" => $planId
            ]);
            DB::commit();
        } catch(Exception $ex) {
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

        return redirect()->back()->with('success', 'Đã gửi kế hoạch tới phòng giáo dục');
    }

    public function download($schoolId, $planId) {
        return $this->schoolPlanService->download($planId,  storage_path("app/public/export_{$planId}.docx"));
    }
    
}
