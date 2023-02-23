<?php

namespace App\Admin\Repositories;

use App\Models\ClassLesson;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Model;

class ClassLessonRepository extends BaseRepository
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
        ClassLesson $model
    )
    {
        parent::__construct($model);
        $this->model = $model;
    }
    
}