<?php

namespace App\Admin\Repositories;

use App\Models\ClassSubject;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Model;

class TimetableRepository extends BaseRepository
{
    protected $model;
    protected $schoolModel;
    protected $classModel;
    protected $classSubjectModel;
    protected $staffModel;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        Timetable $model,
        ClassSubject $classSubjectModel
    )
    {
        parent::__construct($model);
        $this->model = $model;
        $this->classSubjectModel = $classSubjectModel;
    }

    public function findBySchoolId($schoolId) {
        return $this->model->where('school_id', $schoolId)->with('classLessons')->get();
    }

    public function findActiveTimetableBySchool($schoolId) {
        return $this->model->where([
            'school_id' => $schoolId,
            'is_actived' => 1
        ])->with(['classLessons', 'classLessons.classSubject'])->first();
    }

    public function findByStaff($schoolId, $staffId) {
        $staffClassSubjects = $this->classSubjectModel->where('staff_id', $staffId)->get();
        return $this->model->where([
            'school_id' => $schoolId,   
            'is_actived' => 1
        ])->with(['classLessons' => function ($query) use ($staffClassSubjects) {
            $query->whereIn('class_subject_id', $staffClassSubjects->pluck('id')->toArray());
        }, 'classLessons.classSubject'])->first();
    }
}