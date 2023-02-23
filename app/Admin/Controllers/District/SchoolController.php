<?php

namespace App\Admin\Controllers\District;

use App\Admin\Admin;
use App\Admin\Models\Exports\ExportCheckRoomDistrict;
use App\Admin\Models\Exports\ExportPL03District;
use App\Admin\Models\Exports\ExportPL04District;
use App\Http\Controllers\Controller;
use App\Models\CheckRoom;
use App\Models\District;
use App\Models\PL02Report;
use App\Models\PL03Report;
use App\Models\PL04Report;
use App\Models\Province;
use App\Models\School;
use App\Models\SchoolReport;

class SchoolController extends Controller
{

    public function mb_substr_replace($str, $repl, $start, $length = null)
    {
        preg_match_all('/./us', $str, $ar);
        preg_match_all('/./us', $repl, $rar);
        $length = is_int($length) ? $length : utf8_strlen($str);
        array_splice($ar[0], $start, $length, $rar[0]);
        return implode($ar[0]);
    }


    public function pl02Send()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl02.send', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL02Report::class]
        )->whereHasMorph(
            'agency',
            [District::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();

        $schoolSendIds = $schoolReports->pluck('school_id');
        $schoolSends = School::with(['district', 'district.province'])->whereIn('id', $schoolSendIds)->get();
        foreach ($schoolSends as $school) {
            $school['pl02_report_id'] = collect($schoolReports)->firstWhere('school_id', $school->id)->report_id;
            $school['report_type'] = 'pl02';
        }
        $schoolNotSends = collect($schools)->whereNotIn('id', $schoolSendIds)->values()->all();
        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.district.report.district.pl02.index', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'schoolSends' => $schoolSends,
            'schoolNotSends' => $schoolNotSends
        ]);
    }

    public function pl02Result()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl02.result', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL02Report::class]
        )->whereHasMorph(
            'agency',
            [District::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();

        $pl02Reports = PL02Report::whereIn('id', $schoolReports->pluck('report_id'))->get();
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
        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.district.report.district.pl02.result', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'report' => $report
        ]);
    }

    public function pl02Export()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl02.export', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL02Report::class]
        )->whereHasMorph(
            'agency',
            [District::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();

        $pl02Reports = PL02Report::whereIn('id', $schoolReports->pluck('report_id'))->get();
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
        
            $district = District::find($districtId);
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
                "A1" => mb_strtoupper($district->name),
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

    public function pl03Send()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl03.send', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId)->where('school_type', 6);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL03Report::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();
        $schoolSendIds = $schoolReports->pluck('school_id');
        $schoolSends = School::with(['district', 'district.province'])->whereIn('id', $schoolSendIds)->get();
        foreach ($schoolSends as $school) {
            $school['pl03_report_id'] = collect($schoolReports)->firstWhere('school_id', $school->id)->report_id;
            $school['report_type'] = 'pl03';
        }
        $schoolNotSends = collect($schools)->whereNotIn('id', $schoolSendIds)->values()->all();
        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.district.report.district.pl03.index', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'schoolSends' => $schoolSends,
            'schoolNotSends' => $schoolNotSends
        ]);
    }

    public function pl03Result()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl03.result', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL03Report::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();
        $pl03Reports = PL03Report::with('school')->whereIn('id', $schoolReports->pluck('report_id'))->get();
        foreach ($pl03Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }

        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.district.report.district.pl03.result', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'reports' => $pl03Reports
        ]);
    }

    public function pl03Export()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl03.export', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL03Report::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();
        $district = District::find($districtId)->name;
        $data = PL03Report::with('school')->whereIn('id', $schoolReports->pluck('report_id'))->get();
        return (new ExportPL03District($district, $data))->download('BaoCaoCongTacYTeTruongHoc_PL03.xls');
    }

    public function pl04Send()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl04.send', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId)->where('school_type', '!=', 6);;
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL04Report::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();
        $schoolSendIds = $schoolReports->pluck('school_id');
        $schoolSends = School::with(['district', 'district.province'])->whereIn('id', $schoolSendIds)->get();
        foreach ($schoolSends as $school) {
            $school['pl04_report_id'] = collect($schoolReports)->firstWhere('school_id', $school->id)->report_id;
            $school['report_type'] = 'pl04';
        }
        $schoolNotSends = collect($schools)->whereNotIn('id', $schoolSendIds)->values()->all();
        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.district.report.district.pl04.index', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'schoolSends' => $schoolSends,
            'schoolNotSends' => $schoolNotSends
        ]);
    }

    public function pl04Result()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl04.result', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL04Report::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();
        $pl04Reports = PL04Report::with('school')->whereIn('id', $schoolReports->pluck('report_id'))->get();
        foreach ($pl04Reports as $report) {
            unset($report['id'], $report['report_date'], $report['school_id'], $report['created_at'], $report['updated_at']);
        }

        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.district.report.district.pl04.result', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'reports' => $pl04Reports
        ]);
    }

    public function pl04Export()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);

        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.pl04.export', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        if ($districtId) $query = $query->where('district_id', $districtId);
        $schools = $query->with(['district', 'district.province'])->get();
        $school_ids = $schools->pluck('id');
        $schoolReports = SchoolReport::whereHasMorph(
            'report',
            [PL04Report::class]
        )->where('agency_id', $districtId)->whereIn('school_id', $school_ids)->get();
        $district = District::find($districtId)->name;
        $data = PL04Report::with('school')->whereIn('id', $schoolReports->pluck('report_id'))->get();
        return (new ExportPL04District($district, $data))->download('BaoCaoCongTacYTeTruongHoc_PL04.xls');
    }

    public function roomAnalytics()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.room.analytics', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if ($districtId) $query = $query->where('district_id', $districtId);
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        $schools = $query->with(['branches'])->get();
        $schoolBranchIds = [];
        foreach ($schools as $school) {
            $branches = $school->branches()->pluck('id');
            foreach ($branches as $branch) {
                $schoolBranchIds[] = $branch;
            }
        }
        $checkRooms = CheckRoom::whereIn('school_branch_id', $schoolBranchIds)->orderBy('check_date', 'DESC')->get();
        $checkRooms = collect($checkRooms)->unique('school_branch_id')->values()->all();
        $data = [];
        foreach ($checkRooms as $checkRoom) {
            $row = new \stdClass();
            $checkRoomDetails = collect($checkRoom->checkRoomDetails);
            $row->branch_name = $checkRoom->schoolBranch->branch_name;
            $row->so_lop_hoc = count($checkRoom->schoolBranch->classes);
            $row->phong_hoc_so_luong = count($checkRoomDetails);
            $row->phong_hoc_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 1));
            $row->phong_hoc_chua_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 0));
            $row->do_roi_dat_chuan = count($checkRoomDetails->where('do_roi', 1));
            $row->do_roi_chua_dat_chuan = count($checkRoomDetails->where('do_roi', 0));
            $row->do_chieu_sang_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 1));
            $row->do_chieu_sang_chua_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 0));
            $data[] = $row;
        }
        $provinces = Province::with('districts')->get();
        return $this->renderView('admin.district.csvc.room_analytics', [
            'schools' => $schools,
            'provinces' => $provinces,
            'districts' => $districts,
            'provinceId' => $provinceId,
            'districtId' => $districtId,
            'data' => $data
        ]);
    }

    public function roomAnalyticsExport()
    {
        $districtId = request()->query('districtId', null);
        $provinceId = request()->query('provinceId', null);
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd'])) {
            if (count(Admin::user()->districts) > 0) {
                $district = Admin::user()->districts[0];
                if (empty($districtId) || $districtId != $district->id || $provinceId != $district->province->id) {
                    return redirect()->route('district.room.analytics', ['districtId' => $district->id, 'provinceId' => $district->province->id]);
                }
                $districtId = $district->id;
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
        if ($districtId) $query = $query->where('district_id', $districtId);
        if (Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd', 'administrator'])) {
            $query->whereNotIn('school_type', [3, 5]);
        }
        $schools = $query->with(['branches'])->get();
        $schoolBranchIds = [];
        foreach ($schools as $school) {
            $branches = $school->branches()->pluck('id');
            foreach ($branches as $branch) {
                $schoolBranchIds[] = $branch;
            }
        }
        $checkRooms = CheckRoom::whereIn('school_branch_id', $schoolBranchIds)->orderBy('check_date', 'DESC')->get();
        $checkRooms = collect($checkRooms)->unique('school_branch_id')->values()->all();
        $data = [];
        foreach ($checkRooms as $checkRoom) {
            $row = new \stdClass();
            $checkRoomDetails = collect($checkRoom->checkRoomDetails);
            $row->branch_name = $checkRoom->schoolBranch->branch_name;
            $row->so_lop_hoc = count($checkRoom->schoolBranch->classes);
            $row->phong_hoc_so_luong = count($checkRoomDetails);
            $row->phong_hoc_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 1));
            $row->phong_hoc_chua_dat_chuan = count($checkRoomDetails->where('danh_gia_chung', 0));
            $row->do_roi_dat_chuan = count($checkRoomDetails->where('do_roi', 1));
            $row->do_roi_chua_dat_chuan = count($checkRoomDetails->where('do_roi', 0));
            $row->do_chieu_sang_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 1));
            $row->do_chieu_sang_chua_dat_chuan = count($checkRoomDetails->where('do_chieu_sang', 0));
            $data[] = $row;
        }
        $district = District::find($districtId)->name;
        return (new ExportCheckRoomDistrict($district, $data))->download('csvc-danh-gia-phong-hoc-quan.xls');
    }

    public function pl02Detail($id)
    {
        $report = PL02Report::with('school')->find($id);

        return $this->renderView('admin.district.report.pl02.edit', [
            'report' => $report
        ]);
    }

    public function pl03Detail($id)
    {
        $report = PL03Report::with('school')->find($id);
        return $this->renderView('admin.district.report.pl03.edit', [
            'report' => $report
        ]);
    }

    public function pl04Detail($id)
    {
        $report = PL04Report::with('school')->find($id);

        return $this->renderView('admin.district.report.pl04.edit', [
            'report' => $report
        ]);
    }
}