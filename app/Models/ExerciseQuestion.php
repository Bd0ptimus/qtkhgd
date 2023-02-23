<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Admin;

class ExerciseQuestion extends Model
{
    //
    
    protected $table = 'exercise_questions';
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TITLE_DESCRIPTION_ADD = 'Thêm đề kiểm tra';
    const TITLE_DESCRIPTION_EDIT = 'Chỉnh sửa đề kiểm tra';
    const PAGE_SIZE = 25;
    const PARAM_LEVEL = 'level';
    const PARAM_GRADE = 'grade';
    
    
    protected $fillable = [
        'title',
        'name',
        'grade',
        'subject_id',
        'content',
        'creator_id',
        'assemblage',
    ];
    
    public static function boot ()
    {
        parent::boot();
        static::creating(function($exeQuestions){
            $exeQuestions->creator_id = Admin::user()->id;
        });
    }
    
    public function subject () {
        return $this->belongsTo(Subject::class,'subject_id',  'id');
    }
    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
