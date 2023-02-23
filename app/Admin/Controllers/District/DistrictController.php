<?php

namespace App\Admin\Controllers\District;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Admin\Permission;
use App\Admin\Services\RegularGroupPlanService;
use App\Admin\Services\SchoolPlanService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\TeacherPlanService;
use App\Admin\Services\SubjectService;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\RegularGroupPlan;
use App\Models\School;
use App\Models\SchoolStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\HeadingRowImport;
use App\Admin\Helpers\Utils;


class DistrictController extends Controller
{
    protected $schoolService;
    protected $schoolPlanService;
    protected $subjectService;
    protected $taskService;
    protected $rgPlanService;
    protected $teacherPlanService;
    
    public function __construct(
        SchoolService $schoolService,
        SchoolPlanService $schoolPlanService,
        RegularGroupPlanService $rgPlanService,
        TeacherPlanService $teacherPlanService,
        SubjectService $subjectService
    ) {
        $this->schoolService = $schoolService;
        $this->schoolPlanService = $schoolPlanService;
        $this->rgPlanService = $rgPlanService;
        $this->teacherPlanService = $teacherPlanService;
        $this->subjectService = $subjectService;
    }

    public function getList()
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support', ROLE_PHONG_GD, ROLE_CV_PHONG])) {
            return Permission::error();
        }

        if (Admin::user()->inRoles([ROLE_PHONG_GD, ROLE_CV_PHONG])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                return \redirect()->route('district.manage', ['id' => $district->id]);
            } else {
                return Permission::error();
            }
        }

        $province = request()->query('province', null);
        $provinces = Admin::user()->accessProvinces()->get();
        $queryDistricts = Admin::user()->accessDistricts();

        if ($province) $queryDistricts = $queryDistricts->where('province_id', $province);
        $districts = $queryDistricts->get();
        return view('admin.district.district_list', [
            'districts' => $districts,
            'provinces' => $provinces,
            'province' => $province,
        ]);
    }

    public function manage($id)
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support', ROLE_PHONG_GD, ROLE_CV_PHONG])) {
            return Permission::error();
        }

        if (Admin::user()->inRoles([ROLE_PHONG_GD, ROLE_CV_PHONG])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if($district->id != $id) return Permission::error();
            } else {
                return Permission::error();
            }
        }

        $district = District::with('province')->find($id);
        $schoolCount = School::where('district_id', $id)->count();
        $schoolIds = School::where('district_id', $id)->pluck('id')->toArray();
        $staffCount = SchoolStaff::whereIn('school_id',$schoolIds)->count();
        
        return $this->renderView('admin.district.manage', [
            'district' => $district,
            'schoolCount' => $schoolCount,
            'staffCount' => $staffCount,
            
        ]);
    }

    public function schoolList($id)
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support'])) {
            return Permission::error();
        }
        $district = District::find($id);
        $schools = School::where('district_id', $id)->whereNotIn('school_type', [3, 5])->get();
        return $this->renderView('admin.district.school_list', [
            'schools' => $schools,
            'district' => $district
        ]);
    }

    public function users($id)
    {
        $user = Admin::user();
        if (!$user->inRoles(['administrator', 'view.all', 'so-gd', 'phong-gd']  )) {
            return Permission::error();
        }

        $district = District::where('id', $id)->with(['users.roles', 'province'])->first();

        if (Admin::user()->inRoles([ROLE_PHONG_GD, ROLE_CV_PHONG])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if($district->id != $id) return Permission::error();
                
                
                
            } else {
                return Permission::error();
            }
        }

        $users = $district->users;
        return view('admin.agency.pgd_account', [
            'district' => $district,
            'users' => $users
        ]);
        
    }

    public function reviewSchoolPlans(Request $request, $districtId) {
        $district = District::find($districtId);
        $pendingPlans = $this->schoolPlanService->findPendingPlanByDistrict($districtId);
        return view('admin.district.school_plan.pending', [
            'plans' => $pendingPlans,
            'district' => $district
        ]);
    }

    public function addReviewSchoolPlan(Request $request, $districtId, $planId) {
        $district = District::find($districtId);
        $plan = $this->schoolPlanService->findById($planId);
        $plan->update(['status' => PLAN_INREVIEW]);
        $this->schoolPlanService->addHistory($plan, Admin::user()->name." thêm nhận xét \r\n: {$request->notes}");

        $owner = AdminUser::where('username', SchoolStaff::where([
            'school_id' => $plan->school_id,
            'position' => 1
        ])->first()->staff_code)->first();
        $notifcationTitle = "Hiệu trưởng đã thêm nhận xét cho kế hoạch #{$plan->id}";
        $this->schoolPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, $request->notes);

        return redirect()->back()->with('success', 'Đã nhận xét kế hoạch');
    }

    public function approveSchoolPlan(Request $request, $districtId, $planId) {
        $district = District::find($districtId);
        $plan = $this->schoolPlanService->findById($planId);
        $plan->update(['status' => PLAN_APPROVED]);

        $this->schoolPlanService->addHistory($plan, Admin::user()->name." Đã duyệt kế hoạch");

        $owner = AdminUser::where('username', SchoolStaff::where([
            'school_id' => $plan->school_id,
            'position' => 1
        ])->first()->staff_code)->first();
        $notifcationTitle = "Hiệu trưởng đã thêm nhận xét cho kế hoạch #{$plan->id}";
        $this->schoolPlanService->sendNotificationToPlanOwner($owner,$notifcationTitle, "Đã duyệt kế hoạch");

        return redirect()->back()->with('success', 'Đã duyệt kế hoạch');
    }

    public function schoolPlans(Request $request, $districtId) {
        $params = request()->query();
        $district = District::find($districtId);
        $plans = $this->schoolPlanService->findApprovedPlanByDistrict($districtId, $params);
        $districtSchools =$this->schoolService->takeSchoolByDistrictWithCondition($districtId, $params);
        return view('admin.district.school_plan.approved', [
            'plans' => $plans,
            'district' => $district,
            'levelFilter' => $params['level']??null,
            'schoolFilter'=>$params['school']??null,
            'districtSchools' =>$districtSchools,
        ]);
    }
    public function groupPlans(Request $request, $districtId) {
        $params = request()->query();
        $district = District::find($districtId);
        $plans = $this->rgPlanService->findApprovedPlanByDistrict($districtId, $params);
        $districtSchools =$this->schoolService->takeSchoolByDistrictWithCondition($districtId, $params);
        $subjectGrades = $this->subjectService->getSubjectByGrades(Utils::takeSchoolGradesByLevel($params['level']??null));
        return view('admin.district.group_plan.approved', [
            'plans' => $plans,
            'district' => $district,
            'levelFilter' => $params['level']??null,
            'schoolFilter'=>$params['school']??null,
            'subjectFilter'=>$params['subject']??null,
            'districtSchools' =>$districtSchools,
            'subjectGrades' =>$subjectGrades,
        ]);
    }

    public function teacherPlans(Request $request, $districtId) {
        $params = request()->query();
        $district = District::find($districtId);
        $plans = $this->teacherPlanService->findApprovedPlanByDistrict($districtId,$params);
        $districtSchools =$this->schoolService->takeSchoolByDistrictWithCondition($districtId, $params);
        $subjectGrades = $this->subjectService->getSubjectByGrades(Utils::takeSchoolGradesByLevel($params['level']??null));
        return view('admin.district.teacher_plan.approved', [
            'plans' => $plans,
            'district' => $district,
            'levelFilter' => $params['level']??null,
            'schoolFilter'=>$params['school']??null,
            'subjectFilter'=>$params['subject']??null,
            'districtSchools' =>$districtSchools,
            'subjectGrades' =>$subjectGrades,
        ]);
    }
}