<?php

namespace App\Admin\Controllers\Ward;

use App\Admin\Admin;
use App\Admin\Models\Exports\ExportDistrictAccounts;
use App\Admin\Models\Exports\ExportSchoolList;
use App\Admin\Models\Imports\ImportSchool;
use App\Admin\Permission;
use App\Http\Controllers\Controller;
use App\Models\Ward;
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

class WardController extends Controller
{
    public function schoolActivityList($id)
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support', 'tuyen-ttyt-ward'])) {
            return Permission::error();
        }
        $ward = Ward::with('district', 'district.province')->find($id);
        $schools = School::with('branches','classes', 'students')->where('ward_id', $id)->get();
        return $this->renderView('admin.ward.school_activity_list', [
            'schools' => $schools,
            'ward' => $ward
        ]);
    }

    public function schoolList($id)
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support', 'tuyen-ttyt-ward'])) {
            return Permission::error();
        }
        $school_type = request()->query('school_type', null);
        $ward = Ward::with('district', 'district.province')->find($id);
        $query = School::with('branches','classes', 'students')->where('ward_id', $id);
        if($school_type) $query = $query->where('school_type', $school_type);
        $schools = $query->get();
        $school_types = School::SCHOOL_TYPES;
        return $this->renderView('admin.ward.school_list', [
            'schools' => $schools,
            'ward' => $ward,
            'school_types' => $school_types,
            'school_type' => $school_type
        ]);
    }

    public function exportSchoolList($id)
    {
        if (!Admin::user()->inRoles(['administrator', 'customer-support', 'tuyen-ttyt-ward'])) {
            return redirect()->back()->with('error', "Bạn không có quyền export.");
        }
        $school_type = request()->query('school_type', null);
        $query = School::with(['branches','classes', 'students', 'ward', 'district','district.province'])->where('ward_id', $id);
        if($school_type) $query = $query->where('school_type', $school_type);
        $schools = $query->get();
        
        return (new ExportSchoolList($schools))->download('danh-sach-truong.xls');
    }
}