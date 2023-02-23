<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Repositories\NotificationAdminRepository;
use App\Admin\Repositories\RegularGroupPlanRepository;
use App\Models\RegularGroup;
use App\Admin\Repositories\RegularGroupRepository;
use App\Models\GroupPlanHistory;
use App\Models\GroupSubjectPlan;
use App\Models\NotificationAdmin;
use App\Models\RegularGroupPlan;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

use Exception;

class RegularGroupPlanService
{
    protected $rgPlanRepo;
    protected $notificationAdminRepo;
    protected $regularGroupRepo;

    public function __construct(RegularGroupPlanRepository $repo,
    RegularGroupRepository $regularGroupRepo,  
    NotificationAdminRepository $notificationAdminRepo)
    {
        $this->rgPlanRepo = $repo;
        $this->notificationAdminRepo = $notificationAdminRepo;
        $this->regularGroupRepo = $regularGroupRepo;
    }

    public function index()
    {
        return ['regularGroups' => $this->rgRepo->all(['*'], ['subjects'])];
    }

    public function findById($planId)
    {
        return $this->rgPlanRepo->findById($planId, ['*'], ['histories', 'subjectPlans', 'group']);
    }

    public function download($planId, $fileNameEx)
    {
        Settings::setOutputEscapingEnabled(true);
        Settings::setCompatibility(false);
        $plan = $this->findById($planId);
        $filesTitle = "Kế hoạch tổ chuyên môn {$plan->group->name}";
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultFontName('Times New Roman');
        $section = $phpWord->addSection();
        $titleElement = $section->addText($filesTitle);
        $titleElement->setFontStyle(array('bold'=>true, 'size'=>14, 'font'=>'Times New Roman' , 'align'=>'center'));
        $titleElement->setParagraphStyle(array('align'=>'center'));

        $plan->content = $this->removeTagInsideTag($plan->content,['p'],['td']);
        Html::addHtml($section, $plan->content);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filesTitle.'.docx');
        return response()->download($filesTitle.'.docx')->deleteFileAfterSend(true);
    }

    //Remove child tag bên trong parent tag. Data bên trong child tag vẫn sẽ được bảo toàn
    //$parentTags, $childTags: array 
    //function sẽ loại bỏ child tag ra khỏi parent tag theo thứ tự
    public function removeTagInsideTag($html, $childTags, $parentTags)
    {
        if (sizeOf($childTags) != sizeOf($parentTags)) return; //size của $childTags phải tương đương với size $parentTags
        foreach ($childTags as $key => $childTag) {
            $replaceArr = ['<' . $childTag . '>', '</' . $childTag . '>'];
            $dom = new \DOMDocument();
            @$dom->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

            $length = $dom->getElementsByTagName($parentTags[$key])->length;
            for ($i = 0; $i < $length; $i++) {
                $element = $dom->getElementsByTagName($parentTags[$key])->item($i);
                $type = $this->innerHTML($element);
                if (strpos($type, '<' . $childTag . '>') !== false) {
                    Log::info('Before replace : ' . $type);
                    $newType = str_replace($replaceArr, '', $type);
                    Log::info('After replace : ' . $newType);
                    $childElement = $element->removeChild($element->childNodes->item(1));
                    $newElement = $dom->createDocumentFragment();
                    $newElement->appendXML($newType);

                    $element->appendChild($newElement);
                    $dom->saveXML();
                }
            }
            $html = $dom->saveHTML($dom->ownerDocument);
            $html = str_replace([
                '<body>',
                '</body>',
                '<html>',
                '</html>',
                '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'
            ], '', $html);
            $html = str_replace('<br>', '<br/>', $html);
        }

        return $html;
    }

    function innerHTML($node)
    {
        return implode(array_map(
            [$node->ownerDocument, "saveHTML"],
            iterator_to_array($node->childNodes)
        ));
    }

    public function create($school, $data)
    {
        DB::beginTransaction();
        try {
            if ($school->school_type == SCHOOL_MN) {
                $data['content'] = json_encode($data['content']);
            }
            $data['status'] = PLAN_PENDING;
            $groupPlan = RegularGroupPlan::create($data);
            if (isset($data['subjectPlans'])) {
                foreach ($data['subjectPlans'] as $subject => $content) {
                    GroupSubjectPlan::create([
                        'group_plan_id' => $groupPlan->id,
                        'subject_id' => $subject,
                        'content' => json_encode($content)
                    ]);
                }
            }
            DB::commit();
            return $groupPlan;
        } catch (Exception $ex) {
            DB::rollBack();
            if (env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create group plan]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
    }

    public function update($school, $planId, $data)
    {
        DB::beginTransaction();
        try {
            unset($data['_token']);
            unset($data['teacherPlanLessons_length']);
            if (isset($data['subjectPlans'])) {
                $subjectPlans = $data['subjectPlans'];
                unset($data['subjectPlans']);
            }

            if ($school->school_type == SCHOOL_MN) {
                if (isset($data['content'])) $data['content'] = json_encode($data['content']);
            }

            $groupPlan = RegularGroupPlan::where('id', $planId)->first();
            $groupPlan->update($data);

            if (isset($subjectPlans)) {
                GroupSubjectPlan::where([
                    'group_plan_id' => $groupPlan->id,
                ])->whereNotIn('subject_id', array_keys($subjectPlans))->delete();

                foreach ($subjectPlans as $subject => $content) {
                    $content = array_values($content);
                    $groupSubjectPlan = GroupSubjectPlan::where([
                        'group_plan_id' => $groupPlan->id,
                        'subject_id' => $subject,
                    ])->first();

                    if ($groupSubjectPlan) $groupSubjectPlan->update([
                        'content' => json_encode($content)
                    ]);
                    else GroupSubjectPlan::create([
                        'group_plan_id' => $groupPlan->id,
                        'subject_id' => $subject,
                        'content' => json_encode($content)
                    ]);
                }
            } else {
                GroupSubjectPlan::where([
                    'group_plan_id' => $groupPlan->id,
                ])->delete();
            }
            DB::commit();
            return $groupPlan;
        } catch (Exception $ex) {
            DB::rollback();
            if (env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[update group plan]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
    }

    public function addHistory($plan, $content)
    {
        $history = GroupPlanHistory::create([
            'group_plan_id' => $plan->id,
            'notes' => $content,
            'status' => $plan->status
        ]);
    }

    public function findPendingPlanBySchool($schoolId)
    {
        return $this->rgPlanRepo->findPendingPlanBySchool($schoolId);
    }

    public function sendNotificationToPlanOwner($owner, $title, $content)
    {
        $this->notificationAdminRepo->create([
            'user_id' => $owner->id,
            'title' => $title,
            'content' => $content,
            'type' => NotificationAdmin::TYPE["group_plan"],
            'data' => json_encode([])
        ]);
    }

    public function findApprovedPlanByDistrict($districtId, $params=[])
    {
        return $this->rgPlanRepo->findApprovedPlanByDistrictWithConditions($districtId, $params);
    }
}

