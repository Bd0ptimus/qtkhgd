<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Models\Exports\ExportDistrictAccounts;
use App\Admin\Models\Imports\ImportSchool;
use App\Admin\Permission;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\Ward;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\HeadingRowImport;

class AgencyController extends Controller
{
    public function index()
    {
        return view('admin.agency.index', [
            'total_provinces' => Admin::user()->accessProvinces()->get(),
            'total_districts' => Admin::user()->accessDistricts()->get(),
            'total_wards' => Admin::user()->accessWards()->get(),
        ]);
    }

    public function provinces()
    {
        if (!Admin::user()->inRoles(['administrator', 'view.all', 'so-gd'])) {
            return Permission::error();
        }
        $provinces = Admin::user()->accessProvinces()->get();
        return view('admin.agency.provices', [
            'provinces' => $provinces
        ]);
    }

    public function districts()
    {
        if (!Admin::user()->inRoles(['administrator', 'view.all', 'so-gd'])) {
            return Permission::error();
        }

        $province = request()->query('province', null);
        $provinces = Admin::user()->accessProvinces()->get();
        $queryDistricts = Admin::user()->accessDistricts();

        if ($province) $queryDistricts = $queryDistricts->where('province_id', $province);
        $districts = $queryDistricts->get();

        return view('admin.agency.districts', [
            'districts' => $districts,
            'provinces' => $provinces,
            'province' => $province,
        ]);
    }

    public function wards(Request $request)
    {
        if (!Admin::user()->inRoles(['administrator', 'view.all', 'so-gd', 'phong-gd'])) {
            return Permission::error();
        }

        $provinces = Province::with('districts')->get();

        if(!$request->province) {
            return \redirect()->route('admin.agency.wards', ['province' => $provinces[0]->id]);
        }

        $selectedProvince = Province::with('districts')->find($request->province);
        $districts = $selectedProvince->districts;
        
        if ($request->district) {
            $selectedDistrict = District::find($request->district);
        } else {
            $selectedDistrict = $districts[0];
        }

        if($request->isMethod('POST')) {
            $lastWard = Ward::where('id', '>', 0)->orderBy('gso_id', "DESC")->first();
            $newGSO = intval($lastWard->gso_id) + 1;
            Ward::create([
                'name' => $request->ward_name,
                'gso_id' => $newGSO,
                'district_id' => $selectedDistrict->id,
                'is_default' => 0
            ]);
            return redirect()->back()->with('success', 'Thêm phường/xã thành công!');
        }

        return view('admin.agency.wards', [
            'provinces' => $provinces,
            'districts' => $districts,
            'selectedProvince' => $selectedProvince,
            'selectedDistrict' => $selectedDistrict,
            'wards' => Ward::where('district_id', $selectedDistrict->id)->with('district', 'district.province')->get()
        ]);
    }

    public function viewSgdAccountList($id)
    {
        $user = Admin::user();
        if (!$user->inRoles(['administrator', 'view.all', 'so-gd']) || ($user->isRole('so-gd') && $user->agency_id != $id)) {
            return Permission::error();
        }
        $province = Province::where('id', $id)->with('users')->first();
        $users = $province->users;
        return view('admin.agency.sgd_account', [
            'province' => $province,
            'users' => $users
        ]);
    }

    public function viewPgdAccountList($id)
    {
        $user = Admin::user();
        if (!$user->inRoles(['administrator', 'view.all', 'so-gd', 'phong-gd']) || ($user->isRole('phong-gd') && $user->agency_id != $id)) {
            return Permission::error();
        }
        $district = District::where('id', $id)->with(['users.roles', 'province'])->first();
        $users = $district->users;
        return view('admin.agency.pgd_account', [
            'district' => $district,
            'users' => $users
        ]);
    }

    public function viewWardAccountList($id)
    {
        $user = Admin::user();
        if (!$user->inRoles(['administrator', 'view.all', 'so-gd', 'phong-gd']) || ($user->isRole('phong-gd') && $user->agency_id != $id)) {
            return Permission::error();
        }
        $ward = Ward::where('id', $id)->with('users')->first();
        $users = $ward->users;
        return view('admin.agency.ward_account', [
            'ward' => $ward,
            'users' => $users
        ]);
    }

    public function exportAccount($id)
    {
        $district = District::where('id', $id)->with('users.roles')->first();
        $schools = School::where('district_id', $id)->whereNotIn('school_type', [3, 5])->with('users.roles')->get();

        return (new ExportDistrictAccounts($district, $schools))->download('district_accounts.xls');
    }

    public function addSchool($id)
    {
        $data = [
            'school_type' => School::SCHOOL_DISTRICT_TYPES
        ];

        return view('admin.agency.add_school', [
            'district' => District::where('id', $id)->with('province', 'wards')->first(),
            'data' => $data
        ]);
    }

    public function postAddSchool(Request $request, $id)
    {
        $request->validate([
            'ward_id' => 'required',
            'school_name' => 'required',
            'school_email' => 'required|email',
            'school_phone' => 'required|numeric|digits_between:8,16',
            'school_address' => 'required',
            'school_type' => ['required', Rule::in(array_keys(School::SCHOOL_DISTRICT_TYPES))]
        ], [
            'ward_id.required' => __('validation.required', ['attribute' => 'phuờng/xã']),
            'school_name.required' => __('validation.required', ['attribute' => 'tên trường']),
            'school_email.required' => __('validation.required', ['attribute' => 'email']),
            'school_email.email' => __('validation.email'),
            'school_phone.required' => __('validation.required', ['attribute' => 'số điện thoại']),
            'school_phone.numeric' => trans('user.phone_validate'),
            'school_phone.digits_between' => trans('user.phone_digits_between'),
            'school_address.required' => __('validation.required', ['attribute' => 'địa chỉ']),
            'school_type.required' => __('validation.required', ['attribute' => 'loại truờng']),
        ]);

        $data = request()->only([
            'ward_id',
            'school_name',
            'school_email',
            'school_phone',
            'school_address',
            'school_type'
        ]);

        /** @var $district District */
        $district = District::where('id', $id)->with('province', 'wards')->whereHas('wards', function ($query) use ($data) {
            $query->where('id', $data['ward_id']);
        })->find($id);
        if (is_null($district)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $countSchoolSameType = School::where('district_id', $district->id)->where('school_type', $data['school_type'])->count();
        $schoolCode = School::getAccountPrefix($district, $data['school_type']) . ($countSchoolSameType + 1);

        //Render Data and create
        $data['school_code'] = $schoolCode;
        $data['district_id'] = $id;

        DB::beginTransaction();
        try {
            /** @var $school School */
            $school = School::createSchoolWithDefaultUsers($data);
            DB::commit();

            return redirect()->route('admin.school.manage', [
                'id' => $school->id
            ])->with('success', 'Thêm trường thành công!');
        } catch (Exception $ex) {
            if(env('APP_ENV') !== 'production') dd($ex);
            DB::rollback();
        }
    }

    public function editSchool($id, $school_id)
    {
        $data = [
            'school_type' => School::SCHOOL_DISTRICT_TYPES
        ];
        $school = School::find($school_id);
        if (empty($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        return view('admin.agency.edit_school', [
            'district' => District::where('id', $id)->with('province', 'wards')->first(),
            'data' => $data,
            'school' => $school,
        ]);
    }

    public function updateSchool(Request $request, $id, $school_id)
    {
        $request->validate([
            'ward_id' => 'required',
            'school_name' => 'required',
            'school_email' => 'nullable|email',
            'school_phone' => 'required|numeric|digits_between:8,16',
            'school_address' => 'required',
            'school_type' => ['required', Rule::in(array_keys(School::SCHOOL_DISTRICT_TYPES))]
        ], [
            'ward_id.required' => __('validation.required', ['attribute' => 'phuờng/xã']),
            'school_name.required' => __('validation.required', ['attribute' => 'tên trường']),
            'school_email.email' => __('validation.email'),
            'school_phone.required' => __('validation.required', ['attribute' => 'số điện thoại']),
            'school_phone.numeric' => trans('user.phone_validate'),
            'school_phone.digits_between' => trans('user.phone_digits_between'),
            'school_address.required' => __('validation.required', ['attribute' => 'địa chỉ']),
            'school_type.required' => __('validation.required', ['attribute' => 'loại truờng']),
        ]);

        $data = request()->only([
            'ward_id',
            'school_name',
            'school_email',
            'school_phone',
            'school_address',
            'school_type',
        ]);

        /** @var $district District */
        $district = District::where('id', $id)->with('province', 'wards')->whereHas('wards', function ($query) use ($data) {
            $query->where('id', $data['ward_id']);
        })->find($id);
        if (is_null($district)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $school = School::with('staffs')->find($school_id);
        if ($school->school_type != $data['school_type']) {
            $countSchoolSameType = School::where('district_id', $district->id)->where('school_type', $data['school_type'])->count();
            $schoolCode = School::getAccountPrefix($district, $data['school_type']) . ($countSchoolSameType + 1);
            //$student_code_template = $schoolCode . 'HS';
            //$students = $school->students;
            //$index = 0;
            $schoolPrefix = School::getAccountPrefix($district, $data['school_type']);
            $currentExist = SchoolStaff::where('staff_code', 'LIKE', $schoolPrefix . '%')->count();
            $staffs = $school->staffs;


            //Render Data and update
            $data['district_id'] = $id;
            $data['school_code'] = $schoolCode;
        }


        DB::beginTransaction();
        try {
            /** @var $school School */
            $school = School::find($school_id);
            $school->update($data);
            if ($school->school_type != $data['school_type']) {
                /*foreach ($students as $student) {
                    $index++;
                    $student->student_code = $student_code_template . $index;
                    $student->save();
                }*/
                foreach ($staffs as $staff) {
                    $currentExist++;
                    $staff->staff_code = $schoolPrefix . $currentExist;
                    $staff->save();
                }
            }
            DB::commit();

            return redirect()->route('school.maugiao_tieuhoc_thcs', ['provinceId' => $district->province->id, 'districtId' => $id])->with('success', 'Sửa trường thành công!');
        } catch (Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
    }

    public function importSchool($id)
    {
        return view('admin.agency.import_school', [
            'district' => District::where('id', $id)->with('province')->first()
        ]);
    }

    public function postImportSchool($id)
    {
        $district = District::where('id', $id)->with('province')->first();

        $validator = Validator::make(request()->all(), [
            'file_upload' => 'required|file',
        ], [
            'file_upload.required' => trans('validation.file_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        /* Validate Heading */

        $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];

        if (!ImportSchool::validateFileHeader($heading)) {
            return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
        }

        $importData = (new ImportSchool)->toArray(request()->file('file_upload'))[0];
        $importData = ImportSchool::mappingKey($importData);
        $importData = ImportSchool::filterData($importData);
 
        /* Validate Data */
        /* Validate Data in each order */
        $validator = ImportSchool::validator($importData);
        if ($validator->fails()) {
            $message = ImportSchool::getErrorMessage($validator->errors());
            $validator->getMessageBag()->add('file_upload', $message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            //Import School
            //Set School Code
            $countSchoolSameType = School::where('district_id', $district->id)->select('school_type', DB::raw('count(*) as total'))->groupBY('school_type')->pluck('total', 'school_type')->toArray();
            foreach ($importData as $index => $school) {
                $school = array_filter($school, function ($key) {
                    return strlen($key) > 0;
                }, ARRAY_FILTER_USE_KEY);

                $ward = Ward::where('district_id', $id)->where('name', $school['ward'])->first();
                if (!$ward) {
                    $line = $index + 2;
                    return redirect()->back()->with('error', "Dữ liệu xã tại dòng {$line } - {$school['ward']} không đúng. Vui lòng kiểm tra lại!");
                }

                $checkSchoolExist = School::where('ward_id', $ward->id)->where('school_name', $school['school_name'])->first();
                if ($checkSchoolExist) continue;

                else {
                    $schoolType = $school['school_type'];
                    if (!isset($countSchoolSameType[$schoolType])) $countSchoolSameType[$schoolType] = 0;
                    $schoolCode = School::getAccountPrefix($district, $school['school_type']) . ($countSchoolSameType[$schoolType] + 1);
                    $countSchoolSameType[$schoolType] += 1;
                    //Render Data and create
                    unset($school['ward']);
                    $school['school_code'] = $schoolCode;
                    $school['ward_id'] = $ward->id;
                    $school['district_id'] = $id;
                    $newSchool = School::createSchoolWithDefaultUsers($school);
                }
            }
            DB::commit();
            return redirect()->route('school.index', ['district' => $id])->with('success', 'Nhập dữ liệu trường học thành công!');

        } catch (Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
        }

    }
}