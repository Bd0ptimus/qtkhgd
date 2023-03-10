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

            Html::addHtml($section, "<strong>I. C??n c??? x??y d???ng k??? ho???ch</strong><br/>", false, true);
            Html::addHtml($section, $plan->can_cu_1, false, true);

            Html::addHtml($section, "<br/><strong>II. ??i???u ki???n th???c hi???n ch????ng tr??nh n??m h???c</strong>", false, true);
            Html::addHtml($section, "<strong>1. ?????c ??i???m t??nh h??nh kinh t???, v??n h??a, x?? h???i ?????a ph????ng</strong><br/>", false, true);
            Html::addHtml($section, $plan->dac_diem_ktvhxh_21, false, true);

            Html::addHtml($section, "<br/><strong>2. ?????c ??i???m t??nh h??nh nh?? tr?????ng n??m h???c</strong>", false, true);
            Html::addHtml($section, "<strong>2.1. ?????c ??i???m h???c sinh c???a tr?????n</strong><br/>", false, true);
            Html::addHtml($section, $plan->dac_diem_hocsinh_221, false, true);

            Html::addHtml($section, "<br/><strong>2.2. T??nh h??nh ?????i ng?? gi??o vi??n, nh??n vi??n, c??n b??? qu???n l??</strong><br/>", false, true);
            Html::addHtml($section, $plan->tinh_hinh_nhan_vien_222, false, true);
            
            Html::addHtml($section, "<br/><strong>2.3. C?? s??? v???t ch???t, thi???t b??? d???y h???c; ??i???m tr?????ng, l???p gh??p; c?? s??? v???t ch???t th???c hi???n b??n tr??, n???i tr??</strong><br/>", false, true);
            Html::addHtml($section, $plan->co_so_vat_chat_23, false, true);

            Html::addHtml($section, "<br/><strong> III. M???c ti??u gi??o d???c n??m h???c</strong>", false, true);
            Html::addHtml($section, "<strong>1. M???c ti??u chung</strong><br/>", false, true);
            Html::addHtml($section, $plan->mtnh_chung_31, false, true);

            Html::addHtml($section, "<br/><strong>2. Ch??? ti??u c??? th???</strong><br/>", false, true);
            Html::addHtml($section, $plan->mtnh_cu_the_32, false, true);

            Html::addHtml($section, "<br/><strong>IV. T??? ch???c c??c m??n h???c v?? ho???t ?????ng gi??o d???c trong n??m h???c</strong>", false, true);
            Html::addHtml($section, "<strong>1. Ph??n ph???i th???i l?????ng c??c m??n h???c v?? ho???t ?????ng gi??o d???c</strong><br/>", false, true);
            Html::addHtml($section, $plan->phan_phoi_thoi_luong_41, false, true);

            Html::addHtml($section, "<strong>2. C??c ho???t ?????ng gi??o d???c t???p th??? v?? theo nhu c???u ng?????i h???c</strong><br/>", false, true);
            Html::addHtml($section, "<strong>2.1. C??c ho???t ?????ng gi??o d???c t???p th??? th???c hi???n trong n??m h???c</strong><br/>", false, true);
            Html::addHtml($section, $plan->hd_tap_the_421, false, true);

            Html::addHtml($section, "<br/><strong>2.2. T??? ch???c ho???t ?????ng cho h???c sinh sau gi??? h???c ch??nh th???c trong ng??y, theo nhu c???u ng?????i h???c v?? trong th???i gian b??n tr?? t???i tr?????ng</strong><br/>", false, true);
            Html::addHtml($section, $plan->hd_ngoai_gio_422, false, true);
            
            Html::addHtml($section, "<br/><strong>3. T??? ch???c th???c hi???n k??? ho???ch gi??o d???c ?????i v???i c??c ??i???m tr?????ng</strong><br/>", false, true);
            Html::addHtml($section, $plan->to_chuc_thuc_hien_diem_truong_43, false, true);

            Html::addHtml($section, "<br/><strong>4. Khung th???i gian th???c hi???n ch????ng tr??nh n??m h???c v?? k??? ho???ch d???y h???c c??c m??n h???c. ho???t ?????ng gi??o d???c</strong><br/>", false, true);
            Html::addHtml($section, $plan->khung_thoi_gian_44, false, true);

            Html::addHtml($section, "<br/><strong>V. Gi???i ph??p th???c hi???n</strong><br/>", false, true);
            Html::addHtml($section, $plan->giai_phap_thuc_hien_5, false, true);

            Html::addHtml($section, "<br/><strong>VI. T??? ch???c th???c hi???n</strong><br/>", false, true);
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