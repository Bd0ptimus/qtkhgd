<?php

namespace App\Admin\Controllers\District;

use App\Http\Controllers\Controller;
use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Admin\Models\AdminRole;
use App\Admin\Permission;
use App\Models\District;
use App\Models\Province;
use App\Models\DistrictSpecialistSchool;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DistrictSpecialistController extends Controller
{
    public function getSpecialistUserList()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        $users = collect();
        $districts = collect();
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_PHONG_GD])) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý phòng GD. Vui lòng kiểm tra lại thông tin');
        } else {
            $districtName = '';
            if (Admin::user()->inRoles([ROLE_PHONG_GD]) && count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts->first();
                if(empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.specialist_users', [
                        'districtId' => $district->id,
                        'provinceId' => $district->province->id
                    ]);
                }
                $districtId = $district->id;

                $district = District::where('id', $districtId)->with(['users.roles' => function ($query) {
                    $query->where('slug', ROLE_CV_PHONG);
                }])->first();
                $districtName = $district->name;
                $users = $district->users->filter(function ($value) {
                    return count($value->roles) > 0;
                })->values();
            } else {
                if($provinceId) {
                    /*$province = Province::with('districts')->first();
                    $provinceId = $province ? $province->id : '';
                    $districtId = $province->districts->first() ? $province->districts->first()->id : '';*/

                    if($districtId) {
                        $district = District::where('id', $districtId)->with(['users.roles' => function ($query) {
                            $query->where('slug', ROLE_CV_PHONG);
                        }])->first();
                        $districtName = $district->name;
                    } else {
                        $province = Province::with('districts')->where('id', $provinceId)->first();
                        $districtIds = $province->districts->pluck('id');
                        $district = District::whereIn('id', $districtIds)->with(['users.roles' => function ($query) {
                            $query->where('slug', ROLE_CV_PHONG);
                        }])->first();
                    }

                    $users = $district->users->filter(function ($value) {
                        return count($value->roles) > 0;
                    })->values();
                }
            }
        }

        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
        }

        $users = $users->sortByDesc('id');

        $provinces = Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? Province::all() : [$districts[0]->province];

        return view('admin.district.user_list', [
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'districtName' => $districtName,
            'users' => $users
        ]);
    }

    public function putEditUserDistrict(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Sửa tên chuyên viên thất bại!');
        } else {
            $params = $request->all();
            DB::beginTransaction();
            try {
                AdminUser::where('id', $params['id'])
                    ->update(['name' => $params['name']]);
                DB::commit();

                return redirect()->back()->with('success', 'Sửa tên chuyên viên thành công!');
            } catch (Exception $ex) {
                DB::rollback();
                Log::info('Thêm tài khoản chuyên viên: '. $ex->getMessage());
                return redirect()->back()->with('error', 'Sửa tên chuyên viên thất bại!');
            }
        }

    }

    public function postAddUserDistrict(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'district_id' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Thêm tài khoản chuyên viên thất bại!');
        } else {
            $params = $request->all();
            $userRole = Admin::user()->roles->first();
            if ($params['role_id'] == ROLE_CV_PHONG_ID
                && !in_array($userRole->id, [ROLE_ADMIN_ID, ROLE_CM_ID, ROLE_PHONG_GD_ID])) {
                return redirect()->back()->with('error', 'Không có quyền tạo tài khoản này!');
            }
            return redirect()->route('district.manage.add_user', [
                'district_id' => $params['district_id'],
                'role_id' => $params['role_id'],
                'name' => $params['name'],
            ]);
        }
    }

    /*Create user*/
    public function createDistrictUser(Request $request, $district_id, $role_id)
    {
        $userRole = Admin::user()->roles->first();

        if ($role_id == ROLE_CV_PHONG_ID
            && !in_array($userRole->id, [ROLE_ADMIN_ID, ROLE_CM_ID, ROLE_PHONG_GD_ID])) {
            return redirect()->route('district.specialist_users', [
                'district_id' => $district_id,
                'role_id' => $role_id,
            ])->with('error', 'Không có quyền tạo tài khoản này!');
        }
        $params = $request->all();
        $user_first_name = '';

        switch ($role_id) {
            case ROLE_CV_PHONG_ID:
                $user_first_name = $params['name'];
                break;
            default:
                $user_first_name = null;
                break;
        }

        $role = AdminRole::find($role_id);

        $district = District::find($district_id);

        $accountPrefix = strtoupper("CV".$district->gso_id . "PH");

        $currentExist = AdminUser::where('username', 'like', $accountPrefix . '%')->count();

        $dataInsert = [
            'username' => $accountPrefix . ($currentExist + 1),
            'password' => bcrypt(\Config::get('constants.password_reset')),
            'name' => $user_first_name,
            'avatar' => null,
            'created_by' => Admin::user()->id,
            'phone_number' => null,
            'force_change_pass' => 1
        ];
        DB::beginTransaction();
        try {
            AdminUser::createAcount($dataInsert, $role->slug, $district->id);
            DB::commit();

            return redirect()->route('district.specialist_users', [
                'district_id' => $district_id,
                'role_id' => $role_id,
            ])->with('success', 'Thêm tài khoản chuyên viên thành công!');
        } catch (Exception $ex) {
            DB::rollback();
            Log::info('Thêm tài khoản chuyên viên: '. $ex->getMessage());
            return redirect()->route('district.specialist_users', [
                'district_id' => $district_id,
                'role_id' => $role_id,
            ])->with('error', 'Thêm tài khoản chuyên viên thất bại!');
        }
    }

    public function getSpecialistSchool()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        $specialistId = request()->query('specialistId', null);
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_PHONG_GD])) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý phòng GD. Vui lòng kiểm tra lại thông tin');
        } else {
            $specialist = collect();
            $schools = collect();
            $users = collect();
            $districtName = '';
            $userIds = [];
            if (Admin::user()->inRoles([ROLE_PHONG_GD]) && count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts->first();
                if(empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.specialist_users', [
                        'districtId' => $district->id,
                        'provinceId' => $district->province->id
                    ]);
                }
                $districtId = $district->id;
                $schools = School::where('id', '>', 0)->where('district_id', $districtId)->get();

                $district = District::where('id', $districtId)->with(['users.roles' => function ($query) {
                    $query->where('slug', ROLE_CV_PHONG);
                }])->first();

                $users = $district->users->filter(function ($value) {
                    return count($value->roles) > 0;
                })->values();
                $userIds = $users->pluck('id')->toArray();
                $districtName = $district->name;
            } else {
                if($provinceId) {
                    if($districtId) {
                        $district = District::where('id', $districtId)->with(['users.roles' => function ($query) {
                            $query->where('slug', ROLE_CV_PHONG);
                        }])->first();

                        $schools = School::where('id', '>', 0)->where('district_id', $districtId)->get();

                        $users = $district->users->filter(function ($value) {
                            return count($value->roles) > 0;
                        })->values();

                        $districtName = $district->name;
                        $userIds = $users->pluck('id')->toArray();
                    }
                }
            }
        }

        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
        }
        $schoolSelectedIds = [];
        if ($specialistId) {
            $specialist = AdminUser::where('id', $specialistId)->with('specialistSchool')->first();
            if(count($userIds)) {
                $schoolSelectedIds = DistrictSpecialistSchool::whereIn('specialist_id', $userIds)->pluck('school_id')->toArray();
            }
        }

        $provinces = Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? Province::all() : [$districts[0]->province];

        return view('admin.district.specialist_school', [
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'specialistId' => $specialistId,
            'districtName' => $districtName,
            'schools' => $schools,
            'schoolSelectedIds' => $schoolSelectedIds,
            'specialist' => $specialist,
            'users' => $users
        ]);
    }

    public function assignSpecialistSchool(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required',
            'school_id' => 'required|unique:district_specialist_school,school_id',
        ], [
            'specialist_id.required' => __('validation.required', ['attribute' => 'chuyên viên']),
            'school_id.required' => __('validation.required', ['attribute' => 'truờng học']),
            'school_id.unique' => __('validation.unique', ['attribute' => 'truờng học']),
        ]);

        $params = $request->all();

        DB::beginTransaction();
        DistrictSpecialistSchool::where('specialist_id', $params['specialist_id'])->delete();
        try {
            foreach ($params['school_id'] as $val) {
                DistrictSpecialistSchool::create([
                    'specialist_id' => $params['specialist_id'],
                    'school_id' => $val,
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Lưu thành công');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::info($ex->getMessage());
            if(env('APP_ENV') !== 'production') dd($ex);
            return redirect()->back()->with('error', 'Lưu không thành công');
        }
    }
}