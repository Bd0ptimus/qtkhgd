<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPlan extends Model
{
    protected $table = 'teacher_plan';
    protected $dateFormat = 'Y-m-d H:i:s';   

    protected $fillable = [
        'regular_group_id',
        'staff_id',
        'grade',
        'subject_id',
        'month',
        'chuyen_de',
        'additional_tasks',
        'status'
    ];

    public static function boot()
    {
        parent::boot();

        self::updated(function ($model) {

        });

        self::deleted(function ($model) {
            $model->lessons()->delete();
            $model->histories()->delete();
        });
    }

    public function staff() {
        return $this->belongsTo(SchoolStaff::class, 'staff_id', 'id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function lessons() {
        return $this->hasMany(TeacherLesson::class, 'teacher_plan_id', 'id');
    }

    public function regularGroup() {
        return $this->belongsTo(RegularGroup::class, 'regular_group_id', 'id');
    }

    public function histories() {
        return $this->hasMany(TeacherPlanHistory::class, 'teacher_plan_id', 'id')->orderBy('created_at','DESC');
    }

    public function getStatus() {
        switch($this->status) {
            case PLAN_PENDING:
                return "Đang soạn thảo"; break;
            case PLAN_INREVIEW:
                return "Đang duyệt"; break;
            case PLAN_APPROVED:
                return "Đã được duyệt"; break;
            default:
                return "Đang soạn thảo"; break;
        }
    }
}
