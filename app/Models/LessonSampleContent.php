<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Admin;

class LessonSampleContent extends Model
{
    //
    
    protected $table = 'lesson_sample_additional_content';
    protected $dateFormat = 'Y-m-d H:i:s';
   
    protected $fillable = [
        'name',
        'lesson_sample_id',
        'additional_content',        
    ];
    
    public function lessonsample () {
        return $this->belongsTo(LessonSample::class,'lesson_sample_id',  'id');
    }
}
