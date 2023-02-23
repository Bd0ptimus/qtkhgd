<?php

namespace App\Admin\Services;

use App\Admin\Repositories\SubjectRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SubjectService
{
    protected $subjectRepo;

    public function __construct(SubjectRepository $repo)
    {
        $this->subjectRepo = $repo;
    }

    public function index() {
        return [ 'subjects' => $this->subjectRepo->allBySystem() ];
    }

    public function allGroupByGrade() {
        $result = [];
        $subjects = $this->subjectRepo->allBySystem();

        foreach($subjects as $subject) {
            foreach($subject->grades as $gradeSubject) {
                $result[$gradeSubject->grade][] = $subject;
            }
        }
        
        return $result;
    }

    public function getAllBySchool($schoolId) {
        return  $this->subjectRepo->findBySchoolId($schoolId);
    }

    public function getBySchoolGroupByGrade($schoolId) {
        $data = $this->subjectRepo->allWithSchoolDetails($schoolId);
        $school = $data['school'];
        $subjects = $data['subjects'];
        $result = [];

        foreach($subjects as $subject) {
            foreach($subject->grades as $gradeSubject) {
                if(in_array($gradeSubject->grade, $school->getSchoolGrades())) $result[$gradeSubject->grade][] = $subject;
            }
        }
        return $result;
    }

    public function getSubjectByGrades($grades){
        return $this->subjectRepo->findAllByGrades($grades);
    }

    public function allBySchool($schoolId) {
        return $this->subjectRepo->allWithSchoolDetails($schoolId);
    }

    public function getTeacherBySchoolAndSubject($schoolId, $subjectId) {
        return $this->subjectRepo->findTeacherBySchoolAndSubject($schoolId, $subjectId);
    }

    public function getSubjectBySchoolAndGrade($schoolId, $grade) {
        return $this->subjectRepo->findBySchoolAndGrade($schoolId, $grade);
    }

    public function getSubjectBySchoolAndGrades($schoolId, $grades) {
        return $this->subjectRepo->findBySchoolAndGrades($schoolId, $grades);
    }

    public function create($params, $schoolId = null) {
        DB::beginTransaction();
        try{
            $grades = $params['grades'];
            unset($params['grades']);
            if($schoolId) $params['school_id'] = $schoolId;
            $subject = $this->subjectRepo->create($params);
            $this->subjectRepo->setGrades($subject->id, $grades);
            DB::commit();
            return ['success' => true, 'message' => 'Tạo môn học thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
        
    }

    public function update($id, $params) {
        DB::beginTransaction();
        try{
            $grades = $params['grades'];
            unset($params['grades']);
            $this->subjectRepo->update($id, $params);
            $this->subjectRepo->setGrades($id, $grades);
            DB::commit();
            return ['success' => true, 'message' => 'Sửa môn học thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
    }

    public function updateSubjectByGrade($grade, $subjects) {
        $this->subjectRepo->setByGrade($grade, $subjects);
    }
}