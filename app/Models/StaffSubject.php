<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSubject extends Model
{
    protected $table = 'staff_subject';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'subject_id'
    ];

    public function staff() {
        return $this->belongsTo(SchoolStaff::class, 'staff_id', 'id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}
