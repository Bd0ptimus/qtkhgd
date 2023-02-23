<?php

namespace App\Admin\Controllers\School;

use App\Admin\Admin;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use App\Admin\Models\Exports\ExportSchoolList;
use App\Admin\Models\Exports\ExportSchoolUsers;
use App\Admin\Permission;
use App\Admin\Services\RegularGroupPlanService;
use App\Admin\Services\RegularGroupService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\StaffService;
use App\Http\Controllers\Controller;
use App\Models\District;

use App\Models\Province;
use App\Models\RegularGroupPlan;
use App\Models\School;
use App\Models\SchoolBranch;
use App\Models\SchoolClass;
use App\Models\SchoolStaff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class SchoolController extends Controller
{
    protected $schoolService;
    protected $rgService;
    protected $rgPlanService;
    protected $staffService;

    public function __construct(
        SchoolService $schoolService,
        RegularGroupService $rgService,
        RegularGroupPlanService $rgPlanService,
        StaffService $staffService
    ) {
        $this->schoolService = $schoolService;
        $this->rgService = $rgService;
        $this->rgPlanService = $rgPlanService;
        $this->staffService = $staffService;
    }

    public function index()
    {
      
        if (Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_GIAO_VIEN, ROLE_SCHOOL_MANAGER])) {
            if (count(Admin::user()->schools) > 0) {
                $school = Admin::user()->schools[0];
                return \redirect()->route('admin.school.manage', ['id' => $school->id]);
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('school.index', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if (Admin::user()->inRoles(['so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                if ($provinceId != $province->id) {
                    return redirect()->route('school.index', ['provinceId' => $province->id]);
                }
                $provinceId = $province->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin.');
            }
        }

        if(empty($provinceId) && Admin::user()->isAdministrator()) {
            $provinceId = Province::where('id', '>', 0)->first()->id;
        }

        $provinceIdDefault = Province::where('id', '>', 0)->first()->id;

        $districts = [];
        $query = School::where('id', '>', 0);
        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
            if ($districtId == null) {
                $query = $query->whereIn('district_id', $districts->pluck('id'));
            }
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        if (Admin::user()->inRoles(['phong-gd'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        $schools = $query->with(['ward', 'branches', 'district', 'district.province', 'classes','staffs'])->get();

        $provinces = Admin::user()->inRoles(['administrator', 'customer-support']) ? Province::all() : (count($districts) > 0 ? [$districts[0]->province] : []);
        return $this->renderView('admin.school.index', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'provinceIdDefault' => $provinceIdDefault,
        ]);
    }

    public function manage($id)
    {
        if (Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_GIAO_VIEN, ROLE_SCHOOL_MANAGER])) {
            if (count(Admin::user()->schools) > 0) {
                $school = Admin::user()->schools[0];
                if ($school->id != $id) return Permission::error();
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin.');
            }
        }

        $school = School::where('id', $id)->with(['staffs', 'classes', 'branches', 'users', 'users.roles'])->first();
        $staffGroups = Admin::user()->staffDetail ? $this->rgService->allByStaff(Admin::user()->staffDetail->id) : [];
        $view = Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'admin.school.teacher_manage' :'admin.school.manage';
        $staff = Admin::user()->user_detail ? $this->staffService->findById(Admin::user()->user_detail) : null;
        return $this->renderView($view, [
            'school' => $school,
            'staffGroups' => $staffGroups,
            'staff' => $staff
        ]);
    }

    public function users($id)
    {   
        $role = Admin::user()->is_demo_account == 1 && Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_SCHOOL_MANAGER]);
        $school = School::where('id', $id)->with(['users', 'teachers', 'users.roles', 'classes', 'classes.teachers'])->first();
        return $this->renderView('admin.school.users', [
            'school' => $school,
            "role" => $role
        ]);
    }

    public function parentAccounts($school_id, Request $request)
    {
        $school = School::where('id', $school_id)->with('branches')->first();
        if (empty($request->school_branch)) return redirect()->route('admin.school.parent_accounts', ['id' => $school_id, 'school_branch' => $school->branches[0]->id]);
        $class_id = request()->query('class', null);
        if (!$class_id) {
            $class_id = $school->branches[0]->classes[0]->id;
        }
        list($classes, $request_class) = SchoolClass::getClasses($class_id);

        $selectedBranch = SchoolBranch::where([
            'school_id' => $school_id,
            'id' => $request->school_branch
        ])->with(['classes', 'students.parent_accounts', 'students.parent_accounts.roles', 'students.class', 'students' => function ($query) use ($class_id) {
            if (!empty($class_id)) {
                $query->where('class_id', $class_id);
            }
        }])->first();

        if (!$classes) {
            $classes = $selectedBranch->classes;
        }

        return $this->renderView('admin.school.parent_accounts', [
            'school' => $school,
            'selectedBranch' => $selectedBranch,
            'class_id' => $class_id,
            'classes' => $classes,
        ]);
    }

    public function assignTeacherToClass($id, Request $request)
    {
        $data = $request->only(['teacher_id', 'class_id']);

        /** @var $teacher SchoolStaff */
        $teacher = SchoolStaff::find($data['teacher_id']);
        if (is_null($teacher)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        $teacher->assignToClass($data['class_id']);

        return redirect()->route('admin.school.users', ['id' => $id])->with('success', 'Thêm giáo viên thành công!');
    }

    //Create user School manager and TTYT 
    public function createSchoolUser($school_id, $role_id)
    {
        $userId = Admin::user()->id;
        if ($role_id == ROLE_SCHOOL_MANAGER_ID
            && !in_array($userId, [ROLE_ADMIN_ID, ROLE_CM_ID, ROLE_HIEU_TRUONG_ID, ROLE_SCHOOL_MANAGER_ID])) {
            return redirect()->route('admin.school.users', [
                'id' => $school_id
            ])->with('error', 'Không có quyền tạo tài khoản này!');
        }

        $user_first_name = '';

        switch ($role_id) {
            case ROLE_SCHOOL_MANAGER_ID:
                $user_first_name = 'Quản lý - ';
                break;
            default:
                $user_first_name = null;
                break;
        }
        $role = AdminRole::find($role_id);
        $school = School::find($school_id);
        $accountPrefix = strtoupper($school->school_code . School::ROLE_PREFIX[$role_id]);
        $currentExist = AdminUser::where('username', 'like', $accountPrefix . '%')->count();
        $dataInsert = [
            'username' => $accountPrefix . ($currentExist + 1),
            'password' => bcrypt(\Config::get('constants.password_reset')),
            'name' => $user_first_name . $school['school_name'],
            'avatar' => null,
            'created_by' => Admin::user()->id,
            'phone_number' => null,
            'force_change_pass' => 1
        ];
        DB::beginTransaction();
        try {
            /** @var $school School */
            AdminUser::createAcount($dataInsert, $role->slug, $school->id);
            DB::commit();

            return redirect()->route('admin.school.users', [
                'id' => $school->id
            ])->with('success', 'Thêm tài khoản trường thành công!');
        } catch (Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
    }

    public function deleteSchool()
    {
        if (!Auth::guard('admin')->user()->canDeleteSchool()) {
            return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền xóa học sinh!']);
        }
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            DB::beginTransaction();
            try {
                $id = request('id');
                $school = School::find($id);
                if (empty($school)) {
                    return response()->json(['error' => 1, 'msg' => 'Dữ liệu không hợp lệ!']);
                }
                $students = $school->students;
                $staffs = $school->staffs;
                $classes = $school->classes;
                $branches = $school->branches;
                if (!empty($students)) {
                    foreach ($students as $student) {
                        $student->delete();
                    }
                }
                if (!empty($staffs)) {
                    foreach ($staffs as $staff) {
                        $staff->delete();
                    }
                }
                if (!empty($classes)) {
                    foreach ($classes as $class) {
                        $class->delete();
                    }
                }
                if (!empty($branches)) {
                    foreach ($branches as $branch) {
                        // Todo: delete Epidemic relationship with branch
                        $branch->delete();
                    }
                }

                School::destroy($id);
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
                if(env('APP_ENV') !== 'production') dd($ex);
                return response()->json(['error' => 1, 'msg' => 'Có lỗi xảy ra: ' . $ex->getMessage()]);
            }

            return response()->json(array('success' => true));
        }

    }

    public function mb_substr_replace($str, $repl, $start, $length = null)
    {
        preg_match_all('/./us', $str, $ar);
        preg_match_all('/./us', $repl, $rar);
        $length = is_int($length) ? $length : utf8_strlen($str);
        array_splice($ar[0], $start, $length, $rar[0]);
        return implode($ar[0]);
    }

    private function sumField($fields)
    {
        return array_sum($fields);
    }

    public function schoolList()
    {
        if (Admin::user()->inRoles(['tuyen-ttyt-ward'])) {
            if (count(Admin::user()->wards) > 0) {
                $ward_id = Admin::user()->wards[0]->id;
                return redirect()->route('ward.manage.school_list', ['id' => $ward_id]);
            }
        }
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        $school_type = request()->query('school_type', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('school.school_list', ['provinceId' => $district->province->id, 'districtId' => $district->id, 'school_type' => $school_type]);
                }
                $districtId = $district->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                if ($provinceId != $province->id) {
                    return redirect()->route('school.school_list', ['provinceId' => $province->id, 'school_type' => $school_type, 'districtId' => $districtId]);
                }
                $provinceId = $province->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }
        $school_types = School::SCHOOL_TYPES;
        $districts = [];
        $query = School::where('id', '>', 0);
        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
            if ($districtId == null) {
                $listDistrictId = [];
                foreach ($districts as $item) {
                    $listDistrictId[] = $item->id;
                }
                $query = $query->whereIn('district_id', $listDistrictId);
            }
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        if ($school_type) {
            $query = $query->where('school_type', $school_type);
        }
        if (Admin::user()->inRoles(['phong-gd'])) {
            $query->whereNotIn('school_type', [3, 5]);
            unset($school_types[3], $school_types[5]);
        }
        $schools = $query->with(['ward', 'branches', 'district', 'district.province', 'classes'])->get();
        $provinces = Admin::user()->inRoles(['administrator', 'customer-support']) ? Province::all() : [$districts[0]->province];
        return view('admin.view_only.school.school_list', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'school_types' => $school_types,
            'school_type' => $school_type,
        ]);
    }

    public function exportSchoolList()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        $school_type = request()->query('school_type', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('school.export_school_list', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                if ($provinceId != $province->id) {
                    return redirect()->route('school.export_school_list', ['provinceId' => $province->id]);
                }
                $provinceId = $province->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }
        $districts = [];
        $query = School::where('id', '>', 0);
        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
            if ($districtId == null) {
                $listDistrictId = [];
                foreach ($districts as $item) {
                    $listDistrictId[] = $item->id;
                }
                $query = $query->whereIn('district_id', $listDistrictId);
            }
        }
        if ($school_type) {
            $query = $query->where('school_type', $school_type);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        if (Admin::user()->inRoles(['phong-gd'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        $schools = $query->with(['ward', 'branches', 'district', 'district.province', 'classes', 'students'])->get();

        return (new ExportSchoolList($schools))->download('danh-sach-truong.xls');
    }

    public function maugiaoTieuhocThcs()
    {
        if (Admin::user()->inRoles(['hieu-truong', 'school-manager', 'tuyen-ttyt-school'])) {
            if (count(Admin::user()->schools) > 0) {
                $school = Admin::user()->schools[0];
                return \redirect()->route('admin.school.manage', ['id' => $school->id]);
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('school.maugiao_tieuhoc_thcs', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                if ($provinceId != $province->id) {
                    return redirect()->route('school.maugiao_tieuhoc_thcs', ['provinceId' => $province->id]);
                }
                $provinceId = $province->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if(empty($provinceId) && Admin::user()->isAdministrator()) {
            $provinceId = Province::where('id', '>', 0)->first()->id;
        }
        $provinceIdDefault = Province::where('id', '>', 0)->first()->id;

        $districts = [];
        $query = School::where('id', '>', 0);
        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
            if ($districtId == null) {
                $query = $query->whereIn('district_id', $districts->pluck('id'));
            }
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['ward', 'branches', 'district', 'district.province', 'classes', 'staffs'])->whereNotIn('school_type', [3, 5])->get();
        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.school.index', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'provinceIdDefault' => $provinceIdDefault,
        ]);
    }

    public function thpt()
    {
        if (Admin::user()->inRoles(['hieu-truong', 'school-manager'])) {
            if (count(Admin::user()->schools) > 0) {
                $school = Admin::user()->schools[0];
                return \redirect()->route('admin.school.manage', ['id' => $school->id]);
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('school.thpt', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if (Admin::user()->inRoles(['so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                if ($provinceId != $province->id) {
                    return redirect()->route('school.thpt', ['provinceId' => $province->id]);
                }
                $provinceId = $province->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if(empty($provinceId) && Admin::user()->isAdministrator()) {
            $provinceId = Province::where('id', '>', 0)->first()->id;
        }

        $provinceIdDefault = Province::where('id', '>', 0)->first()->id;

        $districts = [];
        $query = School::where('id', '>', 0);

        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
            if ($districtId == null) {
                $query = $query->whereIn('district_id', $districts->pluck('id'));
            }
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['ward', 'branches', 'district', 'district.province', 'classes', 'staffs'])->whereIn('school_type', [3, 5])->get();
        $provinces = Province::with('districts')->get();

        return $this->renderView('admin.school.index_thpt', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'provinceIdDefault' => $provinceIdDefault
        ]);
    }

    public function exportAccounts($id)
    {
        $school = School::where('id', $id)->with([
            'users',
            'teachers',
            'users.roles',
            'classes',
            'classes.teachers'
        ])->first();
        return (new ExportSchoolUsers($school))->download('ds_tai_khoan_truong.xls');
    }

    public function chuanhoa($schoolId){
        //Tự đông tạo account giáo viên 
        //Khi add giáo viên vào 1 bộ môn, thì thêm giáo viên đó vào tổ chuyên môn.
        //Khi sửa mộ môn, khối học của giáo viên thì loại bỏ giá viên khỏi tổ chuyên môn. 
        $this->schoolService->chuanhoa($schoolId);
        return redirect()->back()->with('success', 'Đã chuẩn hoá dữ liệu');
    }

    public function reviewGroupPlans(Request $request, $schoolId) {
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['schoolId'] = $schoolId;
        $data['groupPlans'] = $this->rgPlanService->findPendingPlanBySchool($schoolId);
        return view('admin.school.review_group_plans', $data);
    }
}