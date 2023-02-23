<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TeacherLesson extends Model
{
    protected $table = 'teacher_lesson';
    protected $dateFormat = 'Y-m-d H:i:s';   

    protected $fillable = [
        'teacher_plan_id',
        'status',
        'bai_hoc',
        'ten_bai_hoc',
        'tiet_thu',
        'so_tiet',
        'thoi_diem',
        'thiet_bi_day_hoc',
        'dia_diem_day_hoc',
        'tuan_thang',
        'chu_de',
        'noi_dung_dieu_chinh',
        'ghi_chu',
        'thoi_gian',
        'noi_dung',
        'phoi_hop',
        'ket_qua',
        'content',
        'month_year',
        'start_date',
        'end_date',
        'ppt', 'video_tbs', 'game_simulator', 'diagram_simulator', 'homeworks', 'advanced_exercise', 'test_question', 'game_content'
    ];

    public function histories() {
        return $this->hasMany(TeacherLessonHistory::class, 'teacher_lesson_id', 'id');
    }

    public function plan(){
        return $this->belongsTo(TeacherPlan::class, 'teacher_plan_id', 'id');
    }

    public function setMonthYearAttribute($value)
    {
        $this->attributes['month_year'] = Carbon::parse('01-' . $value);
    }

    public function getMonthYearAttribute($value)
    {
        if (!$value) {
            return $value;
        }
        $monthYear = new Carbon($value);

        return $monthYear->format('m-Y');
    }

    public function getStartDateAttribute($value)
    {
        if (!$value) {
            return $value;
        }
        $monthYear = new Carbon($value);

        return $monthYear->format('d-m-Y');
    }

    public function getEndDateAttribute($value)
    {
        if (!$value) {
            return $value;
        }
        $monthYear = new Carbon($value);

        return $monthYear->format('d-m-Y');
    }
}
