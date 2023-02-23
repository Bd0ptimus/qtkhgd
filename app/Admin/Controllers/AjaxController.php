<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteHistoryItemsEquipment;
use App\Http\Requests\UpdateImportHistoryMedicine;
use App\Http\Requests\UpdateExportHistoryMedicine;
use App\Http\Requests\UpdateImportHistoryEquipment;
use App\Http\Requests\UpdateExportHistoryEquipment;
use App\Http\Requests\DeleteHistoryEquipment;
use App\Http\Requests\DeleteHistoryMedicine;
use App\Http\Requests\DeleteHistoryItemsMedicine;
use App\Models\HistoryMedicine;
use App\Models\MedicalEquipment;
use App\Models\SchoolMedicine;
use App\Models\SchoolMedicineHistory;
use App\Models\SchoolMedicineVariant;
use App\Models\HistoryEquipment;
use App\Models\SchoolMedicalEquipment;
use App\Models\SchoolMedicalEquipmentHistory;
use App\Models\SchoolMedicalEquipmentVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Admin\Permission;
use App\Admin\Admin;
use App\Admin\Helpers\ListHelper;
use App\Admin\Models\AdminUser;
use App\Admin\Services\TeacherPlanService;
use App\Models\LessonSample;
use App\Models\School;
use App\Models\SchoolBranch;
use App\Models\SchoolClass;
use App\Models\Medicine;
use App\Models\Target;
use App\Models\TeacherLesson;
use Illuminate\Support\Facades\Response;
use App\Admin\Helpers\Utils;
class AjaxController extends Controller
{   

   
    protected $teacherPlanService;
    protected $utilsHelper;

    public function __construct(
        TeacherPlanService $teacherPlanService,
        Utils $utilsHelper       
    )
    {
        $this->teacherPlanService = $teacherPlanService;
        $this->utilsHelper = $utilsHelper;
    }

    public function ajaxGetClassesByBranch(Request $request) {
        $branch = SchoolBranch::where('id', $request->branchId)->with('classes')->first();
        return \json_encode($branch->classes);
    }

    public function getStudentByClass(Request $request) {
        $class = SchoolClass::where('id', $request->classId)->with(['students'])->first();
        return json_encode($class->students);
    }


    public function updateImportHistoryMedicine(UpdateImportHistoryMedicine $request) {
        $request->validated();
        $data = $request->all();
        return $this->handleDataHistoryMedicine($data);
    }

    public function updateExportHistoryMedicine(UpdateExportHistoryMedicine $request) {
        $request->validated();
        $data = $request->all();
        return $this->handleDataHistoryMedicine($data);
    }

    protected function handleDataHistoryMedicine($data) {
        try{
            DB::beginTransaction();
            $historyMedicine = HistoryMedicine::where('id', $data['id'])->first();
            $offset = $data['amount'] - $historyMedicine->amount;
            $schoolMedicine = SchoolMedicine::where([
                'medicine_id' => $historyMedicine->medicine_id,
                'school_id' => $data['school_id'],
                'school_branch_id' => $data['school_branch_id']
            ])->first();

            if ($data['amount'] >= 0){
                $schoolMedicineVariant = SchoolMedicineVariant::where([
                    'school_medicine_id' => $schoolMedicine->id,
                    'expired_at' => $historyMedicine->expired_at
                ])->first();

                $updateHistoryMedicine = [
                    'amount' => $data['amount'],
                    'price' => $data['price'],
                    'expired_at' => $data['expired_at'],
                ];

                $updateMedicineVariant = [
                    'amount' => $data['amount'],
                    'expired_at' => $data['expired_at']
                ];
            }else{
                $schoolMedicineVariant = SchoolMedicineVariant::where([
                    'school_medicine_id' => $schoolMedicine->id,
                ])->first();

                $updateHistoryMedicine = [
                    'amount' => $data['amount'],
                    'use_guide' => $data['use_guide'],
                ];

                $updateMedicineVariant = [
                    'amount' => $data['amount'],
                ];
            }
            $historyMedicine->update($updateHistoryMedicine);

            $schoolMedicineVariant->update($updateMedicineVariant);

            $schoolMedicine->update([
                'amount' => $schoolMedicine->amount + $offset
            ]);

            DB::commit();
        } catch(Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
        return json_encode([
            'status' => true
        ]);
    }


    public function updateImportHistoryEquipment(UpdateImportHistoryEquipment $request) {
        $request->validated();
        $data = $request->all();
        return $this->handleDataHistoryEquipment($data);
    }

    public function updateExportHistoryEquipment(UpdateExportHistoryEquipment $request) {
        $request->validated();
        $data = $request->all();
        return $this->handleDataHistoryEquipment($data);
    }

    protected function handleDataHistoryEquipment($data) {
        try{
            DB::beginTransaction();
            $historyEquipment = HistoryEquipment::where('id', $data['id'])->first();
            $offset = $data['amount'] - $historyEquipment->amount;
            $schoolEquipment = SchoolMedicalEquipment::where([
                'medical_equipment_id' => $historyEquipment->medical_equipment_id,
                'school_id' => $data['school_id'],
                'school_branch_id' => $data['school_branch_id']
            ])->first();

            $schoolMedicineEquipmentVariant = SchoolMedicalEquipmentVariant::where([
                'school_medical_equipment_id' => $schoolEquipment->id,
                'status' => $historyEquipment->status
            ])->first();

            if ($data['amount'] >= 0){

                $updateHistoryEquipment = [
                    'amount' => $data['amount'],
                    'price' => $data['price'],
                ];

                $updateEquipmentVariant = [
                    'amount' => $data['amount'],
                ];
            }else{

                $updateHistoryEquipment = [
                    'amount' => $data['amount'],
                ];

                $updateEquipmentVariant = [
                    'amount' => $data['amount'],
                ];
            }

            $historyEquipment->update($updateHistoryEquipment);

            if (isset($schoolMedicineEquipmentVariant)) $schoolMedicineEquipmentVariant->update($updateEquipmentVariant);
            else SchoolMedicalEquipmentVariant::create([
                'school_medical_equipment_id' => $schoolEquipment->id,
                'amount' => $offset,
                'status' => $historyEquipment->status
            ]);
            $schoolEquipment->update([
                'amount' => $schoolEquipment->amount + $offset
            ]);

            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            dd($e);
        }
        return json_encode([
            'status' => true
        ]);
    }

    public function deleteHistoryMedicine(DeleteHistoryMedicine $request) {
        $request->validated();
        $data = $request->all();
        try{
            DB::beginTransaction();
            $this->handleDataDeleteMedicine($data);
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            dd($e);
        }
        return json_encode([
            'status' => true
        ]);
    }

    protected function handleDataDeleteMedicine ($data) {
        $historyMedicine = HistoryMedicine::where('id', $data['id'])->first();
        $offset = $historyMedicine->amount;

        $schoolMedicine = SchoolMedicine::where([
            'medicine_id' => $historyMedicine->medicine_id,
            'school_id' => $data['school_id'],
            'school_branch_id' => $data['school_branch_id']
        ])->first();

        

        if($schoolMedicine) {
            $schoolMedicineVariant = SchoolMedicineVariant::where([
                'school_medicine_id' => $schoolMedicine->id,
                'expired_at' => $historyMedicine->expired_at
            ])->first();

            $historyMedicine->delete();

            $schoolMedicine->update([
                'amount' => $schoolMedicine->amount - $offset
            ]);
            if (isset($schoolMedicineVariant)){
                $schoolMedicineVariant->update([
                    'amount' => $schoolMedicineVariant->amout - $offset
                ]);
            }
        } else $historyMedicine->delete();
        
    }

    public function deleteHistoryEquipment(DeleteHistoryEquipment $request) {
        $request->validated();
        $data = $request->all();
        try{
            DB::beginTransaction();
            $this->handleDataDeleteEquipment($data);
            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
            dd($e);
        }
        return json_encode([
            'status' => true
        ]);
    }

    protected function handleDataDeleteEquipment ($data) {
        $historyEquipment = HistoryEquipment::where('id', $data['id'])->first();
        $offset = $historyEquipment->amount;

        $schoolEquipment = SchoolMedicalEquipment::where([
            'medical_equipment_id' => $historyEquipment->medical_equipment_id,
            'school_id' => $data['school_id'],
            'school_branch_id' => $data['school_branch_id']
        ])->first();

        if($schoolEquipment) {
            $schoolMedicineEquipmentVariant = SchoolMedicalEquipmentVariant::where([
                'school_medical_equipment_id' => $schoolEquipment->id,
                'status' => $historyEquipment->status
            ])->first();
    
            $historyEquipment->delete();
    
            $schoolEquipment->update([
                'amount' => $schoolEquipment->amount - $offset
            ]);
    
            if (isset($schoolMedicineEquipmentVariant)){
                $schoolMedicineEquipmentVariant->update([
                    'amount' => $schoolMedicineEquipmentVariant->amout - $offset
                ]);
            }
        } else $historyEquipment->delete();
    }

    public function deleteLocalMedicine(Request $request){
        try {
            DB::beginTransaction();
            Medicine::where('id', $request->id)->delete();
            SchoolMedicine::where(['medicine_id' => $request->id, 'school_id' => $request->school_id])->delete();
            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
            dd($e);
        }
        return json_encode([
            'status' => true
        ]);
    }

    public function deleteLocalEquipment(Request $request){
        try {
            DB::beginTransaction();
            MedicalEquipment::where('id', $request->id)->delete();
            SchoolMedicalEquipment::where(['medical_equipment_id' => $request->id, 'school_id' => $request->school_id])->delete();
            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
            dd($e);
        }
        return json_encode([
            'status' => true
        ]);
    }

    public function deleteHistoryItemsMedicine(DeleteHistoryItemsMedicine $request){
        $request->validated();
        $data = $request->all();
        try {
            DB::beginTransaction();
            $schoolHistory = SchoolMedicineHistory::where('id', $data['id'])->first();
            $schoolId = $schoolHistory->school_id;
            $schoolBranchId = $schoolHistory->school_branch_id;
            $schoolHistory->delete();
            $historyMedicines = HistoryMedicine::where('history_id', $data['id'])->get();
            foreach ($historyMedicines as $item) {
                $this->handleDataDeleteMedicine([
                    'id' => $item->id,
                    'school_id' => $schoolId,
                    'school_branch_id' => $schoolBranchId,
                ]);
            }
            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
            dd($e);
        }
        return json_encode([
            'status' => true
        ]);
    }

    public function deleteHistoryItemsEquipment(DeleteHistoryItemsEquipment $request){
        $request->validated();
        $data = $request->all();
        try {
            DB::beginTransaction();
            $schoolHistory = SchoolMedicalEquipmentHistory::where('id', $data['id'])->first();
            $schoolId = $schoolHistory->school_id;
            $schoolBranchId = $schoolHistory->school_branch_id;
            $schoolHistory->delete();
            $historyEquipment = HistoryEquipment::where('history_id', $data['id'])->get();
            foreach ($historyEquipment as $item) {
                $this->handleDataDeleteEquipment([
                    'id' => $item->id,
                    'school_id' => $schoolId,
                    'school_branch_id' => $schoolBranchId,
                ]);
            }
            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
            dd($e);
        }
        return json_encode([
            'status' => true
        ]);
    }

    public function systemTargetById() {
        $targetId = (request('id'));
        return json_encode(Target::find($targetId));
    }

    public function getLessonById() {
        $id = (request('id'));
        $lessonSample=LessonSample::where('id',$id)->with(['subject','homesheet','exercise','attachments'])->first();        
        $view = view('admin.lesson_sample.modal_contents', [
            'lessonSample' => $lessonSample,
            'onlyView' => request()->has('view'),
        ])->render();
        return Response::json(['status' => 200, 'view' => $view]);
    }

    public function getTeacherLessonById() {
        //route('school.staff.teacher_lesson.add_review', ['school_id' => $school->id, 'staffId' => $teacherPlan->staff_id,'planId' => $teacherPlan->id, 'lessonId' => $lesson->id
        $id = (request('id'));
        $lesson = TeacherLesson::with(['plan', 'plan.staff', 'plan.staff.school'])->find($id);
        $teacherPlan = $lesson->plan;
        $staff = $teacherPlan->staff;
        $school = $staff->school;
        switch($school->school_type) {
            case SCHOOL_TH:
                $path = '/public/templates/staff/plan/tieu_hoc.html';
                $lessonSample = LessonSample::where('title', 'like', "%$lesson->ten_bai_hoc%")->first();    
                $view = 'admin.school.staff.plan.tieu_hoc'; break;
            case SCHOOL_THCS:
                $path = '/public/templates/staff/plan/thcs.html';
                $lessonSample = LessonSample::where('title', 'like', "%$lesson->ten_bai_hoc%")->first();
                $view = 'admin.school.staff.plan.thcs'; break;
            case SCHOOL_THPT:
                $path = '/public/templates/staff/plan/thpt.html';
                $lessonSample = LessonSample::where('title', 'like', "%$lesson->ten_bai_hoc%")->first();
                $view = 'admin.school.staff.plan.thpt'; break;
            case SCHOOL_MN:
                $path = '/public/templates/staff/plan/mam_non.html';
                $lessonSample = LessonSample::where('title', 'like', "%$lesson->noi_dung%")->first();
                $view = 'admin.school.staff.plan.mam_non'; break;
            default:
                $path = '/public/templates/staff/plan/thpt.html';
                $lessonSample = LessonSample::where('title', 'like', "%$lesson->ten_bai_hoc%")->first();
                $view = 'admin.school.staff.plan.thpt'; break;
        }

        $planOwner = AdminUser::where('username', $teacherPlan->staff->staff_code)->first();
        $lessonTemplate = file_get_contents(base_path($path), false);
        $sampleLessons = LessonSample::where([
            'grade' => $teacherPlan->grade,
            'subject_id' => $teacherPlan->subject_id
        ])->get();
        $monthYears = ListHelper::listMonth();
        $view = view('admin.school.staff.lesson.modals.modal_lesson_detail', [
            'lesson' => $lesson,
            'canManage' => $this->teacherPlanService->checkIfCanMange($staff->id),
            'staff' => $staff,
            'teacherPlan' => $teacherPlan,
            'school' => $school,
            'lessonTemplate' => $lessonTemplate,
            'planOwner' => $planOwner,
            'sampleLessons' => $sampleLessons,
            'onlyView' => request()->has('view'),
            'lessonSample' => $lessonSample ?? null
        ])->render();
        return Response::json(['status' => 200, 'view' => $view]);
    }


    public function getLessonSampleById(Request $request) {
        $sampleLesson = LessonSample::find($request->id);
        $view = view('admin.school.staff.lesson.modals.modal_sample_lesson', [
            'sampleLesson' => $sampleLesson,
        ])->render();
        return Response::json(['status' => 200, 'view' => $view]);
    }
}
