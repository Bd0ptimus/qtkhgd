<?php

namespace App\Admin\Controllers\District;

use App\Admin\Admin;
use App\Admin\Models\Exports\District\Covid\BCChiTietNgay\ExportBCChiTietNgay;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Admin\Models\Exports\ExportInfectiousDiseasesSchoolList;
use App\Admin\Models\Exports\District\Covid\TinhHinhHSGV\ExportTinhHinhHSGV;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function infectiousDiseasesStatistics()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        $filter_start_date = request()->query('filter_start_date', null) ?? Carbon::today()->startOfMonth()->toDateString();
        $filter_end_date = request()->query('filter_end_date', null) ?? Carbon::today()->endOfMonth()->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $filter_start_date)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $filter_end_date)->endOfDay()->toDateTimeString();
        
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if(empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.infectious_diseases_statistics', ['districtId' => $district->id, 'provinceId' => $district->province->id, 'filter_start_date' => $filter_start_date, 'filter_end_date' => $filter_end_date]);
                }
                $districtId = $district->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                if($provinceId != $province->id) {
                    return redirect()->route('district.infectious_diseases_statistics', ['provinceId' => $province->id, 'filter_start_date' => $filter_start_date, 'filter_end_date' => $filter_end_date]);
                }
                $provinceId = $province->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

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
        $schools = $query_school->with(['ward', 'branches', 'district', 'district.province','classes', 'students', 'students.healthAbnormals' => function ($query) use ($type, $diagnosis, $start_date, $end_date) {
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
        }])->whereNotIn('school_type', [3,5])->get();

        $provinces = Province::with('districts')->get();
        return view('admin.district.infectious_diseases_statistics', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'filter_start_date' => $filter_start_date,
            'filter_end_date' => $filter_end_date,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type' => $type
        ]);
    }

    public function exportInfectiousDiseasesStatistics()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        $filter_start_date = request()->query('filter_start_date', null) ?? Carbon::today()->startOfMonth()->toDateString();
        $filter_end_date = request()->query('filter_end_date', null) ?? Carbon::today()->endOfMonth()->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $filter_start_date)->startOfDay()->toDateTimeString();
        $end_date = Carbon::createFromFormat('Y-m-d', $filter_end_date)->endOfDay()->toDateTimeString();
        
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if(empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.infectious_diseases_statistics', ['districtId' => $district->id, 'provinceId' => $district->province->id, 'filter_start_date' => $filter_start_date, 'filter_end_date' => $filter_end_date]);
                }
                $districtId = $district->id;
            } else {
                return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
            }
        }

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
        $schools = $query_school->with(['ward', 'branches', 'district', 'district.province','classes', 'students', 'students.healthAbnormals' => function ($query) use ($type, $diagnosis, $start_date, $end_date) {
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
        }])->whereNotIn('school_type', [3,5])->get();

        $provinces = Province::with('districts')->get();
        $selectDistrict = null;
        foreach ($districts as $district) {
            if($district->id == $districtId){
                $selectDistrict = $district;
            } 
        }
        return (new ExportInfectiousDiseasesSchoolList($schools, $selectDistrict))->download('bao-cao-benh-truyen-nhiem.xls');

    }

    //Thong ke giao vien hoc sinh
    public function baoCaoTongHop($id, Request $request) {
        if ($request->isMethod('post')) {
            $district = District::find($id);
            $date = $request->date;
            
            return (new ExportBCChiTietNgay($district, $date))->downloadAsName();
        } else {
            return $this->renderView('admin.district.report.covid.baocaotonghop', [
                'districtId' => $id,
            ]);
        }
    }

    //thong ke giao vien hoc sinh theo tung khoi
    public function baoCaoTinhHinhHsGv($id,Request $request){
        if ($request->isMethod('post')) {
            $district = District::find($id);
            $date = $request->date;
            $schoolType = $request->schoolType;
            
            return (new ExportTinhHinhHSGV($district, $schoolType, $date))->downloadAsName();
        } else { 
            return $this->renderView('admin.district.report.covid.thongketinhhinhhsgv', [
                'districtId' => $id,
            ]);
        }
    }
}