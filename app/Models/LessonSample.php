<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Admin;

class LessonSample extends Model
{
    //
    
    protected $table = 'lesson_sample';
    protected $dateFormat = 'Y-m-d H:i:s';
    const PAGE_SIZE = 25;
    
    
    
    protected $fillable = [
        'title',
        'name',
        'grade',
        'subject_id',
        'content',
        'creator_id',
        'assemblage',
        'video_thiet_bi_so',
        'game_simulator', 
        'diagram_simulator',
        'homesheet_id',
        'exercise_id'
    ];
    
    public static function boot ()
    {
        parent::boot();
        static::creating(function($lessonSample){
            $lessonSample->creator_id = Admin::user()->id;
        });
    }
    
    public function subject () {
        return $this->belongsTo(Subject::class,'subject_id',  'id');
    }

    public function exercise () {
        return $this->belongsTo(ExerciseQuestion::class,'exercise_id',  'id');
    }

    public function homesheet () {
        return $this->belongsTo(HomeworkSheet::class,'homesheet_id',  'id');
    }

    public function getAssemblage() {
        return in_array($this->assemblage, BOOK_ASSEMBLAGES) ? $this->assemblage : "";
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
