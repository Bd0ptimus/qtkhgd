<?php

namespace App\Admin\Controllers\Province;

use App\Admin\Admin;

use App\Admin\Permission;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\HeadingRowImport;

class ProvinceController extends Controller
{
    public function getList()
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support'])) {
            return Permission::error();
        }
        
        $provinces = Admin::user()->accessProvinces()->with('users', 'districts.schools.students','districts.schools.staffs')->get();
        
        return view('admin.province.province_list', [
            'provinces' => $provinces,
        ]);
    }

    public function manage($id)
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support'])) {
            return Permission::error();
        }
        $province = Province::with('users', 'districts', 'districts.schools.students','districts.schools.staffs')->find($id);      
        if(!$province){
            return redirect()->back()->with('error','Không tồn tại tỉnh');
        }
        $schoolCount = $province->total_school;
        $staffCount = $province->total_staff;
        $studentCount = $province->total_student;
        return $this->renderView('admin.province.manage', [
            'province' => $province,
            'schoolCount' => $schoolCount,
            'staffCount' => $staffCount,
            'studentCount' => $studentCount
        ]);
    }
}