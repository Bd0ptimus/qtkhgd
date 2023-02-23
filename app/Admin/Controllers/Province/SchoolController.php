<?php

namespace App\Admin\Controllers\Province;

use App\Admin\Admin;
use App\Admin\Models\Exports\Province\District\ExportDistrictPL02;
use App\Admin\Models\Exports\Province\District\ExportDistrictPL03;
use App\Admin\Models\Exports\Province\District\ExportDistrictPL04;
use App\Admin\Models\Exports\Province\ExportCheckRoomDistrict;
use App\Admin\Models\Exports\Province\ExportCheckRoomThpt;
use App\Admin\Models\Exports\Province\Thpt\ExportThptPL04;
use App\Admin\Models\Exports\Province\Thpt\ExportProvinceThptAccounts;
use App\Admin\Models\Imports\ImportThptSchool;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Ward;
use App\Models\PL02Report;
use App\Models\PL03Report;
use App\Models\PL04Report;
use App\Models\Province;
use App\Models\School;
use App\Models\SchoolReport;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\HeadingRowImport;


class SchoolController extends Controller
{
    public function roomAnalyticsDistrict()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $province = optional($provinces->first());
                $provinceId = $province->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districts = District::with([
            'schools' => function ($query) {
                $query->whereNotIn('school_type', [3, 5]);
            },
            'schools.branches.classes',
            'schools.branches.checkRooms.checkRoomDetails'
        ])->where('province_id', $provinceId)
            ->get();
        $data = [];
        foreach ($districts as $district) {
            $row = new \stdClass();
            $checkRoomDetails = $district->schools->pluck('branches.*.checkRooms.*.checkRoomDetails')->collapse()->collapse();
            $row->district_name = $district->name;
            $row->so_lop_hoc = count($district->schools->pluck('branches.*.classes')->collapse()->collapse());
            $row->phong_hoc_so_luong = count($checkRoomDetails);
            $row->phong_hoc_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 1));
            $row->phong_hoc_chua_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 0));
            $row->do_roi_dat_chuan = count($checkRoomDetails->where('do_roi', 1));
            $row->do_roi_chua_dat_chuan = count($checkRoomDetails->where('do_roi', 0));
            $row->do_chieu_sang_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 1));
            $row->do_chieu_sang_chua_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 0));
            $data[] = $row;
        }

        return view('admin.province.report.district.room_analytics_district', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'data' => $data
        ]);
    }

    public function roomAnalyticsDistrictExport()
    {
        $provinceId = request()->query('province', null);
        $province = Province::find($provinceId);

        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districts = District::with([
            'schools' => function ($query) {
                $query->whereNotIn('school_type', [3, 5]);
            },
            'schools.branches.classes',
            'schools.branches.checkRooms.checkRoomDetails'
        ])
            ->where('province_id', $provinceId)
            ->get();
        $data = [];
        foreach ($districts as $district) {
            $row = new \stdClass();
            $checkRoomDetails = $district->schools->pluck('branches.*.checkRooms.*.checkRoomDetails')->collapse()->collapse();
            $row->district_name = $district->name;
            $row->so_lop_hoc = count($district->schools->pluck('branches.*.classes')->collapse()->collapse());
            $row->phong_hoc_so_luong = count($checkRoomDetails);
            $row->phong_hoc_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 1));
            $row->phong_hoc_chua_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 0));
            $row->do_roi_dat_chuan = count($checkRoomDetails->where('do_roi', 1));
            $row->do_roi_chua_dat_chuan = count($checkRoomDetails->where('do_roi', 0));
            $row->do_chieu_sang_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 1));
            $row->do_chieu_sang_chua_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 0));
            $data[] = $row;
        }

        return (new ExportCheckRoomDistrict($province, $data))->download('csvc-danh-gia-phong-hoc-so.xls');
    }

    public function roomAnalyticsThpt()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $province = optional($provinces->first());
                $provinceId = $province->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $schools = District::with([
            'schools' => function ($query) {
                $query->whereIn('school_type', [3, 5]);
            },
            'schools.branches.classes',
            'schools.branches.checkRooms.checkRoomDetails'
        ])
            ->where('province_id', $provinceId)
            ->get()
            ->pluck('schools')
            ->collapse();
        $data = [];
        foreach ($schools as $school) {
            $row = new \stdClass();
            $checkRoomDetails = $school->branches->pluck('checkRooms.*.checkRoomDetails')->collapse()->collapse();
            $row->school_name = $school->school_name;
            $row->so_lop_hoc = count($school->branches->pluck('classes')->collapse());
            $row->phong_hoc_so_luong = count($checkRoomDetails);
            $row->phong_hoc_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 1));
            $row->phong_hoc_chua_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 0));
            $row->do_roi_dat_chuan = count($checkRoomDetails->where('do_roi', 1));
            $row->do_roi_chua_dat_chuan = count($checkRoomDetails->where('do_roi', 0));
            $row->do_chieu_sang_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 1));
            $row->do_chieu_sang_chua_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 0));
            $data[] = $row;
        }

        return view('admin.province.report.thpt.room_analytics_thpt', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'data' => $data
        ]);
    }

    public function roomAnalyticsThptExport()
    {
        $provinceId = request()->query('province', null);
        $province = Province::find($provinceId);

        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $schools = District::with([
            'schools' => function ($query) {
                $query->whereIn('school_type', [3, 5]);
            },
            'schools.branches.classes',
            'schools.branches.checkRooms.checkRoomDetails'
        ])
            ->where('province_id', $provinceId)
            ->get()
            ->pluck('schools')
            ->collapse();
        $data = [];
        foreach ($schools as $school) {
            $row = new \stdClass();
            $checkRoomDetails = $school->branches->pluck('checkRooms.*.checkRoomDetails')->collapse()->collapse();
            $row->school_name = $school->school_name;
            $row->so_lop_hoc = count($school->branches->pluck('classes')->collapse());
            $row->phong_hoc_so_luong = count($checkRoomDetails);
            $row->phong_hoc_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 1));
            $row->phong_hoc_chua_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 0));
            $row->do_roi_dat_chuan = count($checkRoomDetails->where('do_roi', 1));
            $row->do_roi_chua_dat_chuan = count($checkRoomDetails->where('do_roi', 0));
            $row->do_chieu_sang_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 1));
            $row->do_chieu_sang_chua_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 0));
            $data[] = $row;
        }

        return (new ExportCheckRoomThpt($province, $data))->download('csvc-danh-gia-phong-hoc-so-thpt.xls');
    }

    public function pl02DistrictSend()
    {
        $provinces = Province::all();
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Province::with(['districts.schools', 'districts.province'])->find(Admin::user()->provinces[0]->id ?? null);
            }
        } elseif(Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $province = Province::with(['districts.schools', 'districts.province'])->find(request()->query('province', null));
        }
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý báo cáo PL02 sở. Vui lòng kiểm tra lại thông tin');
        }
        $provinceId = $province->id;
        $districtSendIds = [];
        foreach ($province->districts as $district){
            $schools = collect($district->schools)->whereNotIn('school_type', [3, 5])->where('district_id', $district->id)->values()->all();
            $school_ids = [];
            foreach($schools as $school){
                array_push($school_ids, $school->id);
            }
            $schoolReports = SchoolReport::whereHasMorph(
                'report',
                [PL02Report::class]
            )->where('agency_id', $district->id)->whereIn('school_id', $school_ids)->get();
            if(count($schoolReports) > 0){
                array_push($districtSendIds, $district->id);
            }
        }
        $districtSends = collect($province->districts)->whereIn('id', $districtSendIds)->values()->all();
        $districtNotSends = collect($province->districts)->whereNotIn('id', $districtSendIds)->values()->all();
        return view('admin.province.report.district.pl02.index', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'districtSends' => $districtSends,
            'districtNotSends' => $districtNotSends
        ]);
    }

    public function pl02DistrictResult()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $provinceId = optional($provinces->first())->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districtIds = District::select('id')
            ->where('province_id', $provinceId)
            ->pluck('id')
            ->toArray();
        $schools = School::with([
            'schoolReport' => function (HasOne $query) {
                $query->with('report')
                    ->where('report_type', PL02Report::class);
            }
        ])->whereNotIn('school_type', [3, 5])
            ->whereIn('district_id', $districtIds)
            ->get();

        $pl02Reports = $schools->pluck('schoolReport.report')->filter();
        foreach ($pl02Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }
        $report = [];
        $booleanFields = [
            'is_healthcare_system', 'is_approved_plan', 'ql_bahd_cotochuckhong', 'ql_bahd_dinhduonghoply', 'ql_tkctyt_pchiv',
            'ql_tkctyt_pctntt', 'ql_tkctyt_pcdbtn', 'ql_tkctyt_pcsdd', 'ql_tkctyt_attp', 'ql_tkctyt_pctl',
            'ql_tkctyt_pcrb', 'ql_tkctyt_xdth', 'hdtt_bstlph', 'hdtt_goctt', 'bddkcssk_pytth', 'bddkcssk_pytcddk', 'bddkcssk_nvytth',
            'bddkcssk_skb', 'bddkcssk_stdsk', 'bddkcssk_stdth', "bddkcsvc_phong", "bddkcsvc_banghe", "bddkcsvc_bang", "bddkcsvc_chieusang",
            "bddkcsvc_thietbi", "bddkcsvc_nuocanuong", "bddkcsvc_nuocsh", "bddkcsvc_ctvs", "bddkcsvc_tgxlrt", "bddkcsvc_attp", "bdmtttcs_pccssk",
            "bdmtttcs_qdthcs", "bdmtttcs_xdqhtchs", "bdmtttcs_xdqhntgd",
            "dgctyt_tudanhgia", "dgctyt_dgccqql",
            "dgctyt_tudanhgia_xeploai", "dgctyt_dgccqql_xeploai"
        ];
        $pl02Reports = $pl02Reports->toArray();
        if ($pl02Reports) {
            foreach(array_keys($pl02Reports[0])as $array_key) {
                if (!in_array($array_key, $booleanFields)) {
                    $report[$array_key] = array_sum(array_column($pl02Reports, $array_key));
                } else {
                    $report[$array_key] = [
                        1 => count(collect($pl02Reports)->where($array_key, 1)),
                        0 => count(collect($pl02Reports)->where($array_key, 0))
                    ];
                }
            }
        }
        return view('admin.province.report.district.pl02.result', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'report' => $report
        ]);
    }

    public function pl02DistrictExport()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $provinceId = optional($provinces->first())->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districtIds = District::select('id')
            ->where('province_id', $provinceId)
            ->pluck('id')
            ->toArray();
        $schools = School::with([
            'schoolReport' => function (HasOne $query) {
                $query->with('report')
                    ->where('report_type', PL02Report::class);
            }
        ])->whereNotIn('school_type', [3, 5])
            ->whereIn('district_id', $districtIds)
            ->get();

        $pl02Reports = $schools->pluck('schoolReport.report')->filter();
        foreach ($pl02Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }
        $report = [];
        $booleanFields = [
            'is_healthcare_system', 'is_approved_plan', 'ql_bahd_cotochuckhong', 'ql_bahd_dinhduonghoply', 'ql_tkctyt_pchiv',
            'ql_tkctyt_pctntt', 'ql_tkctyt_pcdbtn', 'ql_tkctyt_pcsdd', 'ql_tkctyt_attp', 'ql_tkctyt_pctl',
            'ql_tkctyt_pcrb', 'ql_tkctyt_xdth', 'hdtt_bstlph', 'hdtt_goctt', 'bddkcssk_pytth', 'bddkcssk_pytcddk', 'bddkcssk_nvytth',
            'bddkcssk_skb', 'bddkcssk_stdsk', 'bddkcssk_stdth', "bddkcsvc_phong", "bddkcsvc_banghe", "bddkcsvc_bang", "bddkcsvc_chieusang",
            "bddkcsvc_thietbi", "bddkcsvc_nuocanuong", "bddkcsvc_nuocsh", "bddkcsvc_ctvs", "bddkcsvc_tgxlrt", "bddkcsvc_attp", "bdmtttcs_pccssk",
            "bdmtttcs_qdthcs", "bdmtttcs_xdqhtchs", "bdmtttcs_xdqhntgd",
            "dgctyt_tudanhgia", "dgctyt_dgccqql",
            "dgctyt_tudanhgia_xeploai", "dgctyt_dgccqql_xeploai"
        ];
        $pl02Reports = $pl02Reports->toArray();
        $excelData = [];
        if ($pl02Reports) {
            foreach(array_keys($pl02Reports[0])as $array_key) {
                if (!in_array($array_key, $booleanFields)) {
                    $report[$array_key] = array_sum(array_column($pl02Reports, $array_key));
                } else {
                    $report[$array_key] = [
                        1 => count(collect($pl02Reports)->where($array_key, 1)),
                        0 => count(collect($pl02Reports)->where($array_key, 0))
                    ];
                }
            }
        
            $province = Province::find($provinceId);
            $report = (object) $report;
            $sum_ql_dbbt_tong = $report->ql_dbbt_sdd_tong 
                                + $report->ql_dbbt_beophi_tong 
                                + $report->ql_dbbt_brm_tong 
                                + $report->ql_dbbt_bvm_tong 
                                + $report->ql_dbbt_timmach_tong
                                + $report->ql_dbbt_hohap_tong 
                                + $report->ql_dbbt_thankinh_tong 
                                + $report->ql_dbbt_cxk_tong 
                                + $report->ql_dbbt_khac_tong;

            $sum_ql_dbbt_ct     = $report->ql_dbbt_sdd_ct 
                                + $report->ql_dbbt_beophi_ct 
                                + $report->ql_dbbt_brm_ct 
                                + $report->ql_dbbt_bvm_ct 
                                + $report->ql_dbbt_timmach_ct
                                + $report->ql_dbbt_hohap_ct 
                                + $report->ql_dbbt_thankinh_ct 
                                + $report->ql_dbbt_cxk_ct 
                                + $report->ql_dbbt_khac_ct;

            $sum_ql_kdt_tong    = $report->ql_kdt_nknk_tong 
                                + $report->ql_kdt_mat_tong 
                                + $report->ql_kdt_tmh_tong 
                                + $report->ql_kdt_rhm_tong 
                                + $report->ql_kdt_cxk_tong
                                + $report->ql_kdt_thankinh_tong;

            $sum_ql_kdt_ct      = $report->ql_kdt_nknk_ct 
                                + $report->ql_kdt_mat_ct 
                                + $report->ql_kdt_tmh_ct 
                                + $report->ql_kdt_rhm_ct 
                                + $report->ql_kdt_cxk_ct
                                + $report->ql_kdt_thankinh_ct;
            
            $sum_ql_btn_tong    = $report->ql_btn_tieuchay_tong 
                                + $report->ql_btn_tcm_tong 
                                + $report->ql_btn_soi_tong 
                                + $report->ql_btn_quaibi_tong 
                                + $report->ql_btn_cum_tong
                                + $report->ql_btn_rubella_tong;
                                + $report->ql_btn_sxh_tong 
                                + $report->ql_btn_thuydau_tong 
                                + $report->ql_btn_covid_tong
                                + $report->ql_btn_khac_tong;
            $sum_ql_btn_ct      = $report->ql_btn_tieuchay_ct 

                                + $report->ql_btn_tcm_ct 
                                + $report->ql_btn_soi_ct 
                                + $report->ql_btn_quaibi_ct 
                                + $report->ql_btn_cum_ct
                                + $report->ql_btn_rubella_ct;
                                + $report->ql_btn_sxh_ct 
                                + $report->ql_btn_thuydau_ct 
                                + $report->ql_btn_covid_ct
                                + $report->ql_btn_khac_ct;

            $sum_ql_tntt_tong   = $report->ql_tntt_truotnga_tong 
                                + $report->ql_tntt_bong_tong 
                                + $report->ql_tntt_duoinuoc_tong 
                                + $report->ql_tntt_diengiat_tong 
                                + $report->ql_tntt_sucvatcan_tong
                                + $report->ql_tntt_ngodoc_tong 
                                + $report->ql_tntt_hocdivat_tong 
                                + $report->ql_tntt_cvtt_tong 
                                + $report->ql_tntt_bidanh_tong;
                                + $report->ql_tntt_tngt_tong 
                                + $report->ql_tntt_khac_tong;

            $sum_ql_tntt_ct     = $report->ql_tntt_truotnga_ct 
                                + $report->ql_tntt_bong_ct 
                                + $report->ql_tntt_duoinuoc_ct 
                                + $report->ql_tntt_diengiat_ct 
                                + $report->ql_tntt_sucvatcan_ct
                                + $report->ql_tntt_ngodoc_ct 
                                + $report->ql_tntt_hocdivat_ct 
                                + $report->ql_tntt_cvtt_ct 
                                + $report->ql_tntt_bidanh_ct;
                                + $report->ql_tntt_tngt_ct 
                                + $report->ql_tntt_khac_ct;

            $sum_ql_tvsk_tong   = $report->ql_tvsk_ddhl_tong 
                                + $report->ql_tvsk_hdtl_tong 
                                + $report->ql_tvsk_tsl_tong 
                                + $report->ql_tvsk_pcbt_tong 
                                + $report->ql_tvsk_pcbthd_tong
                                + $report->ql_tvsk_sktt_tong;
                                
            $sum_ql_tvsk_ct     = $report->ql_tvsk_ddhl_ct 
                                + $report->ql_tvsk_hdtl_ct 
                                + $report->ql_tvsk_tsl_ct 
                                + $report->ql_tvsk_pcbt_ct 
                                + $report->ql_tvsk_pcbthd_ct
                                + $report->ql_tvsk_sktt_ct;
            $excelData = [
                "A1" => mb_strtoupper($province->name),
                "A8" => '1. Tổng số học sinh: '.$report->student_count.'                                  Tổng số giáo viên: '.$report->teacher_count,
                "A9" => '2. Tổng số lớp học: '.$report->class_count,
                "A10" => '3. Ban chăm sóc sức khoẻ học sinh:       Có: '.$report->is_healthcare_system["1"]. '     Không: '.$report->is_healthcare_system["0"],
                "A11" => '4. Kế hoạch YTTH được phê duyệt:     Có: '.$report->is_healthcare_system["1"]. '     Không: '.$report->is_healthcare_system["0"],
                "A12" => '5. Kinh phí thực hiện: '.$report->cost.' đồng',
                "C16" => $report->ql_dbbt_sdd_tong,
                "D16" => $report->ql_dbbt_sdd_ct,
                "E16" => $report->ql_dbbt_sdd_tong != 0 || $report->ql_dbbt_sdd_tong != null ? round(($report->ql_dbbt_sdd_ct/$report->ql_dbbt_sdd_tong), 2)*100 : null,
                "C17" => $report->ql_dbbt_beophi_tong,
                "D17" => $report->ql_dbbt_beophi_ct,
                "E17" => $report->ql_dbbt_beophi_tong != 0 || $report->ql_dbbt_beophi_tong != null ? round(($report->ql_dbbt_beophi_ct/$report->ql_dbbt_beophi_tong), 2)*100 : null,
                "C18" => $report->ql_dbbt_brm_tong,
                "D18" => $report->ql_dbbt_brm_ct,
                "E18" => $report->ql_dbbt_brm_tong != 0 || $report->ql_dbbt_brm_tong != null ? round(($report->ql_dbbt_brm_ct/$report->ql_dbbt_brm_tong), 2)*100 : null,
                "C19" => $report->ql_dbbt_bvm_tong,
                "D19" => $report->ql_dbbt_bvm_ct,
                "E19" => $report->ql_dbbt_bvm_tong != 0 || $report->ql_dbbt_bvm_tong != null ? round(($report->ql_dbbt_bvm_ct/$report->ql_dbbt_bvm_tong), 2)*100 : null,
                "C20" => $report->ql_dbbt_timmach_tong,
                "D20" => $report->ql_dbbt_timmach_ct,
                "E20" => $report->ql_dbbt_timmach_tong != 0 || $report->ql_dbbt_timmach_tong != null ? round(($report->ql_dbbt_timmach_ct/$report->ql_dbbt_timmach_tong), 2)*100 : null,
                "C21" => $report->ql_dbbt_hohap_tong,
                "D21" => $report->ql_dbbt_hohap_ct,
                "E21" => $report->ql_dbbt_hohap_tong != 0 || $report->ql_dbbt_hohap_tong != null ? round(($report->ql_dbbt_hohap_ct/$report->ql_dbbt_hohap_tong), 2)*100 : null,
                "C22" => $report->ql_dbbt_thankinh_tong,
                "D22" => $report->ql_dbbt_thankinh_ct,
                "E22" => $report->ql_dbbt_thankinh_tong != 0 || $report->ql_dbbt_thankinh_tong != null ? round(($report->ql_dbbt_thankinh_ct/$report->ql_dbbt_thankinh_tong), 2)*100 : null,
                "C23" => $report->ql_dbbt_cxk_tong,
                "D23" => $report->ql_dbbt_cxk_ct,
                "E23" => $report->ql_dbbt_cxk_tong != 0 || $report->ql_dbbt_cxk_tong != null ? round(($report->ql_dbbt_cxk_ct/$report->ql_dbbt_cxk_tong), 2)*100 : null,
                "C24" => $report->ql_dbbt_khac_tong,
                "D24" => $report->ql_dbbt_khac_ct,
                "E24" => $report->ql_dbbt_khac_tong != 0 || $report->ql_dbbt_khac_tong != null ? round(($report->ql_dbbt_khac_ct/$report->ql_dbbt_khac_tong), 2)*100 : null,
                "C25" => $sum_ql_dbbt_tong,
                "D25" => $sum_ql_dbbt_ct,
                "E25" => $sum_ql_dbbt_tong != 0 || $sum_ql_dbbt_tong != null ? round(($sum_ql_dbbt_ct/$sum_ql_dbbt_tong), 2)*100 : null,

                "C30" => $report->ql_kdt_nknk_tong,
                "D30" => $report->ql_kdt_nknk_ct,
                "E30" => $report->ql_kdt_nknk_tong != 0 || $report->ql_kdt_nknk_tong != null ? round(($report->ql_kdt_nknk_ct/$report->ql_kdt_nknk_tong), 2)*100 : null,
                "C31" => $report->ql_kdt_mat_tong,
                "D31" => $report->ql_kdt_mat_ct,
                "E31" => $report->ql_kdt_mat_tong != 0 || $report->ql_kdt_mat_tong != null ? round(($report->ql_kdt_mat_ct/$report->ql_kdt_mat_tong), 2)*100 : null,
                "C32" => $report->ql_kdt_tmh_tong,
                "D32" => $report->ql_kdt_tmh_ct,
                "E32" => $report->ql_kdt_tmh_tong != 0 || $report->ql_kdt_tmh_tong != null ? round(($report->ql_kdt_tmh_ct/$report->ql_kdt_tmh_tong), 2)*100 : null,
                "C33" => $report->ql_kdt_rhm_tong,
                "D33" => $report->ql_kdt_rhm_ct,
                "E33" => $report->ql_kdt_rhm_tong != 0 || $report->ql_kdt_rhm_tong != null ? round(($report->ql_kdt_rhm_ct/$report->ql_kdt_rhm_tong), 2)*100 : null,
                "C34" => $report->ql_kdt_cxk_tong,
                "D34" => $report->ql_kdt_cxk_ct,
                "E34" => $report->ql_kdt_cxk_tong != 0 || $report->ql_kdt_cxk_tong != null ? round(($report->ql_kdt_cxk_ct/$report->ql_kdt_cxk_tong), 2)*100 : null,
                "C35" => $report->ql_kdt_thankinh_tong,
                "D35" => $report->ql_kdt_thankinh_ct,
                "E35" => $report->ql_kdt_thankinh_tong != 0 || $report->ql_kdt_thankinh_tong != null ? round(($report->ql_kdt_thankinh_ct/$report->ql_kdt_thankinh_tong), 2)*100 : null,
                "C36" => $sum_ql_kdt_tong,
                "D36" => $sum_ql_kdt_ct,
                "E36" => $sum_ql_kdt_tong != 0 || $sum_ql_kdt_tong != null ? round(($sum_ql_kdt_ct/$sum_ql_kdt_tong), 2)*100 : null,

                "C41" => $report->ql_btn_tieuchay_tong,
                "D41" => $report->ql_btn_tieuchay_ct,
                "E41" => $report->ql_btn_tieuchay_tong != 0 || $report->ql_btn_tieuchay_tong != null ? round(($report->ql_btn_tieuchay_ct/$report->ql_btn_tieuchay_tong), 2)*100 : null,
                "C42" => $report->ql_btn_tcm_tong,
                "D42" => $report->ql_btn_tcm_ct,
                "E42" => $report->ql_btn_tcm_tong != 0 || $report->ql_btn_tcm_tong != null ? round(($report->ql_btn_tcm_ct/$report->ql_btn_tcm_tong), 2)*100 : null,
                "C43" => $report->ql_btn_soi_tong,
                "D43" => $report->ql_btn_soi_ct,
                "E43" => $report->ql_btn_soi_tong != 0 || $report->ql_btn_soi_tong != null ? round(($report->ql_btn_soi_ct/$report->ql_btn_soi_tong), 2)*100 : null,
                "C44" => $report->ql_btn_quaibi_tong,
                "D44" => $report->ql_btn_quaibi_ct,
                "E44" => $report->ql_btn_quaibi_tong != 0 || $report->ql_btn_quaibi_tong != null ? round(($report->ql_btn_quaibi_ct/$report->ql_btn_quaibi_tong), 2)*100 : null,
                "C45" => $report->ql_btn_cum_tong,
                "D45" => $report->ql_btn_cum_ct,
                "E45" => $report->ql_btn_cum_tong != 0 || $report->ql_btn_cum_tong != null ? round(($report->ql_btn_cum_ct/$report->ql_btn_cum_tong), 2)*100 : null,
                "C46" => $report->ql_btn_rubella_tong,
                "D46" => $report->ql_btn_rubella_ct,
                "E46" => $report->ql_btn_rubella_tong != 0 || $report->ql_btn_rubella_tong != null ? round(($report->ql_btn_rubella_ct/$report->ql_btn_rubella_tong), 2)*100 : null,
                "C47" => $report->ql_btn_sxh_tong,
                "D47" => $report->ql_btn_sxh_ct,
                "E47" => $report->ql_btn_sxh_tong != 0 || $report->ql_btn_sxh_tong != null ? round(($report->ql_btn_sxh_ct/$report->ql_btn_sxh_tong), 2)*100 : null,
                "C48" => $report->ql_btn_thuydau_tong,
                "D48" => $report->ql_btn_thuydau_ct,
                "E48" => $report->ql_btn_thuydau_tong != 0 || $report->ql_btn_thuydau_tong != null ? round(($report->ql_btn_thuydau_ct/$report->ql_btn_thuydau_tong), 2)*100 : null,
                "C49" => $report->ql_btn_covid_tong,
                "D49" => $report->ql_btn_covid_ct,
                "E49" => $report->ql_btn_covid_tong != 0 || $report->ql_btn_covid_tong != null ? round(($report->ql_btn_covid_ct/$report->ql_btn_covid_tong), 2)*100 : null,
                "C50" => $report->ql_btn_khac_tong,
                "D50" => $report->ql_btn_khac_ct,
                "E50" => $report->ql_btn_khac_tong != 0 || $report->ql_btn_khac_tong != null ? round(($report->ql_btn_khac_ct/$report->ql_btn_khac_tong), 2)*100 : null,
                "C51" => $sum_ql_btn_tong,
                "D51" => $sum_ql_btn_ct,
                "E51" => $sum_ql_btn_tong != 0 || $sum_ql_btn_tong != null ? round(($sum_ql_btn_ct/$sum_ql_btn_tong), 2)*100 : null,

                "C56" => $report->ql_tntt_truotnga_tong,
                "D56" => $report->ql_tntt_truotnga_ct,
                "E56" => $report->ql_tntt_truotnga_tong != 0 || $report->ql_tntt_truotnga_tong != null ? round(($report->ql_tntt_truotnga_ct/$report->ql_tntt_truotnga_tong), 2)*100 : null,
                "C57" => $report->ql_tntt_bong_tong,
                "D57" => $report->ql_tntt_bong_ct,
                "E57" => $report->ql_tntt_bong_tong != 0 || $report->ql_tntt_bong_tong != null ? round(($report->ql_tntt_bong_ct/$report->ql_tntt_bong_tong), 2)*100 : null,
                "C58" => $report->ql_tntt_duoinuoc_tong,
                "D58" => $report->ql_tntt_duoinuoc_ct,
                "E58" => $report->ql_tntt_duoinuoc_tong != 0 || $report->ql_tntt_duoinuoc_tong != null ? round(($report->ql_tntt_duoinuoc_ct/$report->ql_tntt_duoinuoc_tong), 2)*100 : null,
                "C59" => $report->ql_tntt_diengiat_tong,
                "D59" => $report->ql_tntt_diengiat_ct,
                "E59" => $report->ql_tntt_diengiat_tong != 0 || $report->ql_tntt_diengiat_tong != null ? round(($report->ql_tntt_diengiat_ct/$report->ql_tntt_diengiat_tong), 2)*100 : null,
                "C60" => $report->ql_tntt_sucvatcan_tong,
                "D60" => $report->ql_tntt_sucvatcan_ct,
                "E60" => $report->ql_tntt_sucvatcan_tong != 0 || $report->ql_tntt_sucvatcan_tong != null ? round(($report->ql_tntt_sucvatcan_ct/$report->ql_tntt_sucvatcan_tong), 2)*100 : null,
                "C61" => $report->ql_tntt_ngodoc_tong,
                "D61" => $report->ql_tntt_ngodoc_ct,
                "E61" => $report->ql_tntt_ngodoc_tong != 0 || $report->ql_tntt_ngodoc_tong != null ? round(($report->ql_tntt_ngodoc_ct/$report->ql_tntt_ngodoc_tong), 2)*100 : null,
                "C62" => $report->ql_tntt_hocdivat_tong,
                "D62" => $report->ql_tntt_hocdivat_ct,
                "E62" => $report->ql_tntt_hocdivat_tong != 0 || $report->ql_tntt_hocdivat_tong != null ? round(($report->ql_tntt_hocdivat_ct/$report->ql_tntt_hocdivat_tong), 2)*100 : null,
                "C63" => $report->ql_tntt_cvtt_tong,
                "D63" => $report->ql_tntt_cvtt_ct,
                "E63" => $report->ql_tntt_cvtt_tong != 0 || $report->ql_tntt_cvtt_tong != null ? round(($report->ql_tntt_cvtt_ct/$report->ql_tntt_cvtt_tong), 2)*100 : null,
                "C64" => $report->ql_tntt_bidanh_tong,
                "D64" => $report->ql_tntt_bidanh_ct,
                "E64" => $report->ql_tntt_bidanh_tong != 0 || $report->ql_tntt_bidanh_tong != null ? round(($report->ql_tntt_bidanh_ct/$report->ql_tntt_bidanh_tong), 2)*100 : null,
                "C65" => $report->ql_tntt_tngt_tong,
                "D65" => $report->ql_tntt_tngt_ct,
                "E65" => $report->ql_tntt_tngt_tong != 0 || $report->ql_tntt_tngt_tong != null ? round(($report->ql_tntt_tngt_ct/$report->ql_tntt_tngt_tong), 2)*100 : null,
                "C66" => $report->ql_tntt_khac_tong,
                "D66" => $report->ql_tntt_khac_ct,
                "E66" => $report->ql_tntt_khac_tong != 0 || $report->ql_tntt_khac_tong != null ? round(($report->ql_tntt_khac_ct/$report->ql_tntt_khac_tong), 2)*100 : null,
                "C67" => $sum_ql_tntt_tong,
                "D67" => $sum_ql_tntt_ct,
                "E67" => $sum_ql_tntt_tong != 0 || $sum_ql_tntt_tong != null ? round(($sum_ql_tntt_ct/$sum_ql_tntt_tong), 2)*100 : null,

                "C73" => $report->ql_tvsk_ddhl_tong,
                "D73" => $report->ql_tvsk_ddhl_ct,
                "E73" => $report->ql_tvsk_ddhl_tong != 0 || $report->ql_tvsk_ddhl_tong != null ? round(($report->ql_tvsk_ddhl_ct/$report->ql_tvsk_ddhl_tong), 2)*100 : null,
                "C74" => $report->ql_tvsk_hdtl_tong,
                "D74" => $report->ql_tvsk_hdtl_ct,
                "E74" => $report->ql_tvsk_hdtl_tong != 0 || $report->ql_tvsk_hdtl_tong != null ? round(($report->ql_tvsk_hdtl_ct/$report->ql_tvsk_hdtl_tong), 2)*100 : null,
                "C75" => $report->ql_tvsk_tsl_tong,
                "D75" => $report->ql_tvsk_tsl_ct,
                "E75" => $report->ql_tvsk_tsl_tong != 0 || $report->ql_tvsk_tsl_tong != null ? round(($report->ql_tvsk_tsl_ct/$report->ql_tvsk_tsl_tong), 2)*100 : null,
                "C76" => $report->ql_tvsk_pcbt_tong,
                "D76" => $report->ql_tvsk_pcbt_ct,
                "E76" => $report->ql_tvsk_pcbt_tong != 0 || $report->ql_tvsk_pcbt_tong != null ? round(($report->ql_tvsk_pcbt_ct/$report->ql_tvsk_pcbt_tong), 2)*100 : null,
                "C77" => $report->ql_tvsk_pcbthd_tong,
                "D77" => $report->ql_tvsk_pcbthd_ct,
                "E77" => $report->ql_tvsk_pcbthd_tong != 0 || $report->ql_tvsk_pcbthd_tong != null ? round(($report->ql_tvsk_pcbthd_ct/$report->ql_tvsk_pcbthd_tong), 2)*100 : null,
                "C78" => $report->ql_tvsk_sktt_tong,
                "D78" => $report->ql_tvsk_sktt_ct,
                "E78" => $report->ql_tvsk_sktt_tong != 0 || $report->ql_tvsk_sktt_tong != null ? round(($report->ql_tvsk_sktt_ct/$report->ql_tvsk_sktt_tong), 2)*100 : null,
                "C79" => $sum_ql_tvsk_tong,
                "D79" => $sum_ql_tvsk_ct,
                "E79" => $sum_ql_tvsk_tong != 0 || $sum_ql_tvsk_tong != null ? round(($sum_ql_tvsk_ct/$sum_ql_tvsk_tong), 2)*100 : null,

                "A84" => "Trường có tổ chức ăn bán trú/nội trú:    Có: ".$report->ql_bahd_cotochuckhong["1"]. "     Không: ".$report->ql_bahd_cotochuckhong["0"],
                "A85" => "Xây dựng thực đơn bảo đảm dinh dưỡng hợp lý:   Có: ".$report->ql_bahd_dinhduonghoply["1"]. "     Không: ".$report->ql_bahd_dinhduonghoply["0"],

                "A101" => '- Tổng số học sinh có sổ theo dõi sức khoẻ: '.$report->ql_stdsk_tong,
                "A102" => '- Số sổ theo dõi sức khỏe học sinh và sổ theo dõi tổng hợp tình trạng sức khỏe học sinh được cập nhật thông tin thường xuyên về sức khỏe: tỷ lệ '.$report->ql_stdsk_tong.'%',
                "A103" => '- Tổng số HS được thông báo về tình trạng SK cho gia đình/người giám hộ '.$report->ql_stdsk_tbgd,

                "C108" => $report->ql_kqtk_vstl_soluot,
                "C109" => $report->ql_kqtk_phunhoachat_soluot,
                "C110" => $report->ql_kqtk_vsktdd_soluot,
                "C111" => $report->ql_kqtk_vsnanb_soluot,
                "C112" => $report->ql_kqtk_vsnn_soluot,
                "C113" => $report->ql_kqtk_tgxlrt_soluot,

                "C119" => $report->ql_tkctyt_pchiv["1"],
                "D119" => $report->ql_tkctyt_pchiv["0"],
                "C120" => $report->ql_tkctyt_pctntt["1"],
                "D120" => $report->ql_tkctyt_pctntt["0"],
                "C121" => $report->ql_tkctyt_pcdbtn["1"],
                "D121" => $report->ql_tkctyt_pcdbtn["0"],
                "C122" => $report->ql_tkctyt_pcsdd["1"],
                "D122" => $report->ql_tkctyt_pcsdd["0"],
                "C123" => $report->ql_tkctyt_attp["1"],
                "D123" => $report->ql_tkctyt_attp["0"],
                "C124" => $report->ql_tkctyt_pctl["1"],
                "D124" => $report->ql_tkctyt_pctl["0"],
                "C125" => $report->ql_tkctyt_pcrb["1"],
                "D125" => $report->ql_tkctyt_pcrb["0"],
                "C126" => $report->ql_tkctyt_xdth["1"],
                "D126" => $report->ql_tkctyt_xdth["0"],

                "C131" => $report->ql_bckqkp_tong,
                "C132" => $report->ql_bckqkp_nsnn,
                "C133" => $report->ql_bckqkp_bhyt,
                "C134" => $report->ql_bckqkp_khac,

                "A140" => "3.1. Biên soạn tài liệu, nội dung truyền thông phù hợp với tình hình dịch bệnh của địa phương:    Có: ".$report->hdtt_bstlph['1']. "     Không: ".$report->hdtt_bstlph['0'],
                "A141" => "3.2. Có góc truyền thông giáo dục sức khỏe:     Có: ".$report->hdtt_goctt['1']. "     Không: ".$report->hdtt_goctt['0'],

                "C144" => $report->hdtt_tctt_pcd_soluot,
                "D144" => $report->hdtt_tctt_pcd_songuoi,
                "C145" => $report->hdtt_tctt_pcnd_soluot,
                "D145" => $report->hdtt_tctt_pcnd_songuoi,
                "C146" => $report->hdtt_tctt_ddhl_soluot,
                "D146" => $report->hdtt_tctt_ddhl_songuoi,
                "C147" => $report->hdtt_tctt_hdtl_soluot,
                "D147" => $report->hdtt_tctt_hdtl_songuoi,
                "C148" => $report->hdtt_tctt_pcthtl_soluot,
                "D148" => $report->hdtt_tctt_pcthtl_songuoi,
                "C149" => $report->hdtt_tctt_pcthrb_soluot,
                "D149" => $report->hdtt_tctt_pcthrb_songuoi,
                "C150" => $report->hdtt_tctt_pcbthd_soluot,
                "D150" => $report->hdtt_tctt_pcbthd_songuoi,
                "C151" => $report->hdtt_tctt_csrm_soluot,
                "D151" => $report->hdtt_tctt_csrm_songuoi,
                "C152" => $report->hdtt_tctt_pccbvm_soluot,
                "D152" => $report->hdtt_tctt_pccbvm_songuoi,
                "C153" => $report->hdtt_tctt_pctntt_soluot,
                "D153" => $report->hdtt_tctt_pctntt_songuoi,

                "C160" => $report->bddkcssk_pytth["1"],
                "D160" => $report->bddkcssk_pytth["0"],
                "C161" => $report->bddkcssk_pytcddk["1"],
                "D161" => $report->bddkcssk_pytcddk["0"],
                "C162" => $report->bddkcssk_nvytth["1"],
                "D162" => $report->bddkcssk_nvytth["0"],
                "C163" => $report->bddkcssk_skb["1"],
                "D163" => $report->bddkcssk_skb["0"],
                "C164" => $report->bddkcssk_stdsk["1"],
                "D164" => $report->bddkcssk_stdsk["0"],
                "C165" => $report->bddkcssk_stdth["1"],
                "D165" => $report->bddkcssk_stdth["0"],

                "C172" => $report->bddkcsvc_phong["1"],
                "D172" => $report->bddkcsvc_phong["0"],
                "C173" => $report->bddkcsvc_banghe["1"],
                "D173" => $report->bddkcsvc_banghe["0"],
                "C174" => $report->bddkcsvc_bang["1"],
                "D174" => $report->bddkcsvc_bang["0"],
                "C175" => $report->bddkcsvc_chieusang["1"],
                "D175" => $report->bddkcsvc_chieusang["0"],
                "C176" => $report->bddkcsvc_thietbi["1"],
                "D176" => $report->bddkcsvc_thietbi["0"],
                "C177" => $report->bddkcsvc_nuocanuong["1"],
                "D177" => $report->bddkcsvc_nuocanuong["0"],
                "C178" => $report->bddkcsvc_nuocsh["1"],
                "D178" => $report->bddkcsvc_nuocsh["0"],
                "C179" => $report->bddkcsvc_ctvs["1"],
                "D179" => $report->bddkcsvc_ctvs["0"],
                "C180" => $report->bddkcsvc_tgxlrt["1"],
                "D180" => $report->bddkcsvc_tgxlrt["0"],
                "C181" => $report->bddkcsvc_attp["1"],
                "D181" => $report->bddkcsvc_attp["0"],

                "C189" => $report->bdmtttcs_pccssk["1"],
                "D189" => $report->bdmtttcs_pccssk["0"],
                "C190" => $report->bdmtttcs_qdthcs["1"],
                "D190" => $report->bdmtttcs_qdthcs["0"],
                "C191" => $report->bdmtttcs_xdqhtchs["1"],
                "D191" => $report->bdmtttcs_xdqhtchs["0"],
                "C192" => $report->bdmtttcs_xdqhntgd["1"],
                "D192" => $report->bdmtttcs_xdqhntgd["0"],
                "A197" => "Tự đánh giá kết quả thực hiện công tác y tế trường học theo mẫu quy định tại Thông tư liên tịch số   /TTLT-BYT-BGDĐT ngày      tháng  5  năm 2016:    Có: ".$report->dgctyt_tudanhgia['1']. "     Không: ".$report->dgctyt_tudanhgia['0'],
            ];
        }
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
        $spreadsheet = $reader->load(public_path('admin/format/export/BaoCaoCongTacYTeTruongHocPhong_PL02.xls'));

        foreach ($excelData as $row => $value) {
            $spreadsheet->getActiveSheet()->setCellValue($row, $value);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');

        // It will be called file.xls
        header('Content-Disposition: attachment; filename="BaoCaoCongTacYTeTruongHocPhong_PL02.xls"');

        // Write file to the browser
        $writer->save('php://output');
    }


    public function pl03DistrictResult()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $provinceId = optional($provinces->first())->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districts = District::with([
            'schools' => function ($query) {
                $query->whereNotIn('school_type', [3, 5]);
            },
            'schools.schoolReport' => function ($query) {
                $query->where('report_type', PLO4Report::class);
            },
            'schools.schoolReport.report'
        ])->where('province_id', $provinceId)
            ->get();

        $booleanFields = [
            'hinhthucdg', 'hinhthucdg', 'xeploai'
        ];
        $reports = [];
        foreach ($districts as $district) {
            $report = [];
            $dataReports = $district->schools->pluck('schoolReport.report');
            $report['district_name'] = $district->name;
            $report['districtId'] = $district->id;
            $report['provinceId'] = $district->province->id;
            foreach ($dataReports as $dataReport) {
                if($dataReport) {
                    foreach ($dataReport->toArray() as $key => $value) {
                        if (array_key_exists($key, $report)) {
                            $newValue = $report[$key];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = ($report[$key][$value] ?? 0) + 1;
                            } else {
                                $newValue = $report[$key] + (is_numeric($value) ? $value : (is_null($value) ? 0 : 1));
                            }
                        } else {
                            $newValue = [];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = 1;
                            } else {
                                $newValue = is_numeric($value) ? $value : (is_null($value) ? 0 : 1);
                            }
                        }
                        $report[$key] = $newValue;
                    }
                }
                
            }
            $reports[] = $report;
        }
        return view('admin.province.report.district.pl03.result', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'reports' => $reports
        ]);
    }

    public function pl03DistrictExport()
    {
        $provinceId = request()->query('province', null);
        $province = Province::find($provinceId);
        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districts = District::with([
            'schools' => function ($query) {
                $query->whereNotIn('school_type', [3, 5]);
            },
            'schools.schoolReport' => function ($query) {
                $query->where('report_type', PLO3Report::class);
            },
            'schools.schoolReport.report'
        ])->where('province_id', $provinceId)
            ->get();

        $booleanFields = [
            'hinhthucdg', 'hinhthucdg', 'xeploai'
        ];

        $reports = [];
        foreach ($districts as $district) {
            $report = [];
            $dataReports = $district->schools->pluck('schoolReport.report');
            $report['district_name'] = $district->name;
            foreach ($dataReports as $dataReport) {
                if($dataReport) {
                    foreach ($dataReport->toArray() as $key => $value) {
                        if (array_key_exists($key, $report)) {
                            $newValue = $report[$key];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = ($report[$key][$value] ?? 0) + 1;
                            } else {
                                $newValue = $report[$key] + (is_numeric($value) ? $value : (is_null($value) ? 0 : 1));
                            }
                        } else {
                            $newValue = [];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = 1;
                            } else {
                                $newValue = is_numeric($value) ? $value : (is_null($value) ? 0 : 1);
                            }
                        }
                        $report[$key] = $newValue;
                    }
                }
                
            }
            $reports[] = $report;
        }

        return (new ExportDistrictPL03($province, $reports))->download('BaoCaoCongTacYTeTruongHoc_PL03.xls');
    }


    public function pl04DistrictResult()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $provinceId = optional($provinces->first())->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districts = District::with([
            'schools' => function ($query) {
                $query->whereNotIn('school_type', [3, 5]);
            },
            'schools.schoolReport' => function ($query) {
                $query->where('report_type', PLO4Report::class);
            },
            'schools.schoolReport.report'
        ])->where('province_id', $provinceId)
            ->get();

        $booleanFields = [
            'hinhthucdg', 'hinhthucdg', 'xeploai'
        ];

        $reports = [];
        foreach ($districts as $district) {
            $report = [];
            $dataReports = $district->schools->pluck('schoolReport.report');
            $report['district_name'] = $district->name;
            $report['districtId'] = $district->id;
            $report['provinceId'] = $district->province->id;
            foreach ($dataReports as $dataReport) {
                if($dataReport) {
                    foreach ($dataReport->toArray() as $key => $value) {
                        if (array_key_exists($key, $report)) {
                            $newValue = $report[$key];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = ($report[$key][$value] ?? 0) + 1;
                            } else {
                                $newValue = $report[$key] + (is_numeric($value) ? $value : (is_null($value) ? 0 : 1));
                            }
                        } else {
                            $newValue = [];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = 1;
                            } else {
                                $newValue = is_numeric($value) ? $value : (is_null($value) ? 0 : 1);
                            }
                        }
                        $report[$key] = $newValue;
                    }
                }
            }
            $reports[] = $report;
        }

        return view('admin.province.report.district.pl04.result', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'reports' => $reports
        ]);
    }

    public function pl04DistrictExport()
    {
        $provinceId = request()->query('province', null);
        $province = Province::find($provinceId);
        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districts = District::with([
            'schools' => function ($query) {
                $query->whereNotIn('school_type', [3, 5]);
            },
            'schools.schoolReport' => function ($query) {
                $query->where('report_type', PLO4Report::class);
            },
            'schools.schoolReport.report'
        ])->where('province_id', $provinceId)
            ->get();

        $booleanFields = [
            'hinhthucdg', 'hinhthucdg', 'xeploai'
        ];

        $reports = [];
        foreach ($districts as $district) {
            $report = [];
            $dataReports = $district->schools->pluck('schoolReport.report');
            $report['district_name'] = $district->name;
            foreach ($dataReports as $dataReport) {
                if($dataReport) {
                    foreach ($dataReport->toArray() as $key => $value) {
                        if (array_key_exists($key, $report)) {
                            $newValue = $report[$key];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = ($report[$key][$value] ?? 0) + 1;
                            } else {
                                $newValue = $report[$key] + (is_numeric($value) ? $value : (is_null($value) ? 0 : 1));
                            }
                        } else {
                            $newValue = [];
                            if (in_array($key, $booleanFields)) {
                                $newValue["{$value}"] = 1;
                            } else {
                                $newValue = is_numeric($value) ? $value : (is_null($value) ? 0 : 1);
                            }
                        }
                        $report[$key] = $newValue;
                    }
                }
                
            }
            $reports[] = $report;
        }

        return (new ExportDistrictPL04($province, $reports))->download('BaoCaoCongTacYTeTruongHoc_PL04.xls');
    }

    public function pl02ThptSend()
    {
        $provinces = Province::all();
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
            }
        } elseif(Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $province = Province::find(request()->query('provinceId', null));
        }
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý báo cáo PL02 sở. Vui lòng kiểm tra lại thông tin');
        }
        $provinceId = $province->id;
        $districtIds = $province->districts->pluck('id');
        $schools = School::with(['district', 'district.province'])->where('id', '>', 0)->whereIn('district_id', $districtIds)->whereIn('school_type', [3, 5])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL02Report::class]
        )->whereHasMorph(
            'agency',
            [Province::class]
        )->where('agency_id', $provinceId)->get();
        $schoolSendIds = $schoolReports->pluck('school_id');
        $schoolSends = collect($schools)->whereIn('id', $schoolSendIds)->values()->all();
        foreach ($schoolSends as $school) {
            $school['pl02_report_id'] = collect($schoolReports)->firstWhere('school_id', $school->id)->report_id;
            $school['report_type'] = 'pl02';
        }
        $schoolNotSends = collect($schools)->whereNotIn('id', $schoolSendIds)->values()->all();

        return view('admin.province.report.thpt.pl02.index', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'schoolSends' => $schoolSends,
            'schoolNotSends' => $schoolNotSends
        ]);
    }

    public function pl02ThptResult()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $provinceId = optional($provinces->first())->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districtIds = District::select('id')
            ->where('province_id', $provinceId)
            ->pluck('id')
            ->toArray();
        $schools = School::with([
            'schoolReport' => function (HasOne $query) {
                $query->with('report')
                    ->where('report_type', PL02Report::class);
            }
        ])->whereIn('school_type', [3, 5])
            ->whereIn('district_id', $districtIds)
            ->get();

        $pl02Reports = $schools->pluck('schoolReport.report')->filter();
        foreach ($pl02Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }
        $report = [];
        $booleanFields = [
            'is_healthcare_system', 'is_approved_plan', 'ql_bahd_cotochuckhong', 'ql_bahd_dinhduonghoply', 'ql_tkctyt_pchiv',
            'ql_tkctyt_pctntt', 'ql_tkctyt_pcdbtn', 'ql_tkctyt_pcsdd', 'ql_tkctyt_attp', 'ql_tkctyt_pctl',
            'ql_tkctyt_pcrb', 'ql_tkctyt_xdth', 'hdtt_bstlph', 'hdtt_goctt', 'bddkcssk_pytth', 'bddkcssk_pytcddk', 'bddkcssk_nvytth',
            'bddkcssk_skb', 'bddkcssk_stdsk', 'bddkcssk_stdth', "bddkcsvc_phong", "bddkcsvc_banghe", "bddkcsvc_bang", "bddkcsvc_chieusang",
            "bddkcsvc_thietbi", "bddkcsvc_nuocanuong", "bddkcsvc_nuocsh", "bddkcsvc_ctvs", "bddkcsvc_tgxlrt", "bddkcsvc_attp", "bdmtttcs_pccssk",
            "bdmtttcs_qdthcs", "bdmtttcs_xdqhtchs", "bdmtttcs_xdqhntgd",
            "dgctyt_tudanhgia", "dgctyt_dgccqql",
            "dgctyt_tudanhgia_xeploai", "dgctyt_dgccqql_xeploai"
        ];
        $pl02Reports = $pl02Reports->toArray();
        foreach(array_keys($pl02Reports[0])as $array_key) {
            if (!in_array($array_key, $booleanFields)) {
                $report[$array_key] = array_sum(array_column($pl02Reports, $array_key));
            } else {
                $report[$array_key] = [
                    1 => count(collect($pl02Reports)->where($array_key, 1)),
                    0 => count(collect($pl02Reports)->where($array_key, 0))
                ];
            }
        }
        
        return view('admin.province.report.thpt.pl02.result', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'report' => $report
        ]);
    }

    public function pl02ThptExport()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $provinceId = optional($provinces->first())->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $districtIds = District::select('id')
            ->where('province_id', $provinceId)
            ->pluck('id')
            ->toArray();
        $schools = School::with([
            'schoolReport' => function (HasOne $query) {
                $query->with('report')
                    ->where('report_type', PL02Report::class);
            }
        ])->whereIn('school_type', [3, 5])
            ->whereIn('district_id', $districtIds)
            ->get();

        $pl02Reports = $schools->pluck('schoolReport.report')->filter();
        foreach ($pl02Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }
        $report = [];
        $booleanFields = [
            'is_healthcare_system', 'is_approved_plan', 'ql_bahd_cotochuckhong', 'ql_bahd_dinhduonghoply', 'ql_tkctyt_pchiv',
            'ql_tkctyt_pctntt', 'ql_tkctyt_pcdbtn', 'ql_tkctyt_pcsdd', 'ql_tkctyt_attp', 'ql_tkctyt_pctl',
            'ql_tkctyt_pcrb', 'ql_tkctyt_xdth', 'hdtt_bstlph', 'hdtt_goctt', 'bddkcssk_pytth', 'bddkcssk_pytcddk', 'bddkcssk_nvytth',
            'bddkcssk_skb', 'bddkcssk_stdsk', 'bddkcssk_stdth', "bddkcsvc_phong", "bddkcsvc_banghe", "bddkcsvc_bang", "bddkcsvc_chieusang",
            "bddkcsvc_thietbi", "bddkcsvc_nuocanuong", "bddkcsvc_nuocsh", "bddkcsvc_ctvs", "bddkcsvc_tgxlrt", "bddkcsvc_attp", "bdmtttcs_pccssk",
            "bdmtttcs_qdthcs", "bdmtttcs_xdqhtchs", "bdmtttcs_xdqhntgd",
            "dgctyt_tudanhgia", "dgctyt_dgccqql",
            "dgctyt_tudanhgia_xeploai", "dgctyt_dgccqql_xeploai"
        ];
        $pl02Reports = $pl02Reports->toArray();
        $excelData = [];
        if ($pl02Reports) {
            foreach(array_keys($pl02Reports[0])as $array_key) {
                if (!in_array($array_key, $booleanFields)) {
                    $report[$array_key] = array_sum(array_column($pl02Reports, $array_key));
                } else {
                    $report[$array_key] = [
                        1 => count(collect($pl02Reports)->where($array_key, 1)),
                        0 => count(collect($pl02Reports)->where($array_key, 0))
                    ];
                }
            }
        
            $province = Province::find($provinceId);
            $report = (object) $report;
            $sum_ql_dbbt_tong = $report->ql_dbbt_sdd_tong 
                                + $report->ql_dbbt_beophi_tong 
                                + $report->ql_dbbt_brm_tong 
                                + $report->ql_dbbt_bvm_tong 
                                + $report->ql_dbbt_timmach_tong
                                + $report->ql_dbbt_hohap_tong 
                                + $report->ql_dbbt_thankinh_tong 
                                + $report->ql_dbbt_cxk_tong 
                                + $report->ql_dbbt_khac_tong;

            $sum_ql_dbbt_ct     = $report->ql_dbbt_sdd_ct 
                                + $report->ql_dbbt_beophi_ct 
                                + $report->ql_dbbt_brm_ct 
                                + $report->ql_dbbt_bvm_ct 
                                + $report->ql_dbbt_timmach_ct
                                + $report->ql_dbbt_hohap_ct 
                                + $report->ql_dbbt_thankinh_ct 
                                + $report->ql_dbbt_cxk_ct 
                                + $report->ql_dbbt_khac_ct;

            $sum_ql_kdt_tong    = $report->ql_kdt_nknk_tong 
                                + $report->ql_kdt_mat_tong 
                                + $report->ql_kdt_tmh_tong 
                                + $report->ql_kdt_rhm_tong 
                                + $report->ql_kdt_cxk_tong
                                + $report->ql_kdt_thankinh_tong;

            $sum_ql_kdt_ct      = $report->ql_kdt_nknk_ct 
                                + $report->ql_kdt_mat_ct 
                                + $report->ql_kdt_tmh_ct 
                                + $report->ql_kdt_rhm_ct 
                                + $report->ql_kdt_cxk_ct
                                + $report->ql_kdt_thankinh_ct;
            
            $sum_ql_btn_tong    = $report->ql_btn_tieuchay_tong 
                                + $report->ql_btn_tcm_tong 
                                + $report->ql_btn_soi_tong 
                                + $report->ql_btn_quaibi_tong 
                                + $report->ql_btn_cum_tong
                                + $report->ql_btn_rubella_tong;
                                + $report->ql_btn_sxh_tong 
                                + $report->ql_btn_thuydau_tong 
                                + $report->ql_btn_covid_tong
                                + $report->ql_btn_khac_tong;
            $sum_ql_btn_ct      = $report->ql_btn_tieuchay_ct 

                                + $report->ql_btn_tcm_ct 
                                + $report->ql_btn_soi_ct 
                                + $report->ql_btn_quaibi_ct 
                                + $report->ql_btn_cum_ct
                                + $report->ql_btn_rubella_ct;
                                + $report->ql_btn_sxh_ct 
                                + $report->ql_btn_thuydau_ct 
                                + $report->ql_btn_covid_ct
                                + $report->ql_btn_khac_ct;

            $sum_ql_tntt_tong   = $report->ql_tntt_truotnga_tong 
                                + $report->ql_tntt_bong_tong 
                                + $report->ql_tntt_duoinuoc_tong 
                                + $report->ql_tntt_diengiat_tong 
                                + $report->ql_tntt_sucvatcan_tong
                                + $report->ql_tntt_ngodoc_tong 
                                + $report->ql_tntt_hocdivat_tong 
                                + $report->ql_tntt_cvtt_tong 
                                + $report->ql_tntt_bidanh_tong;
                                + $report->ql_tntt_tngt_tong 
                                + $report->ql_tntt_khac_tong;

            $sum_ql_tntt_ct     = $report->ql_tntt_truotnga_ct 
                                + $report->ql_tntt_bong_ct 
                                + $report->ql_tntt_duoinuoc_ct 
                                + $report->ql_tntt_diengiat_ct 
                                + $report->ql_tntt_sucvatcan_ct
                                + $report->ql_tntt_ngodoc_ct 
                                + $report->ql_tntt_hocdivat_ct 
                                + $report->ql_tntt_cvtt_ct 
                                + $report->ql_tntt_bidanh_ct;
                                + $report->ql_tntt_tngt_ct 
                                + $report->ql_tntt_khac_ct;

            $sum_ql_tvsk_tong   = $report->ql_tvsk_ddhl_tong 
                                + $report->ql_tvsk_hdtl_tong 
                                + $report->ql_tvsk_tsl_tong 
                                + $report->ql_tvsk_pcbt_tong 
                                + $report->ql_tvsk_pcbthd_tong
                                + $report->ql_tvsk_sktt_tong;
                                
            $sum_ql_tvsk_ct     = $report->ql_tvsk_ddhl_ct 
                                + $report->ql_tvsk_hdtl_ct 
                                + $report->ql_tvsk_tsl_ct 
                                + $report->ql_tvsk_pcbt_ct 
                                + $report->ql_tvsk_pcbthd_ct
                                + $report->ql_tvsk_sktt_ct;
            $excelData = [
                "A1" => mb_strtoupper($province->name),
                "A8" => '1. Tổng số học sinh: '.$report->student_count.'                                  Tổng số giáo viên: '.$report->teacher_count,
                "A9" => '2. Tổng số lớp học: '.$report->class_count,
                "A10" => '3. Ban chăm sóc sức khoẻ học sinh:       Có: '.$report->is_healthcare_system["1"]. '     Không: '.$report->is_healthcare_system["0"],
                "A11" => '4. Kế hoạch YTTH được phê duyệt:     Có: '.$report->is_healthcare_system["1"]. '     Không: '.$report->is_healthcare_system["0"],
                "A12" => '5. Kinh phí thực hiện: '.$report->cost.' đồng',
                "C16" => $report->ql_dbbt_sdd_tong,
                "D16" => $report->ql_dbbt_sdd_ct,
                "E16" => $report->ql_dbbt_sdd_tong != 0 || $report->ql_dbbt_sdd_tong != null ? round(($report->ql_dbbt_sdd_ct/$report->ql_dbbt_sdd_tong), 2)*100 : null,
                "C17" => $report->ql_dbbt_beophi_tong,
                "D17" => $report->ql_dbbt_beophi_ct,
                "E17" => $report->ql_dbbt_beophi_tong != 0 || $report->ql_dbbt_beophi_tong != null ? round(($report->ql_dbbt_beophi_ct/$report->ql_dbbt_beophi_tong), 2)*100 : null,
                "C18" => $report->ql_dbbt_brm_tong,
                "D18" => $report->ql_dbbt_brm_ct,
                "E18" => $report->ql_dbbt_brm_tong != 0 || $report->ql_dbbt_brm_tong != null ? round(($report->ql_dbbt_brm_ct/$report->ql_dbbt_brm_tong), 2)*100 : null,
                "C19" => $report->ql_dbbt_bvm_tong,
                "D19" => $report->ql_dbbt_bvm_ct,
                "E19" => $report->ql_dbbt_bvm_tong != 0 || $report->ql_dbbt_bvm_tong != null ? round(($report->ql_dbbt_bvm_ct/$report->ql_dbbt_bvm_tong), 2)*100 : null,
                "C20" => $report->ql_dbbt_timmach_tong,
                "D20" => $report->ql_dbbt_timmach_ct,
                "E20" => $report->ql_dbbt_timmach_tong != 0 || $report->ql_dbbt_timmach_tong != null ? round(($report->ql_dbbt_timmach_ct/$report->ql_dbbt_timmach_tong), 2)*100 : null,
                "C21" => $report->ql_dbbt_hohap_tong,
                "D21" => $report->ql_dbbt_hohap_ct,
                "E21" => $report->ql_dbbt_hohap_tong != 0 || $report->ql_dbbt_hohap_tong != null ? round(($report->ql_dbbt_hohap_ct/$report->ql_dbbt_hohap_tong), 2)*100 : null,
                "C22" => $report->ql_dbbt_thankinh_tong,
                "D22" => $report->ql_dbbt_thankinh_ct,
                "E22" => $report->ql_dbbt_thankinh_tong != 0 || $report->ql_dbbt_thankinh_tong != null ? round(($report->ql_dbbt_thankinh_ct/$report->ql_dbbt_thankinh_tong), 2)*100 : null,
                "C23" => $report->ql_dbbt_cxk_tong,
                "D23" => $report->ql_dbbt_cxk_ct,
                "E23" => $report->ql_dbbt_cxk_tong != 0 || $report->ql_dbbt_cxk_tong != null ? round(($report->ql_dbbt_cxk_ct/$report->ql_dbbt_cxk_tong), 2)*100 : null,
                "C24" => $report->ql_dbbt_khac_tong,
                "D24" => $report->ql_dbbt_khac_ct,
                "E24" => $report->ql_dbbt_khac_tong != 0 || $report->ql_dbbt_khac_tong != null ? round(($report->ql_dbbt_khac_ct/$report->ql_dbbt_khac_tong), 2)*100 : null,
                "C25" => $sum_ql_dbbt_tong,
                "D25" => $sum_ql_dbbt_ct,
                "E25" => $sum_ql_dbbt_tong != 0 || $sum_ql_dbbt_tong != null ? round(($sum_ql_dbbt_ct/$sum_ql_dbbt_tong), 2)*100 : null,

                "C30" => $report->ql_kdt_nknk_tong,
                "D30" => $report->ql_kdt_nknk_ct,
                "E30" => $report->ql_kdt_nknk_tong != 0 || $report->ql_kdt_nknk_tong != null ? round(($report->ql_kdt_nknk_ct/$report->ql_kdt_nknk_tong), 2)*100 : null,
                "C31" => $report->ql_kdt_mat_tong,
                "D31" => $report->ql_kdt_mat_ct,
                "E31" => $report->ql_kdt_mat_tong != 0 || $report->ql_kdt_mat_tong != null ? round(($report->ql_kdt_mat_ct/$report->ql_kdt_mat_tong), 2)*100 : null,
                "C32" => $report->ql_kdt_tmh_tong,
                "D32" => $report->ql_kdt_tmh_ct,
                "E32" => $report->ql_kdt_tmh_tong != 0 || $report->ql_kdt_tmh_tong != null ? round(($report->ql_kdt_tmh_ct/$report->ql_kdt_tmh_tong), 2)*100 : null,
                "C33" => $report->ql_kdt_rhm_tong,
                "D33" => $report->ql_kdt_rhm_ct,
                "E33" => $report->ql_kdt_rhm_tong != 0 || $report->ql_kdt_rhm_tong != null ? round(($report->ql_kdt_rhm_ct/$report->ql_kdt_rhm_tong), 2)*100 : null,
                "C34" => $report->ql_kdt_cxk_tong,
                "D34" => $report->ql_kdt_cxk_ct,
                "E34" => $report->ql_kdt_cxk_tong != 0 || $report->ql_kdt_cxk_tong != null ? round(($report->ql_kdt_cxk_ct/$report->ql_kdt_cxk_tong), 2)*100 : null,
                "C35" => $report->ql_kdt_thankinh_tong,
                "D35" => $report->ql_kdt_thankinh_ct,
                "E35" => $report->ql_kdt_thankinh_tong != 0 || $report->ql_kdt_thankinh_tong != null ? round(($report->ql_kdt_thankinh_ct/$report->ql_kdt_thankinh_tong), 2)*100 : null,
                "C36" => $sum_ql_kdt_tong,
                "D36" => $sum_ql_kdt_ct,
                "E36" => $sum_ql_kdt_tong != 0 || $sum_ql_kdt_tong != null ? round(($sum_ql_kdt_ct/$sum_ql_kdt_tong), 2)*100 : null,

                "C41" => $report->ql_btn_tieuchay_tong,
                "D41" => $report->ql_btn_tieuchay_ct,
                "E41" => $report->ql_btn_tieuchay_tong != 0 || $report->ql_btn_tieuchay_tong != null ? round(($report->ql_btn_tieuchay_ct/$report->ql_btn_tieuchay_tong), 2)*100 : null,
                "C42" => $report->ql_btn_tcm_tong,
                "D42" => $report->ql_btn_tcm_ct,
                "E42" => $report->ql_btn_tcm_tong != 0 || $report->ql_btn_tcm_tong != null ? round(($report->ql_btn_tcm_ct/$report->ql_btn_tcm_tong), 2)*100 : null,
                "C43" => $report->ql_btn_soi_tong,
                "D43" => $report->ql_btn_soi_ct,
                "E43" => $report->ql_btn_soi_tong != 0 || $report->ql_btn_soi_tong != null ? round(($report->ql_btn_soi_ct/$report->ql_btn_soi_tong), 2)*100 : null,
                "C44" => $report->ql_btn_quaibi_tong,
                "D44" => $report->ql_btn_quaibi_ct,
                "E44" => $report->ql_btn_quaibi_tong != 0 || $report->ql_btn_quaibi_tong != null ? round(($report->ql_btn_quaibi_ct/$report->ql_btn_quaibi_tong), 2)*100 : null,
                "C45" => $report->ql_btn_cum_tong,
                "D45" => $report->ql_btn_cum_ct,
                "E45" => $report->ql_btn_cum_tong != 0 || $report->ql_btn_cum_tong != null ? round(($report->ql_btn_cum_ct/$report->ql_btn_cum_tong), 2)*100 : null,
                "C46" => $report->ql_btn_rubella_tong,
                "D46" => $report->ql_btn_rubella_ct,
                "E46" => $report->ql_btn_rubella_tong != 0 || $report->ql_btn_rubella_tong != null ? round(($report->ql_btn_rubella_ct/$report->ql_btn_rubella_tong), 2)*100 : null,
                "C47" => $report->ql_btn_sxh_tong,
                "D47" => $report->ql_btn_sxh_ct,
                "E47" => $report->ql_btn_sxh_tong != 0 || $report->ql_btn_sxh_tong != null ? round(($report->ql_btn_sxh_ct/$report->ql_btn_sxh_tong), 2)*100 : null,
                "C48" => $report->ql_btn_thuydau_tong,
                "D48" => $report->ql_btn_thuydau_ct,
                "E48" => $report->ql_btn_thuydau_tong != 0 || $report->ql_btn_thuydau_tong != null ? round(($report->ql_btn_thuydau_ct/$report->ql_btn_thuydau_tong), 2)*100 : null,
                "C49" => $report->ql_btn_covid_tong,
                "D49" => $report->ql_btn_covid_ct,
                "E49" => $report->ql_btn_covid_tong != 0 || $report->ql_btn_covid_tong != null ? round(($report->ql_btn_covid_ct/$report->ql_btn_covid_tong), 2)*100 : null,
                "C50" => $report->ql_btn_khac_tong,
                "D50" => $report->ql_btn_khac_ct,
                "E50" => $report->ql_btn_khac_tong != 0 || $report->ql_btn_khac_tong != null ? round(($report->ql_btn_khac_ct/$report->ql_btn_khac_tong), 2)*100 : null,
                "C51" => $sum_ql_btn_tong,
                "D51" => $sum_ql_btn_ct,
                "E51" => $sum_ql_btn_tong != 0 || $sum_ql_btn_tong != null ? round(($sum_ql_btn_ct/$sum_ql_btn_tong), 2)*100 : null,

                "C56" => $report->ql_tntt_truotnga_tong,
                "D56" => $report->ql_tntt_truotnga_ct,
                "E56" => $report->ql_tntt_truotnga_tong != 0 || $report->ql_tntt_truotnga_tong != null ? round(($report->ql_tntt_truotnga_ct/$report->ql_tntt_truotnga_tong), 2)*100 : null,
                "C57" => $report->ql_tntt_bong_tong,
                "D57" => $report->ql_tntt_bong_ct,
                "E57" => $report->ql_tntt_bong_tong != 0 || $report->ql_tntt_bong_tong != null ? round(($report->ql_tntt_bong_ct/$report->ql_tntt_bong_tong), 2)*100 : null,
                "C58" => $report->ql_tntt_duoinuoc_tong,
                "D58" => $report->ql_tntt_duoinuoc_ct,
                "E58" => $report->ql_tntt_duoinuoc_tong != 0 || $report->ql_tntt_duoinuoc_tong != null ? round(($report->ql_tntt_duoinuoc_ct/$report->ql_tntt_duoinuoc_tong), 2)*100 : null,
                "C59" => $report->ql_tntt_diengiat_tong,
                "D59" => $report->ql_tntt_diengiat_ct,
                "E59" => $report->ql_tntt_diengiat_tong != 0 || $report->ql_tntt_diengiat_tong != null ? round(($report->ql_tntt_diengiat_ct/$report->ql_tntt_diengiat_tong), 2)*100 : null,
                "C60" => $report->ql_tntt_sucvatcan_tong,
                "D60" => $report->ql_tntt_sucvatcan_ct,
                "E60" => $report->ql_tntt_sucvatcan_tong != 0 || $report->ql_tntt_sucvatcan_tong != null ? round(($report->ql_tntt_sucvatcan_ct/$report->ql_tntt_sucvatcan_tong), 2)*100 : null,
                "C61" => $report->ql_tntt_ngodoc_tong,
                "D61" => $report->ql_tntt_ngodoc_ct,
                "E61" => $report->ql_tntt_ngodoc_tong != 0 || $report->ql_tntt_ngodoc_tong != null ? round(($report->ql_tntt_ngodoc_ct/$report->ql_tntt_ngodoc_tong), 2)*100 : null,
                "C62" => $report->ql_tntt_hocdivat_tong,
                "D62" => $report->ql_tntt_hocdivat_ct,
                "E62" => $report->ql_tntt_hocdivat_tong != 0 || $report->ql_tntt_hocdivat_tong != null ? round(($report->ql_tntt_hocdivat_ct/$report->ql_tntt_hocdivat_tong), 2)*100 : null,
                "C63" => $report->ql_tntt_cvtt_tong,
                "D63" => $report->ql_tntt_cvtt_ct,
                "E63" => $report->ql_tntt_cvtt_tong != 0 || $report->ql_tntt_cvtt_tong != null ? round(($report->ql_tntt_cvtt_ct/$report->ql_tntt_cvtt_tong), 2)*100 : null,
                "C64" => $report->ql_tntt_bidanh_tong,
                "D64" => $report->ql_tntt_bidanh_ct,
                "E64" => $report->ql_tntt_bidanh_tong != 0 || $report->ql_tntt_bidanh_tong != null ? round(($report->ql_tntt_bidanh_ct/$report->ql_tntt_bidanh_tong), 2)*100 : null,
                "C65" => $report->ql_tntt_tngt_tong,
                "D65" => $report->ql_tntt_tngt_ct,
                "E65" => $report->ql_tntt_tngt_tong != 0 || $report->ql_tntt_tngt_tong != null ? round(($report->ql_tntt_tngt_ct/$report->ql_tntt_tngt_tong), 2)*100 : null,
                "C66" => $report->ql_tntt_khac_tong,
                "D66" => $report->ql_tntt_khac_ct,
                "E66" => $report->ql_tntt_khac_tong != 0 || $report->ql_tntt_khac_tong != null ? round(($report->ql_tntt_khac_ct/$report->ql_tntt_khac_tong), 2)*100 : null,
                "C67" => $sum_ql_tntt_tong,
                "D67" => $sum_ql_tntt_ct,
                "E67" => $sum_ql_tntt_tong != 0 || $sum_ql_tntt_tong != null ? round(($sum_ql_tntt_ct/$sum_ql_tntt_tong), 2)*100 : null,

                "C73" => $report->ql_tvsk_ddhl_tong,
                "D73" => $report->ql_tvsk_ddhl_ct,
                "E73" => $report->ql_tvsk_ddhl_tong != 0 || $report->ql_tvsk_ddhl_tong != null ? round(($report->ql_tvsk_ddhl_ct/$report->ql_tvsk_ddhl_tong), 2)*100 : null,
                "C74" => $report->ql_tvsk_hdtl_tong,
                "D74" => $report->ql_tvsk_hdtl_ct,
                "E74" => $report->ql_tvsk_hdtl_tong != 0 || $report->ql_tvsk_hdtl_tong != null ? round(($report->ql_tvsk_hdtl_ct/$report->ql_tvsk_hdtl_tong), 2)*100 : null,
                "C75" => $report->ql_tvsk_tsl_tong,
                "D75" => $report->ql_tvsk_tsl_ct,
                "E75" => $report->ql_tvsk_tsl_tong != 0 || $report->ql_tvsk_tsl_tong != null ? round(($report->ql_tvsk_tsl_ct/$report->ql_tvsk_tsl_tong), 2)*100 : null,
                "C76" => $report->ql_tvsk_pcbt_tong,
                "D76" => $report->ql_tvsk_pcbt_ct,
                "E76" => $report->ql_tvsk_pcbt_tong != 0 || $report->ql_tvsk_pcbt_tong != null ? round(($report->ql_tvsk_pcbt_ct/$report->ql_tvsk_pcbt_tong), 2)*100 : null,
                "C77" => $report->ql_tvsk_pcbthd_tong,
                "D77" => $report->ql_tvsk_pcbthd_ct,
                "E77" => $report->ql_tvsk_pcbthd_tong != 0 || $report->ql_tvsk_pcbthd_tong != null ? round(($report->ql_tvsk_pcbthd_ct/$report->ql_tvsk_pcbthd_tong), 2)*100 : null,
                "C78" => $report->ql_tvsk_sktt_tong,
                "D78" => $report->ql_tvsk_sktt_ct,
                "E78" => $report->ql_tvsk_sktt_tong != 0 || $report->ql_tvsk_sktt_tong != null ? round(($report->ql_tvsk_sktt_ct/$report->ql_tvsk_sktt_tong), 2)*100 : null,
                "C79" => $sum_ql_tvsk_tong,
                "D79" => $sum_ql_tvsk_ct,
                "E79" => $sum_ql_tvsk_tong != 0 || $sum_ql_tvsk_tong != null ? round(($sum_ql_tvsk_ct/$sum_ql_tvsk_tong), 2)*100 : null,

                "A84" => "Trường có tổ chức ăn bán trú/nội trú:    Có: ".$report->ql_bahd_cotochuckhong["1"]. "     Không: ".$report->ql_bahd_cotochuckhong["0"],
                "A85" => "Xây dựng thực đơn bảo đảm dinh dưỡng hợp lý:   Có: ".$report->ql_bahd_dinhduonghoply["1"]. "     Không: ".$report->ql_bahd_dinhduonghoply["0"],

                "A101" => '- Tổng số học sinh có sổ theo dõi sức khoẻ: '.$report->ql_stdsk_tong,
                "A102" => '- Số sổ theo dõi sức khỏe học sinh và sổ theo dõi tổng hợp tình trạng sức khỏe học sinh được cập nhật thông tin thường xuyên về sức khỏe: tỷ lệ '.$report->ql_stdsk_tong.'%',
                "A103" => '- Tổng số HS được thông báo về tình trạng SK cho gia đình/người giám hộ '.$report->ql_stdsk_tbgd,

                "C108" => $report->ql_kqtk_vstl_soluot,
                "C109" => $report->ql_kqtk_phunhoachat_soluot,
                "C110" => $report->ql_kqtk_vsktdd_soluot,
                "C111" => $report->ql_kqtk_vsnanb_soluot,
                "C112" => $report->ql_kqtk_vsnn_soluot,
                "C113" => $report->ql_kqtk_tgxlrt_soluot,

                "C119" => $report->ql_tkctyt_pchiv["1"],
                "D119" => $report->ql_tkctyt_pchiv["0"],
                "C120" => $report->ql_tkctyt_pctntt["1"],
                "D120" => $report->ql_tkctyt_pctntt["0"],
                "C121" => $report->ql_tkctyt_pcdbtn["1"],
                "D121" => $report->ql_tkctyt_pcdbtn["0"],
                "C122" => $report->ql_tkctyt_pcsdd["1"],
                "D122" => $report->ql_tkctyt_pcsdd["0"],
                "C123" => $report->ql_tkctyt_attp["1"],
                "D123" => $report->ql_tkctyt_attp["0"],
                "C124" => $report->ql_tkctyt_pctl["1"],
                "D124" => $report->ql_tkctyt_pctl["0"],
                "C125" => $report->ql_tkctyt_pcrb["1"],
                "D125" => $report->ql_tkctyt_pcrb["0"],
                "C126" => $report->ql_tkctyt_xdth["1"],
                "D126" => $report->ql_tkctyt_xdth["0"],

                "C131" => $report->ql_bckqkp_tong,
                "C132" => $report->ql_bckqkp_nsnn,
                "C133" => $report->ql_bckqkp_bhyt,
                "C134" => $report->ql_bckqkp_khac,

                "A140" => "3.1. Biên soạn tài liệu, nội dung truyền thông phù hợp với tình hình dịch bệnh của địa phương:    Có: ".$report->hdtt_bstlph['1']. "     Không: ".$report->hdtt_bstlph['0'],
                "A141" => "3.2. Có góc truyền thông giáo dục sức khỏe:     Có: ".$report->hdtt_goctt['1']. "     Không: ".$report->hdtt_goctt['0'],

                "C144" => $report->hdtt_tctt_pcd_soluot,
                "D144" => $report->hdtt_tctt_pcd_songuoi,
                "C145" => $report->hdtt_tctt_pcnd_soluot,
                "D145" => $report->hdtt_tctt_pcnd_songuoi,
                "C146" => $report->hdtt_tctt_ddhl_soluot,
                "D146" => $report->hdtt_tctt_ddhl_songuoi,
                "C147" => $report->hdtt_tctt_hdtl_soluot,
                "D147" => $report->hdtt_tctt_hdtl_songuoi,
                "C148" => $report->hdtt_tctt_pcthtl_soluot,
                "D148" => $report->hdtt_tctt_pcthtl_songuoi,
                "C149" => $report->hdtt_tctt_pcthrb_soluot,
                "D149" => $report->hdtt_tctt_pcthrb_songuoi,
                "C150" => $report->hdtt_tctt_pcbthd_soluot,
                "D150" => $report->hdtt_tctt_pcbthd_songuoi,
                "C151" => $report->hdtt_tctt_csrm_soluot,
                "D151" => $report->hdtt_tctt_csrm_songuoi,
                "C152" => $report->hdtt_tctt_pccbvm_soluot,
                "D152" => $report->hdtt_tctt_pccbvm_songuoi,
                "C153" => $report->hdtt_tctt_pctntt_soluot,
                "D153" => $report->hdtt_tctt_pctntt_songuoi,

                "C160" => $report->bddkcssk_pytth["1"],
                "D160" => $report->bddkcssk_pytth["0"],
                "C161" => $report->bddkcssk_pytcddk["1"],
                "D161" => $report->bddkcssk_pytcddk["0"],
                "C162" => $report->bddkcssk_nvytth["1"],
                "D162" => $report->bddkcssk_nvytth["0"],
                "C163" => $report->bddkcssk_skb["1"],
                "D163" => $report->bddkcssk_skb["0"],
                "C164" => $report->bddkcssk_stdsk["1"],
                "D164" => $report->bddkcssk_stdsk["0"],
                "C165" => $report->bddkcssk_stdth["1"],
                "D165" => $report->bddkcssk_stdth["0"],

                "C172" => $report->bddkcsvc_phong["1"],
                "D172" => $report->bddkcsvc_phong["0"],
                "C173" => $report->bddkcsvc_banghe["1"],
                "D173" => $report->bddkcsvc_banghe["0"],
                "C174" => $report->bddkcsvc_bang["1"],
                "D174" => $report->bddkcsvc_bang["0"],
                "C175" => $report->bddkcsvc_chieusang["1"],
                "D175" => $report->bddkcsvc_chieusang["0"],
                "C176" => $report->bddkcsvc_thietbi["1"],
                "D176" => $report->bddkcsvc_thietbi["0"],
                "C177" => $report->bddkcsvc_nuocanuong["1"],
                "D177" => $report->bddkcsvc_nuocanuong["0"],
                "C178" => $report->bddkcsvc_nuocsh["1"],
                "D178" => $report->bddkcsvc_nuocsh["0"],
                "C179" => $report->bddkcsvc_ctvs["1"],
                "D179" => $report->bddkcsvc_ctvs["0"],
                "C180" => $report->bddkcsvc_tgxlrt["1"],
                "D180" => $report->bddkcsvc_tgxlrt["0"],
                "C181" => $report->bddkcsvc_attp["1"],
                "D181" => $report->bddkcsvc_attp["0"],

                "C189" => $report->bdmtttcs_pccssk["1"],
                "D189" => $report->bdmtttcs_pccssk["0"],
                "C190" => $report->bdmtttcs_qdthcs["1"],
                "D190" => $report->bdmtttcs_qdthcs["0"],
                "C191" => $report->bdmtttcs_xdqhtchs["1"],
                "D191" => $report->bdmtttcs_xdqhtchs["0"],
                "C192" => $report->bdmtttcs_xdqhntgd["1"],
                "D192" => $report->bdmtttcs_xdqhntgd["0"],
                "A197" => "Tự đánh giá kết quả thực hiện công tác y tế trường học theo mẫu quy định tại Thông tư liên tịch số   /TTLT-BYT-BGDĐT ngày      tháng  5  năm 2016:    Có: ".$report->dgctyt_tudanhgia['1']. "     Không: ".$report->dgctyt_tudanhgia['0'],
            ];
        }
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
        $spreadsheet = $reader->load(public_path('admin/format/export/BaoCaoCongTacYTeTruongHocPhong_PL02.xls'));

        foreach ($excelData as $row => $value) {
            $spreadsheet->getActiveSheet()->setCellValue($row, $value);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');

        // It will be called file.xls
        header('Content-Disposition: attachment; filename="BaoCaoCongTacYTeTruongHocPhong_PL02.xls"');

        // Write file to the browser
        $writer->save('php://output');
        
    }

    public function pl04ThptSend()
    {
        $provinces = Province::all();
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
            }
        } elseif(Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $province = Province::find(request()->query('provinceId', null));
        }
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý báo cáo PL04 sở. Vui lòng kiểm tra lại thông tin');
        }
        $provinceId = $province->id;
        $districtIds = $province->districts->pluck('id');
        $schools = School::with(['district', 'district.province'])->where('id', '>', 0)->whereIn('district_id', $districtIds)->whereIn('school_type', [3, 5])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL04Report::class]
        )->whereHasMorph(
            'agency',
            [Province::class]
        )->where('agency_id', $provinceId)->get();
        $schoolSendIds = $schoolReports->pluck('school_id');
        $schoolSends = collect($schools)->whereIn('id', $schoolSendIds)->values()->all();
        foreach ($schoolSends as $school) {
            $school['pl04_report_id'] = collect($schoolReports)->firstWhere('school_id', $school->id)->report_id;
            $school['report_type'] = 'pl04';
        }
        $schoolNotSends = collect($schools)->whereNotIn('id', $schoolSendIds)->values()->all();

        return view('admin.province.report.thpt.pl04.index', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'schoolSends' => $schoolSends,
            'schoolNotSends' => $schoolNotSends
        ]);
    }

    public function pl04ThptResult()
    {
        $provinces = collect();
        $provinceId = request()->query('province', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-province', 'so-gd'])) {
            if (count(Admin::user()->provinces) > 0) {
                $province = Admin::user()->provinces[0];
                $provinceId = $province->id;
            }
        } else if (Admin::user()->inRoles(['administrator', 'customer-support'])) {
            $provinces = Province::all();
            if (empty($provinceId)) {
                $provinceId = optional($provinces->first())->id;
            }
        }

        if (empty($provinceId)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $pl04Reports = PL04Report::with([
            'schoolReport.school'
        ])->whereHas('school', function ($query) use ($provinceId) {
            $query->whereIn('school_type', [3, 5])
                ->whereHas('district', function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                });
        })->get();

        foreach ($pl04Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }

        return view('admin.province.report.thpt.pl04.result', [
            'provinces' => $provinces,
            'provinceId' => $provinceId,
            'reports' => $pl04Reports
        ]);
    }

    public function pl04ThptExport()
    {
        $provinceId = request()->query('province', null);
        $province = Province::find($provinceId);
        if (empty($province)) {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý bảo hiểm y tế. Vui lòng kiểm tra lại thông tin');
        }

        $pl04Reports = PL04Report::with([
            'schoolReport.school'
        ])->whereHas('school', function ($query) use ($provinceId) {
            $query->whereIn('school_type', [3, 5])
                ->whereHas('district', function ($query) use ($provinceId) {
                    $query->where('province_id', $provinceId);
                });
        })->get();

        foreach ($pl04Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }

        return (new ExportThptPL04($province, $pl04Reports))->download('BaoCaoCongTacYTeTHPT_PL04.xls');
    }

    public function addThptSchool($id)
    {
        $province = Province::with('districts')->find($id);
        $selectedDistrictId = request()->query('district', null);
        if($selectedDistrictId){
            $selectedDistrict = collect($province->districts)->where('id', $selectedDistrictId)->values()->first();
        } 
        if(!$selectedDistrictId || !$selectedDistrict){
            $selectedDistrict = $province->districts[0];
        }
        $school_types = School::SCHOOL_THPT_TYPES;
        $data = [
            'school_type' => $school_types
        ];
        $wards = $selectedDistrict->wards ?? [];
        return view('admin.province.thpt_school.add_school', [
            'province' => $province,
            'selectedDistrict' => $selectedDistrict,
            'wards' => $wards,
            'data' => $data
        ]);
    }

    public function postAddThptSchool($id)
    {
        $school_types = School::SCHOOL_THPT_TYPES;
        $data = request()->only([
            'district',
            'ward_id',
            'school_name',
            'school_email',
            'school_phone',
            'school_address',
            'school_type'
        ]);

        $validator = Validator::make($data, [
            'district' => "required",
            'ward_id' => "required",
            'school_name' => "required",
            'school_email' => "required",
            'school_phone' => "required",
            'school_address' => "required",
            'school_type' => ["required", Rule::in(array_keys($school_types))]
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data['district_id'] = $data['district'];
        unset($data['district']);
        /** @var $district District */
        $district = District::with('province', 'wards')->whereHas('wards', function ($query) use ($data) {
            $query->where('id', $data['ward_id']);
        })->find($data['district_id']);
        if (is_null($district)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        $school_code_template = School::getAccountPrefix($district, $data['school_type']);
        $schools = School::where('school_type', $data['school_type'])->where('school_code', 'like', '%'.$school_code_template.'%')->orderBy('id')->get();
        $lasted_code = 0;
        if(count($schools) > 0){
            $last_school = $schools[count($schools) -1];
            $lasted_code = intval(str_replace($school_code_template, '', $last_school->school_code));
        }
        
        $schoolCode = School::getAccountPrefix($district, $data['school_type']) . ($lasted_code + 1);

        //Render Data and create
        $data['school_code'] = $schoolCode;

        DB::beginTransaction();
        try {
            /** @var $school School */
            $school = School::createSchoolWithDefaultUsers($data);
            DB::commit();

            return redirect()->route('province.index', [
                'id' => $school->id
            ])->with('success', 'Thêm trường THPT thành công!');
        } catch (Exception $e) {
            DB::rollback();
            dd($e);
        }
    }

    public function editThptSchool($id, $school_id)
    {
        $school_types = School::SCHOOL_THPT_TYPES;
        $data = [
            'school_type' => $school_types
        ];
        $school = School::with(['district', 'district.province'])->find($school_id);
        if (empty($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        $province = Province::with('districts')->find($id);
        $selectedDistrictId = request()->query('district', null);
        $selectedDistrict = null;
        if($selectedDistrictId){
            $selectedDistrict = collect($province->districts)->where('id', $selectedDistrictId)->values()->first();
        } 
        if(!$selectedDistrictId || !$selectedDistrict){
            $selectedDistrict = $school->district;
        }
        $school_types = School::SCHOOL_THPT_TYPES;
        $data = [
            'school_type' => $school_types
        ];
        $wards = $selectedDistrict->wards ?? [];
        return view('admin.province.thpt_school.edit_school', [
            'province' => $province,
            'selectedDistrict' => $selectedDistrict,
            'data' => $data,
            'school' => $school,
            'wards' => $wards
        ]);
    }

    public function updateThptSchool(Request $request, $id, $school_id)
    {
        $request->validate([
            'district' => 'required',
            'ward_id' => 'required',
            'school_name' => 'required',
            'school_email' => 'nullable|email',
            'school_phone' => 'nullable|numeric|digits_between:8,16',
            'school_address' => 'required',
            'school_type' => 'required'
        ], [
            'district.required' => __('validation.required', ['attribute' => 'quận/huyện']),
            'ward_id.required' => __('validation.required', ['attribute' => 'phuờng/xã']),
            'school_name.required' => __('validation.required', ['attribute' => 'tên trường']),
            'school_email.email' => __('validation.email'),
            'school_phone.numeric' => trans('user.phone_validate'),
            'school_phone.digits_between' => trans('user.phone_digits_between'),
            'school_address.required' => __('validation.required', ['attribute' => 'địa chỉ']),
            'school_type.required' => __('validation.required', ['attribute' => 'loại truờng']),
        ]);

        $data = request()->only([
            'district',
            'ward_id',
            'school_name',
            'school_email',
            'school_phone',
            'school_address',
            'school_type',
        ]);

        /** @var $district District */

        $data['district_id'] = $data['district'];
        unset($data['district']);
        /** @var $district District */
        $district = District::with('province', 'wards')->whereHas('wards', function ($query) use ($data) {
            $query->where('id', $data['ward_id']);
        })->find($data['district_id']);
        if (is_null($district)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $school = School::with('staffs')->find($school_id);
        if ($school->school_type != $data['school_type']) {
            $countSchoolSameType = School::where('district_id', $district->id)->where('school_type', $data['school_type'])->count();
            $schoolCode = School::getAccountPrefix($district, $data['school_type']) . ($countSchoolSameType + 1);
            /*$student_code_template = $schoolCode . 'HS';
            $students = $school->students;
            $index = 0;*/
            $schoolPrefix = School::getAccountPrefix($district, $data['school_type']);
            $currentExist = SchoolStaff::where('staff_code', 'LIKE', $schoolPrefix . '%')->count();
            $staffs = $school->staffs;


            //Render Data and update
            
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

            return redirect()->route('school.thpt', [ 'provinceId' => $district->province->id , 'districtId' => $district->id])->with('success', 'Sửa trường thành công!');
        } catch (Exception $e) {
            DB::rollback();
            dd($e);
        }
    }

    public function exportThptAccount($id)
    {
        $province = Province::with(['districts.users.roles', 'districts.schools.users.roles'])->find($id);
        $schools = [];
        foreach ($province->districts as $district){
            $schools = array_merge($schools,collect($district->schools)->whereIn('school_type', [3, 5])->values()->all());
        }

        return (new ExportProvinceThptAccounts($province, $schools))->download('province_thpt_accounts.xls');
    }
    
    public function importThptSchool($id)
    {
        return view('admin.province.thpt_school.import_thpt_school', [
            'province' => Province::where('id', $id)->first()
        ]);
    }

    public function postImportThptSchool($id)
    {
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

        if (!ImportThptSchool::validateFileHeader($heading)) {
            return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
        }

        $importData = (new ImportThptSchool)->toArray(request()->file('file_upload'))[0];
        $importData = ImportThptSchool::mappingKey($importData);
        $importData = ImportThptSchool::filterData($importData);

        /* Validate Data */
        /* Validate Data in each order */
        $validator = ImportThptSchool::validator($importData);
        if ($validator->fails()) {
            $message = ImportThptSchool::getErrorMessage($validator->errors());
            $validator->getMessageBag()->add('file_upload', $message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            //Import School
            //Set School Code
            $province = Province::with(['districts','districts.wards'])->where('id', $id)->first();

            foreach ($importData as $index => $school) {
                $school = array_filter($school, function ($key) {
                    return strlen($key) > 0;
                }, ARRAY_FILTER_USE_KEY);
                $district = collect($province->districts)->filter(function ($district) use($school){
                    return collect($district->wards)->where('name', $school['ward']);
                })->first(); 
                $ward = collect($district->wards)->filter(function ($ward) use($school){
                    return $ward->name == $school['ward'];
                })->first();

                if (!$ward) {
                    $line = $index + 2;
                    return redirect()->back()->with('error', "Dữ liệu xã tại dòng {$line } - {$school['ward']} không đúng. Vui lòng kiểm tra lại!");
                }
                $countSchoolSameType = School::where('district_id', $district->id)->select('school_type', DB::raw('count(*) as total'))->groupBY('school_type')->pluck('total', 'school_type')->toArray();
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
            return redirect()->route('province.index', ['id' => $province->id])->with('success', 'Nhập dữ liệu trường học thành công!');

        } catch (Exception $e) {
            DB::rollback();
            dd($e);
        }

    }
}