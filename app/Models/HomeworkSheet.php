<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Admin;

class HomeworkSheet extends Model
{
    //
    
    protected $table = 'homework_sheets';
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TITLE_DESCRIPTION_ADD = 'Thêm phiếu bài tập về nhà';
    const TITLE_DESCRIPTION_EDIT = 'Chỉnh sửa phiếu bài tập về nhà';
    const PAGE_SIZE = 25;
    const PARAM_LEVEL = 'level';
    const PARAM_GRADE = 'grade';
    protected $fillable = [
        'name',
        'grade',
        'subject_id',
        'content',
        'assemblage',
        'creator_id'
    ];
    
    public static function boot ()
    {
        parent::boot();
        static::creating(function($homeworkSheets){
            $homeworkSheets->creator_id = Admin::user()->id;
        });
    }
    
    public function subject () {
        return $this->belongsTo(Subject::class,'subject_id',  'id');
    }
    
    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
