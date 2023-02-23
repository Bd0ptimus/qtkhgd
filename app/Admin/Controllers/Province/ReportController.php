<?php

namespace App\Admin\Controllers\Province;

use App\Http\Controllers\Controller;
use App\Admin\Admin;
use App\Admin\Helpers\ListHelper;
use Carbon\Carbon;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Admin\Models\Exports\ExportInfectiousDiseasesDictrictList;
use App\Admin\Models\Exports\ExportInfectiousDiseasesSchoolList;

class ReportController extends Controller
{
    public function infectiousDiseasesStatisticsDistrict()
    {
        $filter_start_date = request()->query('filter_start_date', null) ?? Carbon::today()->startOfMonth()->toDateString();
        $filter_end_date = request()->query('filter_end_date', null) ?? Carbon::today()->endOfMonth()->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $filter_start_date)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $filter_end_date)->endOfDay()->toDateTimeString();
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
            }
        } elseif(Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $province = Province::find(request()->query('province', null));
        }
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý báo cáo bệnh truyền nhiễm. Vui lòng kiểm tra lại thông tin');
        }
        $provinceId = $province->id;
        $districts = [];
        $type = 3; //Type = 3: Bệnh truyền nhiễm
        $diagnosis = [21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
        $districts = District::with(['schools' => function ($query){
                $query->whereNotIn('school_type', [3,5]);
            } ,'schools.students.healthAbnormals' => function ($query) use ($type, $diagnosis, $start_date, $end_date) {
                if (!empty($type)) {
                    $query->where('type', $type);
                }
                if (!empty($diagnosis)) {
                    $query->whereIn('diagnosis', $diagnosis);
                }
                if (!empty($start_date)) {
                    $query->where('date', '>=', $start_date);
                }
                if (!empty($end_date)) {
                    $query->where('date', '<=', $end_date);
                }
            }])->where('province_id', $provinceId)->get();
        $total_tieuchay = $districts->reduce(function ($count, $district) {
            return $count + $district->total_tieuchay;
        }, 0);
        $total_chantaymieng = $districts->reduce(function ($count, $district) {
            return $count + $district->total_chantaymieng;
        }, 0);
        $total_soi = $districts->reduce(function ($count, $district) {
            return $count + $district->total_soi;
        }, 0);
        $total_quaibi = $districts->reduce(function ($count, $district) {
            return $count + $district->total_quaibi;
        }, 0);
        $total_cum = $districts->reduce(function ($count, $district) {
            return $count + $district->total_cum;
        }, 0);
        $total_rubella = $districts->reduce(function ($count, $district) {
            return $count + $district->total_rubella;
        }, 0);
        $total_sotxuathuyet = $districts->reduce(function ($count, $district) {
            return $count + $district->total_sotxuathuyet;
        }, 0);
        $total_thuydau = $districts->reduce(function ($count, $district) {
            return $count + $district->total_thuydau;
        }, 0);
        $total_sars_cov_2 = $districts->reduce(function ($count, $district) {
            return $count + $district->total_sars_cov_2;
        }, 0);
        $total_khac = $districts->reduce(function ($count, $district) {
            return $count + $district->total_khac;
        }, 0);
        
        return view('admin.province.infectious_diseases_statistics_district', [
            'filter_start_date' => $filter_start_date,
            'filter_end_date' => $filter_end_date,
            'districts' => $districts,
            'type' => $type,
            'total_tieuchay' => $total_tieuchay,
            'total_chantaymieng' => $total_chantaymieng,
            'total_soi' => $total_soi,
            'total_quaibi' => $total_quaibi,
            'total_cum' => $total_cum,
            'total_rubella' => $total_rubella,
            'total_sotxuathuyet' => $total_sotxuathuyet,
            'total_thuydau' => $total_thuydau,
            'total_sars_cov_2' => $total_sars_cov_2,
            'total_khac' => $total_khac,
        ]);
    }

    public function exportInfectiousDiseasesStatisticsDistrict()
    {
        $filter_start_date = request()->query('filter_start_date', null) ?? Carbon::today()->startOfMonth()->toDateString();
        $filter_end_date = request()->query('filter_end_date', null) ?? Carbon::today()->endOfMonth()->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $filter_start_date)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $filter_end_date)->endOfDay()->toDateTimeString();
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
            }
        } elseif(Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $province = Province::find(request()->query('province', null));
        }
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý báo cáo bệnh truyền nhiễm. Vui lòng kiểm tra lại thông tin');
        }
        $provinceId = $province->id;
        $districts = [];
        $type = 3; //Type = 3: Bệnh truyền nhiễm
        $diagnosis = [21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
        $districts = District::with(['schools' => function ($query){
                $query->whereNotIn('school_type', [3,5]);
            } ,'schools.students.healthAbnormals' => function ($query) use ($type, $diagnosis, $start_date, $end_date) {
                if (!empty($type)) {
                    $query->where('type', $type);
                }
                if (!empty($diagnosis)) {
                    $query->whereIn('diagnosis', $diagnosis);
                }
                if (!empty($start_date)) {
                    $query->where('date', '>=', $start_date);
                }
                if (!empty($end_date)) {
                    $query->where('date', '<=', $end_date);
                }
            }])->where('province_id', $provinceId)->get();
        $total_tieuchay = $districts->reduce(function ($count, $district) {
            return $count + $district->total_tieuchay;
        }, 0);
        $total_chantaymieng = $districts->reduce(function ($count, $district) {
            return $count + $district->total_chantaymieng;
        }, 0);
        $total_soi = $districts->reduce(function ($count, $district) {
            return $count + $district->total_soi;
        }, 0);
        $total_quaibi = $districts->reduce(function ($count, $district) {
            return $count + $district->total_quaibi;
        }, 0);
        $total_cum = $districts->reduce(function ($count, $district) {
            return $count + $district->total_cum;
        }, 0);
        $total_rubella = $districts->reduce(function ($count, $district) {
            return $count + $district->total_rubella;
        }, 0);
        $total_sotxuathuyet = $districts->reduce(function ($count, $district) {
            return $count + $district->total_sotxuathuyet;
        }, 0);
        $total_thuydau = $districts->reduce(function ($count, $district) {
            return $count + $district->total_thuydau;
        }, 0);
        $total_sars_cov_2 = $districts->reduce(function ($count, $district) {
            return $count + $district->total_sars_cov_2;
        }, 0);
        $total_khac = $districts->reduce(function ($count, $district) {
            return $count + $district->total_khac;
        }, 0);
        
        return (new ExportInfectiousDiseasesDictrictList($districts))->download('bao-cao-benh-truyen-nhiem-theo-cac-phong-gd.xls');

    }

    public function infectiousDiseasesStatisticsThpt()
    {
        $districtId = request()->query('districtId', null);
        $filter_start_date = request()->query('filter_start_date', null) ?? Carbon::today()->startOfMonth()->toDateString();
        $filter_end_date = request()->query('filter_end_date', null) ?? Carbon::today()->endOfMonth()->toDateString();
        if(!request()->query('filter_start_date', null) || !request()->query('filter_end_date', null)){
            return redirect()->route('province.infectious_diseases_statistics.thpt',['province' => request()->query('province', null),'filter_start_date' => $filter_start_date, 'filter_end_date' => $filter_end_date]);
        }
        $start_date = Carbon::createFromFormat('Y-m-d', $filter_start_date)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $filter_end_date)->endOfDay()->toDateTimeString();
        
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
            }
        } elseif(Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $province = Province::find(request()->query('province', null));
        }
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý báo cáo bệnh truyền nhiễm. Vui lòng kiểm tra lại thông tin');
        }
        $provinceId = $province->id;
        $districts = [];
        $type = 3; //Type = 3: Bệnh truyền nhiễm
        $diagnosis = [21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
        $query_school = School::where('id', '>', 0);
        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
            if ($districtId == null) {
                $listDistrictId = [];
                foreach ($districts as $item) {
                    $listDistrictId[] = $item->id;
                }
                $query_school = $query_school->whereIn('district_id', $listDistrictId);
            }
        }
        if ($districtId) $query_school = $query_school->where('district_id', $districtId);
        $schools = $query_school->with(['students', 'students.healthAbnormals' => function ($query) use ($type, $diagnosis, $start_date, $end_date) {
            if (!empty($type)) {
                $query->where('type', $type);
            }
            if (!empty($diagnosis)) {
                $query->whereIn('diagnosis', $diagnosis);
            }
            if (!empty($start_date)) {
                $query->where('date', '>=', $start_date);
            }
            if (!empty($end_date)) {
                $query->where('date', '<=', $end_date);
            }
        }])->whereIn('school_type', [3,5])->get();

        $total_tieuchay = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(21);
        }, 0);
        $total_chantaymieng =$schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(22);
        }, 0);
        $total_soi = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(23);
        }, 0);
        $total_quaibi = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(24);
        }, 0);
        $total_cum = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(25);
        }, 0);
        $total_rubella = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(26);
        }, 0);
        $total_sotxuathuyet = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(27);
        }, 0);
        $total_thuydau =$schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(28);
        }, 0);
        $total_sars_cov_2 = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(29);
        }, 0);
        $total_khac = $schools->reduce(function ($count, $school) {
            return $count + $school->countingStudentsWithInfectiousDiseases(30);
        }, 0);
        $provinces = Province::with('districts')->get();
        return view('admin.province.infectious_diseases_statistics_thpt', [
            'filter_start_date' => $filter_start_date,
            'filter_end_date' => $filter_end_date,
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type' => $type,
            'total_tieuchay' => $total_tieuchay,
            'total_chantaymieng' => $total_chantaymieng,
            'total_soi' => $total_soi,
            'total_quaibi' => $total_quaibi,
            'total_cum' => $total_cum,
            'total_rubella' => $total_rubella,
            'total_sotxuathuyet' => $total_sotxuathuyet,
            'total_thuydau' => $total_thuydau,
            'total_sars_cov_2' => $total_sars_cov_2,
            'total_khac' => $total_khac,
        ]);
    }

    public function exportInfectiousDiseasesStatisticsThpt()
    {
        $districtId = request()->query('districtId', null);
        $filter_start_date = request()->query('filter_start_date', null) ?? Carbon::today()->startOfMonth()->toDateString();
        $filter_end_date = request()->query('filter_end_date', null) ?? Carbon::today()->endOfMonth()->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $filter_start_date)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $filter_end_date)->endOfDay()->toDateTimeString();
        
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
            }
        } elseif(Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $province = Province::find(request()->query('province', null));
        }
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý báo cáo bệnh truyền nhiễm. Vui lòng kiểm tra lại thông tin');
        }
        $provinceId = $province->id;
      

        $districts = [];
        $type = 3; //Type = 3: Bệnh truyền nhiễm
        $diagnosis = [21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
        $query_school = School::where('id', '>', 0);
        if ($provinceId) {
            $districts = District::where('province_id', $provinceId)->get();
            if ($districtId == null) {
                $listDistrictId = [];
                foreach ($districts as $item) {
                    $listDistrictId[] = $item->id;
                }
                $query_school = $query_school->whereIn('district_id', $listDistrictId);
            }
        }
        if ($districtId) $query_school = $query_school->where('district_id', $districtId);
        $schools = $query_school->with(['students', 'students.healthAbnormals' => function ($query) use ($type, $diagnosis, $start_date, $end_date) {
            if (!empty($type)) {
                $query->where('type', $type);
            }
            if (!empty($diagnosis)) {
                $query->whereIn('diagnosis', $diagnosis);
            }
            if (!empty($start_date)) {
                $query->where('date', '>=', $start_date);
            }
            if (!empty($end_date)) {
                $query->where('date', '<=', $end_date);
            }
        }])->whereIn('school_type', [3,5])->get();
        $provinces = Province::with('districts')->get();
        $selectDistrict = null;
        foreach ($districts as $district) {
            if($district->id == $districtId){
                $selectDistrict = $district;
            } 
        }
        return (new ExportInfectiousDiseasesSchoolList($schools, $selectDistrict))->download('bao-cao-benh-truyen-nhiem-theo-truong-thpt.xls');
    }

}