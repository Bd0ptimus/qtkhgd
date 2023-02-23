<?php

namespace App\Models;

use App\Admin\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;

class TeacherLessonHistory extends Model
{
    protected $table = 'teacher_lesson_history';
    protected $dateFormat = 'Y-m-d H:i:s';   

    protected $fillable = [
        'teacher_lesson_id',
        'notes',
        'status',
        'created_by'
    ];

    public function createdBy() {
        return $this->belongsTo(AdminUser::class, 'created_by', 'id');
    }
}
