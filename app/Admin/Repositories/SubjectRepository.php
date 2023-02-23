<?php

namespace App\Admin\Repositories;

use App\Models\Subject;
use App\Models\GradeSubject;
use App\Models\School;
use Illuminate\Database\Eloquent\Model;

class SubjectRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Subject $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function allBySystem() {
        return $this->model->with(['grades'])->where('school_id', null)->get();
    }

    /* 
        Lấy ra các môn học thuộc về 1 trường.
        Trả về ds môn học, thuộc những khối nào, có những giáo viên nào dạy. 
     */
    public function findBySchoolId($schoolId) {
        $school = School::find($schoolId);
        return $this->model->with(['grades', 'teachers' => function($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        }])->whereIn('id', GradeSubject::whereIn('grade', $school->getSchoolGrades())->pluck('subject_id')->toArray())
        ->where(function($query) use ($schoolId) {
            $query->where('school_id', null);
            $query->orWhere('school_id', $schoolId);
        })->get();
    }

    public function allWithSchoolDetails($schoolId) {
        return [
            'school' => $school = School::find($schoolId),
            'subjects' => $this->model->with(['grades', 'teachers' => function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            }])->whereIn('id', GradeSubject::whereIn('grade', $school->getSchoolGrades())->pluck('subject_id')->toArray())
            ->where(function($query) use ($schoolId) {
                $query->where('school_id', null);
                $query->orWhere('school_id', $schoolId);
            })->get()
        ];
    }

    public function findTeacherBySchoolAndSubject($schoolId, $subjectId) {
        return $this->model->findbyId($subjectId, ['*'], ['teachers' => function($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        }]);
    }

    public function findBySchoolAndGrade($schoolId, $grade) {
        return $this->model->whereIn('id', GradeSubject::where('grade', $grade)->pluck('subject_id')->toArray())
        ->where(function($query) use ($schoolId) {
            $query->where('school_id', null);
            $query->orWhere('school_id', $schoolId);
        })->get();
    }

    public function findBySchoolAndGrades($schoolId, $grades) {
        return $this->model->whereIn('id', GradeSubject::whereIn('grade', $grades)->pluck('subject_id')->toArray())
        ->where(function($query) use ($schoolId) {
            $query->where('school_id', null);
            $query->orWhere('school_id', $schoolId);
        })->get();
    }

    public function findByGrades($gradeIds){
        return $this->model->whereIn('id', GradeSubject::where('grade', $gradeIds)->pluck('subject_id')->toArray())->pluck('id');
    }

    public function findAllByGrades($gradeIds){
        return $this->model->whereIn('id', GradeSubject::whereIn('grade', $gradeIds)->pluck('subject_id')->toArray())->get();
    }

    public function setGrades($id, $grades) {
        GradeSubject::where('subject_id', $id)->delete();
        foreach ( $grades as $grade ) {
            GradeSubject::create([
                'subject_id' => $id,
                'grade' => $grade
            ]);
        }
    }

    public function setByGrade($grade, $subjects) {
        GradeSubject::where('grade', $grade)->delete();
        foreach ( $subjects as $subject ) {
            GradeSubject::create([
                'subject_id' => $subject,
                'grade' => $grade
            ]);
        }
    }
}