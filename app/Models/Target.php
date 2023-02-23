<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassInfo;

class Target extends Model
{
    protected $table = 'target';
    protected $dateFormat = 'Y-m-d H:i:s';
    const PAGE_SIZE = 25;
    
    protected $fillable = [
        'school_id',
        'staff_id',
        'main_target',
        'final_target',
        'target_index',
        'type',
        'school_type',
        'title',
        'description',
        'solution',
        'result',
        'created_by',
    ];
    
    public static function boot ()
    {
        parent::boot();
    }

    public function staffTargets() {
        return $this->hasMany(Target::class, 'main_target', 'id');
    }
    public function points() {
        return $this->hasMany(TargetPoint::class, 'target_id', 'id');
    }
    public function mainPoints() {
        return $this->hasMany(TargetPoint::class, 'target_id', 'id')->where('main_point', NULL);
    }
    public function staff() {
        return $this->belongsTo(SchoolStaff::class, 'staff_id', 'id');
    }

    public function teacherSubject(){
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function teacherClass(){
        return $this->belongsTo(SchoolClass::class,  'class_id', 'id');
    }
    public function getTotalIndexPoint() {
        if(count($this->mainPoints) > 0) {
            $totalIndexPoint = 0;
            foreach($this->mainPoints as $point) {
                $totalIndexPoint += $point->index_point;
            }
            return $totalIndexPoint;
        } else return 100;
    }
    public function getResultMainPoint() {
        if(count($this->mainPoints) > 0) {
            $total = 0;
            foreach($this->mainPoints as $point) {
                $total += $point->final_point;
            }
            return $total * $this->target_index / 100;
        } else return $this->final_point;
    }
    public function getSummaryResult() {
        if(count($this->staffTargets) > 0) {
            $total = 0;
            foreach($this->staffTargets as $target) {
                $total += $target->result;
            }
            return $total / count($this->staffTargets);
        } else return $this->result;
    }
    public function updateResultTarget($point) {
        $total = 0;
        $totalIndexPoint = 0;
        if(count($this->mainPoints) > 0) {
            foreach($this->mainPoints as $mainPoint) {
                if($point && $point->id == $mainPoint->id) {
                    $total += $point->final_point;
                    $totalIndexPoint += $point->index_point;
                    continue;
                }
                $total += $mainPoint->final_point;
                $totalIndexPoint += $mainPoint->index_point;
            }
           $this->result = $total;
           $this->final_target = $total * $this->target_index / 100;
           $this->save();
        }
        return $total;
    }
}
