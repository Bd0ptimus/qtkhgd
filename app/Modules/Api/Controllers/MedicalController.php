<?php
#app/Modules/Api/Controllers/HealthController.php
namespace App\Modules\Api\Controllers;

use App\Admin\Models\AdminUser;
use App\Models\HealthAbnormal;
use App\Models\MedicalEquipment;
use App\Models\Medicine;
use App\Models\Student;
use App\Models\StudentHealthIndex;
use App\Models\StudentSpecialistTest;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalController extends ApiController
{
    /**
     * Get All students
     * @return JsonResponse
     */
    public function getStudents()
    {
        /** @var $auth AdminUser */
        $auth = auth()->user()->load(['students.class']);
        return $this->respSuccess($auth->students);
    }

    /**
     * Get student detail
     * @return JsonResponse
     */
    public function getStudentDetail($id)
    {
        $student = Student::with(['class', 'schoolBranch'])->find($id);
        /** @var $auth AdminUser */
        return $this->respSuccess($student);
    }

    /**
     * Get All medicines by student ID
     * @return JsonResponse
     */
    public function getMedicinesByStudent($id)
    {
        /** @var $auth AdminUser */
        $student = Student::with(['school'])->find($id);
        $school = optional($student)->school;
        $medicines = collect();
        if (!empty($school)) {
            $medicines = Medicine::where('school_id', $school->id)
                ->get();
        }

        return $this->respSuccess($medicines);
    }

    /**
     * Get All equipment by student ID
     * @return JsonResponse
     */
    public function getEquipmentByStudent($id)
    {
        /** @var $auth AdminUser */
        $student = Student::with(['school'])->find($id);
        $school = optional($student)->school;
        $equipments = collect();
        if (!empty($school)) {
            $equipments = MedicalEquipment::where('school_id', $school->id)
                ->get();
        }

        return $this->respSuccess($equipments);
    }

    /**
     * Get student health index
     * @return JsonResponse
     */
    public function getStudentHealthIndex($id)
    {
        $studentHealthIndex = StudentHealthIndex::whereHas('student', function (Builder $query) use ($id) {
            $query->where('id', $id);
        })->orderBy('month', 'desc')->paginate(12);

        return $this->respSuccess($studentHealthIndex);
    }

    /**
     * Get student health abnormal
     * @return JsonResponse
     */
    public function getStudentHealthAbnormal($id)
    {
        $healthAbnormal = HealthAbnormal::whereHasMorph(
            'object',
            Student::class,
            function (Builder $query) use ($id) {
                $query->where('id', $id);
            }
        )->select('id', 'date')->orderBy('date', 'desc')->paginate(12);

        return $this->respSuccess($healthAbnormal);
    }

    /**
     * Get student health abnormal detail
     * @return JsonResponse
     */
    public function getStudentDetailHealthAbnormal($id, $abnormalId)
    {
        $healthAbnormal = HealthAbnormal::whereHasMorph(
            'object',
            Student::class,
            function (Builder $query) use ($id) {
                $query->where('id', $id);
            }
        )->with([
            'health_abnormal_medicine.school_medicine_history.items.medicine',
            'health_abnormal_equipment.school_medical_equipment_history.items.equipment'
        ])->where('id', $abnormalId)->first();

        return $this->respSuccess($healthAbnormal);
    }

    /**
     * ADd student health abnormal
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addStudentHealthAbnormal($id, Request $request)
    {
        $data = $request->input();
//        $medicines = $request->input('medicines');
//        $equipments = $request->input('equipments');
        $student = Student::find($id);
        if (empty($student)) {
            return $this->respError('Dont have data');
        }
//        $school = $student->school;
//        $schoolBranch = $student->schoolBranch;
        $data['object_id'] = $id;
        $data['object_type'] = Student::class;
        $data['edit_by'] = HealthAbnormal::EDIT_BY['parent'];
        unset($data['medicines']);
        unset($data['equipments']);
        DB::beginTransaction();
//        $processUserId = auth()->user()->id;
        try {
            $healthAbnormal = HealthAbnormal::create($data);
//            if (!empty($medicines)) {
//                $schoolMedicineHistory = $healthAbnormal->exportMedicinesCreateHeathAbnormal($medicines, null, $school->id, $schoolBranch->id, $request->date, $student->fullname ?? null, 'Dữ liệu sức khỏe bất thường - ' . $request->initial_diagnosis, $processUserId);
//                if (empty($schoolMedicineHistory)) {
//                    throw new Exception();
//                }
//                HealthAbnormalMedicine::create([
//                    'case_id' => $healthAbnormal->id,
//                    'school_medicine_history_id' => $schoolMedicineHistory->id
//                ]);
//            }
//            if (!empty($equipments)) {
//                $schoolEquipmentHistory = $healthAbnormal->exportMedicalEquipmentCreateHeathAbnormal($equipments, null, $school->id, $schoolBranch->id, $request->date, $student->fullname ?? null, 'Dữ liệu sức khỏe bất thường - ' . $request->initial_diagnosis, $processUserId);
//                if (empty($schoolEquipmentHistory)) {
//                    throw new Exception();
//                }
//                HealthAbnormalEquipment::create([
//                    'case_id' => $healthAbnormal->id,
//                    'school_medical_equipment_history_id' => $schoolEquipmentHistory->id
//                ]);
//            }
            DB::commit();
            return $this->respSuccess($healthAbnormal);
        } catch (Exception $e) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($e);
            return $this->respError("Error");
        }
    }


    /**
     * Get student special test
     * @return JsonResponse
     */
    public function getStudentSpecialTest($id)
    {
        $studentSpecialTest = StudentSpecialistTest::whereHas('student', function (Builder $query) use ($id) {
            $query->where('id', $id);
        })->orderBy('date', 'desc')->paginate(12);

        return $this->respSuccess($studentSpecialTest);
    }
}