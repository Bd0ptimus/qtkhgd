<?php

namespace App\Admin\Controllers\School;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Admin\Repositories\RegularGroupPlanRepository;
use App\Admin\Services\CommonService;
use App\Admin\Services\RegularGroupPlanService;
use App\Models\RegularGroup;
use App\Models\Subject;
use App\Admin\Services\RegularGroupService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Models\RegularGroupPlan;
use App\Models\School;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RegularGroupPlanController extends Controller
{

    protected $rgService;
    protected $commonService;
    protected $subjectService;
    protected $schoolService;
    protected $rgPlanService;
    protected $taskService;

    public function __construct(
        RegularGroupService $service, 
        CommonService $commonService, 
        SubjectService $subjectService,
        SchoolService $schoolSerice,
        RegularGroupPlanService $rgPlanService,
        TaskService $taskService
    )
    {
        $this->rgService = $service;
        $this->commonService = $commonService;
        $this->subjectService = $subjectService;
        $this->schoolService = $schoolSerice;
        $this->rgPlanService = $rgPlanService;
        $this->taskService = $taskService;
    }

    public function index(Request $request, $schoolId, $rgId) {
        $data['school'] = $this->schoolService->findById($schoolId);
        $date['schoolId'] = $schoolId; $data['rgId'] = $rgId;
        $data['regularGroup'] = $this->rgService->findByGroupId($rgId);
        $data['canManage'] = $this->rgService->checkIfCanMange($rgId);
        $data['staffId'] = Admin::user()->staffDetail ? Admin::user()->staffDetail->id : null;
        $data['staffGroups'] = $data['staffId'] ? $this->rgService->allByStaff($data['staffId']) : [];
        return view('admin.school.regular_group.plan.index', $data);
    }

    public function create(Request $request, $schoolId, $rgId) {
        $school = $this->schoolService->findById($schoolId);
        $regularGroup = $this->rgService->findByGroupId($rgId);

        if ($request->isMethod('post')) {
            $plan = $this->rgPlanService->create($school, $request->all());
            $this->rgPlanService->addHistory($plan, Admin::user()->name." t???o m???i k??? ho???ch");
            return redirect()->route('school.regular_group.plan.index', ['id' => $schoolId, 'rgId' => $rgId])->with('success', '???? l??u k??? ho???ch');
        }

        $content = "";
        $subjects = [];
        switch($school->school_type) {
            case 1:
                $view = 'admin.school.regular_group.plan.full_form'; 
                $subjects = $this->subjectService->getSubjectBySchoolAndGrades($schoolId, $regularGroup->groupGrades->pluck('grade')->toArray());
                break;
            case 6:
                $view = 'admin.school.regular_group.plan.table_form';
                break;
            default:
                $path = url('/templates/plan/group_plan_content.html');
                $content = file_get_contents($path); 
                $view = 'admin.school.regular_group.plan.content_form'; break;
        }

        return view($view, [    
            'school' => $school,
            'regularGroup' => $regularGroup,
            'subjects' => $subjects,
            'content' => $content
        ]);
    }

    public function edit(Request $request, $schoolId, $rgId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        $regularGroup = $this->rgService->findByGroupId($rgId);

        if ($request->isMethod('post')) {
            DB::beginTransaction();
            try{
                $plan = $this->rgPlanService->update($school, $planId, $request->all());
                $this->rgPlanService->addHistory($plan, Admin::user()->name." c???p nh???t k??? ho???ch");
                DB::commit(); 
                //return redirect()->route('school.regular_group.plan.index', ['id' => $schoolId, 'rgId' => $rgId])->with('success', '???? l??u k??? ho???ch');
                return redirect()->back()->with('success', '???? l??u k??? ho???ch');
            } catch (Exception $ex) {
                if(env('APP_ENV') !== 'production') dd($ex);

                DB::rollBack();

                Log::error($ex->getMessage(), [
                    'process' => '[create group plan]',
                    'function' => __function__,
                    'file' => basename(__FILE__),
                    'line' => __line__,
                    'path' => __file__,
                    'error_message' => $ex->getMessage()
                ]);

                return redirect()->back()->with('error', $ex->getMessage());
            }
            
        }
        
        $groupPlan = $this->rgPlanService->findById($planId);

        /* foreach($groupPlan->subjectPlans as $item) {
            if($item->subject_id == 8) {
                dd(json_decode($item->content));
            }
        } */
        
        $subjects = $this->subjectService->getSubjectBySchoolAndGrades($schoolId, $regularGroup->groupGrades->pluck('grade')->toArray());
        switch($school->school_type) {
            case 1:
                $view = 'admin.school.regular_group.plan.full_form'; break;
            case 6:
                $view = 'admin.school.regular_group.plan.table_form';
                $groupPlan->content = json_decode($groupPlan->content);
                break;
            default:
                $view = 'admin.school.regular_group.plan.content_form'; break;
        }

        return view($view, [    
            'school' => $school,
            'regularGroup' => $regularGroup,
            'content' => '',
            'groupPlan' => $groupPlan,
            'subjects' => $subjects,
            'canManage' => $this->rgService->checkIfCanMange($rgId)
        ]);
    }

    public function submit(Request $request, $schoolId, $rgId, $planId) {
        $school = $this->schoolService->findById($schoolId);
        $regularGroup = $this->rgService->findByGroupId($rgId);
        DB::beginTransaction();
        try {
            $plan = $this->rgPlanService->update($school, $planId, [
                'status' => PLAN_SUBMITTED
            ]);
            $this->taskService->create([
                "title" => "Duy???t k??? ho???ch gi??o d???c c???a $regularGroup->name",
                "description" => "Duy???t k??? ho???ch gi??o d???c c???a $regularGroup->name",
                "priority" => "high",
                "start_date" => date('d/m/Y', time()),
                "due_date" => null,
                "creator_id" => Admin::user()->id,
                "assignee_ids" => [$school->accountHieuTruong()->id ?? null],
                "object_type" => RegularGroupPlan::class,
                "object_id" => $regularGroup->id
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
        

        $this->rgPlanService->addHistory($plan, Admin::user()->name." G???i k??? ho???ch cho hi???u tr?????ng");
        return redirect()->back()->with('success', '???? g???i hi???u tr?????ng duy???t');
    }

    public function delete($schoolId, $rgId, $planId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                RegularGroupPlan::destroy($planId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => '???? c?? l???i']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function download($schoolId,$rgId, $planId) {
        return $this->rgPlanService->download($planId, storage_path("app/public/export_{$planId}.docx"));
    }

    public function addReview(Request $request,$schoolId,$rgId, $planId) {
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $plan = $this->rgPlanService->findById($planId);
        $plan->update(['status' => PLAN_INREVIEW]);
        $this->rgPlanService->addHistory($plan, Admin::user()->name." th??m nh???n x??t \r\n: {$request->notes}");

        $owner = AdminUser::where('username', $regularGroup->leader->staff->staff_code)->first();
        $notifcationTitle = "Hi???u tr?????ng ???? th??m nh???n x??t cho k??? ho???ch #{$plan->id}";
        $this->rgPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, $request->notes);

        return redirect()->back()->with('success', '???? th??m nh???n x??t');
    }

    public function approve($schoolId,$rgId, $planId) {
        $regularGroup = $this->rgService->findByGroupId($rgId);
        $school = $this->schoolService->findById($schoolId);
        $plan = $this->rgPlanService->findById($planId);
        $this->rgPlanService->update($school, $planId, [
            'status' => PLAN_APPROVED
        ]);

        $this->rgPlanService->addHistory($plan, Admin::user()->name." ???? duy???t k??? ho???ch");

        $owner = AdminUser::where('username', $regularGroup->leader->staff->staff_code)->first();
        $notifcationTitle = "Hi???u tr?????ng ???? duy???t k??? ho???ch #{$plan->id}";
        $this->rgPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "???? duy???t k??? ho???ch");

        return redirect()->back()->with('success', '???? duy???t k??? ho???ch');
    }
}
