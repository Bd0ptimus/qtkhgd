<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Models\AdminUser;
use App\Admin\Admin;
use App\Models\District;


class StaffLinkingSchool extends Model
{
    public $table           = 'staff_linking_school';
    protected $guarded      = [];
    protected $fillable = [
        'staff_id',
        'primary_school_id',
        'additional_school_id',
        'working_days',
        'working_slots'
    ];

    const SLOT_BY_DAY = [
        2 => ['mon_1', 'mon_2', 'mon_3', 'mon_4', 'mon_5', 'mon_6', 'mon_7', 'mon_8', 'mon_9'],
        3 => ['tue_1', 'tue_2', 'tue_3', 'tue_4', 'tue_5', 'tue_6', 'tue_7', 'tue_8', 'tue_9'],
        4 => ['wed_1', 'wed_2', 'wed_3', 'wed_4', 'wed_5', 'wed_6', 'wed_7', 'wed_8', 'wed_9'],
        5 => ['thu_1', 'thu_2', 'thu_3', 'thu_4', 'thu_5', 'thu_6', 'thu_7', 'thu_8', 'thu_9'],
        6 => ['fri_1', 'fri_2', 'fri_3', 'fri_4', 'fri_5', 'fri_6', 'fri_7', 'fri_8', 'fri_9'],
        7 => ['sat_1', 'sat_2', 'sat_3', 'sat_4', 'sat_5', 'sat_6', 'sat_7', 'sat_8', 'sat_9'],
    ];

    private static $getList = null;

    public function staff() {
        return $this->belongsTo(SchoolStaff::class, 'staff_id', 'id');
    }

    public function primarySchool() {
        return $this->belongsTo(School::class, 'primary_school_id', 'id');
    }

    public function additionalSchool() {
        return $this->belongsTo(School::class, 'additional_school_id', 'id');
    }
}