<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $table = 'timetable';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'school_id',
        'school_brand_id',
        'from_date',
        'to_date',
        'is_actived'
    ];

    public static function boot()
    {
        parent::boot();
        self::deleted(function ($model) {
            $model->classLessons()->delete();
        });
    }


    public function classLessons() {
        return $this->hasMany(ClassLesson::class, 'timetable_id', 'id');
    }

    public function getFromDateAttribute($value)
    {
        if (!empty($value)) return date(DATETIME_SHORT_FORMAT, strtotime($value)); 
    }
    
    public function getToDateAttribute($value)
    {
        if (!empty($value)) return date(DATETIME_SHORT_FORMAT, strtotime($value)); 
    }
} 
