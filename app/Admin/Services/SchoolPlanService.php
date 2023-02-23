<?php

namespace App\Admin\Services;

use App\Admin\Repositories\NotificationAdminRepository;
use App\Admin\Repositories\SchoolPlanRepository;
use App\Models\NotificationAdmin;
use App\Models\SchoolPlanDetail;
use App\Models\SchoolPlanHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class SchoolPlanService
{
    protected $schoolPlanRepo;
    protected $notificationAdminRepo;

    public function __construct(SchoolPlanRepository $repo, NotificationAdminRepository $notificationAdminRepo)
    {
        $this->schoolPlanRepo = $repo;
        $this->notificationAdminRepo = $notificationAdminRepo;
    }

    public function get($schoolId) {
        return $this->schoolPlanRepo->findBySchoolId($schoolId);
    }

    public function findById($planId) {
        return $this->schoolPlanRepo->findById($planId, ['*'], ['gradeDetails', 'school']);
    }

    public function findPendingPlanBySchool($schoolId) {
        return $this->schoolPlanRepo->findPendingPlanBySchool($schoolId);
    }

    public function findPendingPlanByDistrict($districtId) {
        return $this->schoolPlanRepo->findPendingPlanByDistrict($districtId);
    }

    public function findApprovedPlanByDistrict($districtId, $params=[]) {
        return $this->schoolPlanRepo->findApprovedPlanByDistrictWithConditions($districtId, $params);
    }

    public function create($data) {
        DB::beginTransaction();
        try{
            $data['status'] = PLAN_PENDING;
            $newPlan = $this->schoolPlanRepo->create($data);
            if(isset($data['gradeDetails'])) {
                foreach($data['gradeDetails'] as $grade => $content) {
                    SchoolPlanDetail::create([
                        'school_plan_id' => $newPlan->id,
                        'grade' => $grade,
                        'thoi_gian_to_chuc_theo_tuan' => $content['thoi_gian_to_chuc_theo_tuan'],
                        'ke_hoach_cac_mon' => isset($content['ke_hoach_cac_mon']) ? json_encode($content['ke_hoach_cac_mon']) : null
                    ]);
                }
            }
            DB::commit();
            return $newPlan;
        } catch (Exception $e) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($e);
        }
       
    }

    public function update($planId, $data) {
        DB::beginTransaction();
        try{
            $gradeDetails = isset($data['gradeDetails']) ? $data['gradeDetails'] : [];
            unset($data['_token']);
            unset($data['gradeDetails']);

            $this->schoolPlanRepo->update($planId, $data);

            if(!empty($gradeDetails)) {
                foreach($gradeDetails as $grade => $content) {
                    SchoolPlanDetail::where([
                        'school_plan_id' => $planId,
                        'grade' => $grade
                    ])->update([
                        'thoi_gian_to_chuc_theo_tuan' => $content['thoi_gian_to_chuc_theo_tuan'],
                        'ke_hoach_cac_mon' => isset($content['ke_hoach_cac_mon']) ? json_encode($content['ke_hoach_cac_mon']) : null
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($e);
        }
    }
    
    public function download($planId, $fileNameEx) {
        Settings::setOutputEscapingEnabled(true);
        Settings::setCompatibility(false);
        $plan = $this->findById($planId);
        
        $phpWord = new PhpWord();
        
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultFontName('Times New Roman');
        $section = $phpWord->addSection();
        if($plan->school->school_type == 1) {

            Html::addHtml($section, "<strong>I. Căn cứ xây dựng kế hoạch</strong><br/>", false, true);
            Html::addHtml($section, $plan->can_cu_1, false, true);

            Html::addHtml($section, "<br/><strong>II. Điều kiện thực hiện chương trình năm học</strong>", false, true);
            Html::addHtml($section, "<strong>1. Đặc điểm tình hình kinh tế, văn hóa, xã hội địa phương</strong><br/>", false, true);
            Html::addHtml($section, $plan->dac_diem_ktvhxh_21, false, true);

            Html::addHtml($section, "<br/><strong>2. Đặc điểm tình hình nhà trường năm học</strong>", false, true);
            Html::addHtml($section, "<strong>2.1. Đặc điểm học sinh của trườn</strong><br/>", false, true);
            Html::addHtml($section, $plan->dac_diem_hocsinh_221, false, true);

            Html::addHtml($section, "<br/><strong>2.2. Tình hình đội ngũ giáo viên, nhân viên, cán bộ quản lý</strong><br/>", false, true);
            Html::addHtml($section, $plan->tinh_hinh_nhan_vien_222, false, true);
            
            Html::addHtml($section, "<br/><strong>2.3. Cơ sở vật chất, thiết bị dạy học; điểm trường, lớp ghép; cơ sở vật chất thực hiện bán trú, nội trú</strong><br/>", false, true);
            Html::addHtml($section, $plan->co_so_vat_chat_23, false, true);

            Html::addHtml($section, "<br/><strong> III. Mục tiêu giáo dục năm học</strong>", false, true);
            Html::addHtml($section, "<strong>1. Mục tiêu chung</strong><br/>", false, true);
            Html::addHtml($section, $plan->mtnh_chung_31, false, true);

            Html::addHtml($section, "<br/><strong>2. Chỉ tiêu cụ thể</strong><br/>", false, true);
            Html::addHtml($section, $plan->mtnh_cu_the_32, false, true);

            Html::addHtml($section, "<br/><strong>IV. Tổ chức các môn học và hoạt động giáo dục trong năm học</strong>", false, true);
            Html::addHtml($section, "<strong>1. Phân phối thời lượng các môn học và hoạt động giáo dục</strong><br/>", false, true);
            Html::addHtml($section, $plan->phan_phoi_thoi_luong_41, false, true);

            Html::addHtml($section, "<strong>2. Các hoạt động giáo dục tập thể và theo nhu cầu người học</strong><br/>", false, true);
            Html::addHtml($section, "<strong>2.1. Các hoạt động giáo dục tập thể thực hiện trong năm học</strong><br/>", false, true);
            Html::addHtml($section, $plan->hd_tap_the_421, false, true);

            Html::addHtml($section, "<br/><strong>2.2. Tổ chức hoạt động cho học sinh sau giờ học chính thức trong ngày, theo nhu cầu người học và trong thời gian bán trú tại trường</strong><br/>", false, true);
            Html::addHtml($section, $plan->hd_ngoai_gio_422, false, true);
            
            Html::addHtml($section, "<br/><strong>3. Tổ chức thực hiện kế hoạch giáo dục đối với các điểm trường</strong><br/>", false, true);
            Html::addHtml($section, $plan->to_chuc_thuc_hien_diem_truong_43, false, true);

            Html::addHtml($section, "<br/><strong>4. Khung thời gian thực hiện chương trình năm học và kế hoạch dạy học các môn học. hoạt động giáo dục</strong><br/>", false, true);
            Html::addHtml($section, $plan->khung_thoi_gian_44, false, true);

            Html::addHtml($section, "<br/><strong>V. Giải pháp thực hiện</strong><br/>", false, true);
            Html::addHtml($section, $plan->giai_phap_thuc_hien_5, false, true);

            Html::addHtml($section, "<br/><strong>VI. Tổ chức thực hiện</strong><br/>", false, true);
            Html::addHtml($section, $plan->to_chuc_thuc_hien_6, false, true);
        } else {
            $plan->content = $this->removeHeadTag($plan->content);
            $removeStyles= ['font-size','font-family'];
            foreach($removeStyles as $removeStyle){
                $plan->content=$this->removeStyle($plan->content, $removeStyle);            
            }
            Html::addHtml($section, $plan->content, false, true);
        }
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        ob_clean();
        $objWriter->save($fileNameEx);
        return response()->download($fileNameEx)->deleteFileAfterSend(true);
    }

    private function removeStyle($html, $style){
        $fPos = stripos($html, $style);
        if($fPos == false){
            return $html;
        }
        
        for($i = $fPos ; $i <strlen($html);$i++){
            if(($html[$i]) == ';'){
                for($y = $fPos; $y<$i+1; $y++){
                    $html[$y]=' ';
                }
                return $this->removeStyle($html,$style);
            }
        }
    }

    private function removeHeadTag($html){
        $styleFound = [];
        foreach (["<head>",'</head>'] as $word) {
            $pos = strpos($html, $word);
            if($pos) {
                array_push($styleFound, $pos);
            }
        }
        if(!empty($styleFound)) $html=substr_replace($html, '', $styleFound[0], $styleFound[1]-$styleFound[0]+7);
        $html=str_replace([
            "<!DOCTYPE html>", 
            "<!-- Generated by PHPWord -->", 
            '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'
        ],"",$html);
        return $html;
    }

    public function sendNotificationToPlanOwner($owner, $title, $content) {
        $this->notificationAdminRepo->create([
            'user_id' => $owner->id,
            'title' => $title,
            'content' => $content,
            'type' => NotificationAdmin::TYPE["school_plan"],
            'data' => json_encode([
            ])
        ]);
    }

    public function addHistory($plan, $content) {
        $history = SchoolPlanHistory::create([
            'school_plan_id' => $plan->id,
            'notes' => $content,
            'status' => $plan->status
        ]);
    }
}