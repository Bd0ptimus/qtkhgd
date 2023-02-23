<?php

namespace App\Admin\Repositories;

use App\Models\LessonSampleContent;
use App\Models\LessonSample;
use Illuminate\Database\Eloquent\Model;

class LessonSampleContentRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(LessonSampleContent $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function allWithLessonSample($lessonsampleId) {
        $lessoncontents = LessonSampleContent::with('lessonsample')->where('lesson_sample_id',$lessonsampleId)->get();
         return $lessoncontents;
    }    
   
}