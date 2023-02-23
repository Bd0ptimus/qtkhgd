<?php

namespace App\Admin\Repositories;

use App\Admin\Admin;
use App\Models\GradeSubject;
use App\Models\SchoolClass;
use App\Admin\Models\AdminUser;
use App\Models\Subject;
use App\Models\UserAgency;

use Illuminate\Database\Eloquent\Model;

class SchoolClassRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(SchoolClass $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findBySchoolId($schoolId) {
        return $this->model->with(['homeroomTeacher'])->where('school_id', $schoolId)->get();
    }
  
    public function setHomeroomTeacher($classId, $teacherId) {
        //set teacher to homeroom_teacher in class table.  
    }

    public function getLessonSlotByGrade($grade) {
        switch ($grade) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
                return [
                    'mon_2', 'mon_3', 'mon_4', 'mon_6', 'mon_7', 'mon_8', 'mon_9',
                    'tue_1', 'tue_2', 'tue_3', 'tue_4', 'tue_6', 'tue_7', 'tue_8', 'tue_9',
                    'wed_1', 'wed_2', 'wed_3', 'wed_4', 'wed_6', 'wed_7', 'wed_8', 'wed_9',
                    'thu_1', 'thu_2', 'thu_3', 'thu_4', 'thu_6', 'thu_7', 'thu_8', 'thu_9',
                    'fri_1', 'fri_2', 'fri_3', 'fri_4', 'fri_6', 'fri_7', 'fri_8', 'fri_9',
                ]; break;
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
            case 12:
                return [
                    'mon_2', 'mon_3', 'mon_4', 'mon_5', 'mon_6', 'mon_7', 'mon_8', 'mon_9',
                    'tue_1', 'tue_2', 'tue_3', 'tue_4', 'tue_5', 'tue_6', 'tue_7', 'tue_8', 'tue_9',
                    'wed_1', 'wed_2', 'wed_3', 'wed_4', 'wed_5', 'wed_6', 'wed_7', 'wed_8', 'wed_9',
                    'thu_1', 'thu_2', 'thu_3', 'thu_4', 'thu_5', 'thu_6', 'thu_7', 'thu_8', 'thu_9',
                    'fri_1', 'fri_2', 'fri_3', 'fri_4', 'fri_5', 'fri_6', 'fri_7', 'fri_8', 'fri_9',
                    'sat_1', 'sat_2', 'sat_3', 'sat_4', 'sat_6', 'sat_7', 'sat_8', 'sat_9',
                ]; break;
            default:
                return [
                    'mon_2', 'mon_3', 'mon_4', 'mon_5', 'mon_6', 'mon_7', 'mon_8', 'mon_9',
                    'tue_1', 'tue_2', 'tue_3', 'tue_4', 'tue_5', 'tue_6', 'tue_7', 'tue_8', 'tue_9',
                    'wed_1', 'wed_2', 'wed_3', 'wed_4', 'wed_5', 'wed_6', 'wed_7', 'wed_8', 'wed_9',
                    'thu_1', 'thu_2', 'thu_3', 'thu_4', 'thu_5', 'thu_6', 'thu_7', 'thu_8', 'thu_9',
                    'fri_1', 'fri_2', 'fri_3', 'fri_4', 'fri_5', 'fri_6', 'fri_7', 'fri_8', 'fri_9',
                    'sat_1', 'sat_2', 'sat_3', 'sat_4', 'sat_6', 'sat_7', 'sat_8', 'sat_9',
                ]; break;
        }
    }

    public function getSHLLesson() {
        switch ($this->schoolType) {
            
        }
    } 

    public function getLessonsInWeek($class) {
        $lessons = [];
        foreach($class->classSubjects as $classSubject) {
            for($i = 0; $i < $classSubject->lesson_per_week; $i++) {
                array_push($lessons, $classSubject);
            }
        }

        return $lessons;
    }

    public function checkClassSubjectIfValid($class) {
        $result = true;
        foreach($class->classSubjects as $classSubject) {
           if($classSubject->staff_id == null) { 
                $result = false;
            }
        }
        return $result;
    }

    public function checkAllClassSubjectIfValid($classes) {
        $result = [
            'success' => true,
            'message' => "Success"
        ];

        foreach($classes as $index => $class) {
            if(!$this->checkClassSubjectIfValid($class)) {
                $result = [
                    'success' => false,
                    'message' => "Lớp {$class->class_name} chưa cấu hình đủ thông tin cho các môn học"
                ];
            }
        }
        return $result;
    }
    
    
    public function getSubjectByGrade($grade)
    {
        if (!$grade) return [];
        
        $subjectId = GradeSubject::where('grade', $grade)->get('subject_id');
        $subjects = Subject::whereIn('id', $subjectId)->get(['id', 'name']);
        
        return  $subjects;
    }
    
    public function getSchoolTypeByGrade($grade)
    {
        $schoolType = null;
        
        switch($grade) {
            case $grade <= 5:
                $schoolType = [
                    'id' => 1,
                    'name' => 'Tiểu học'
                ]; break;
            case ($grade <= 9 && $grade > 5):
                $schoolType = [
                    'id' => 2,
                    'name' => 'Trung học cơ sở'
                ]; break;
            case ($grade <= 12 && $grade > 9):
                $schoolType = [
                    'id' => 3,
                    'name' => 'Trung học phổ thông'
                ]; break;
            case ($grade <= 18 && $grade > 12):
                $schoolType = [
                    'id' => 6,
                    'name' => 'Mầm non'
                ]; break;
        }
        
        return $schoolType;
    }
    
    public function getGradeBySchoolType($schoolType)
    {
        $grades = null;
        switch($schoolType) {
            case 1:
                $grades = [1,2,3,4,5]; break;
            case 2:
                $grades = [6,7,8,9]; break;
            case 4:
                $grades = [1,2,3,4,5,6,7,8,9]; break;
            case 3:
            case 7:
                $grades = [10,11,12]; break;
            case 5:
                $grades = [6,7,8,9,10,11,12]; break;
            case 6:
                $grades = [13,14,15,16,17,18]; break;
        }
        
        return $grades;
    }

    public function getClassByGradeAndSubject($gradeId, $subjectId)
    {
        return SchoolClass::select('class.id', 'class.class_name')
            ->join('class_subject', 'class.id', '=', 'class_subject.class_id')
            ->where('class.grade', $gradeId)
            ->where('class_subject.subject_id', $subjectId)
            ->where('class_subject.staff_id', Admin::user()->staffDetail->id)
            ->get();
    }
}