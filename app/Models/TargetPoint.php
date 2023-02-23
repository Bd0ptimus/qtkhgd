<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassInfo;
use Illuminate\Support\Facades\DB;

class TargetPoint extends Model
{
    protected $table = 'target_points';
    
    protected $fillable = [
        'target_id',
        'content',
        'final_point',
        'index_point',
        'result',
        'staff_id',
        'class_id',
        'main_point',
        'subject_id'
    ];
    public function subPoints() {
        return $this->hasMany(TargetPoint::class, "main_point", "id");
    }
    public function getSubPointsByStaff($staffId) {
        return $this->subPoints()->where("staff_id", $staffId)->get();
    }
    public function target() {
        return $this->belongsTo(Target::class,"target_id", "id");
    }
    public function teacherSubject(){
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function teacherClass(){
        return $this->belongsTo(SchoolClass::class,  'class_id', 'id');
    }
    public function staff(){
        return $this->belongsTo(SchoolStaff::class,  'staff_id', 'id');
    }
    public function getIndexPoint($staffId){
        $subPoint = $this->subPoints()->where("staff_id",$staffId)->first();
        return $subPoint->index_point;
    }
    public function getTotalIndexPoint() {
        if(count($this->subPoints) > 0) {
            $totalIndexPoint = 0;
            foreach($this->subPoints as $point) {
                $totalIndexPoint += $point->index_point;
            }
            return $totalIndexPoint;
        } else return 100;
    }
    public function updateResultMainPoint($checkWithClass = false, $totalIndexMainPoint, $point) {
        $total = 0;
        $totalIndexPoint = 0;
       
        if(count($this->subPoints) > 0) {
                foreach($this->subPoints as $targetPoint) {
                    if($point && $point->id == $targetPoint->id) {
                        $total += $point->final_point;
                        $totalIndexPoint += $point->index_point;
                        continue;
                    }
                    $total += $targetPoint->final_point;
                    $totalIndexPoint += $targetPoint->index_point;
                }
               $this->result = $total;
               $this->final_point = $total * $this->index_point / $totalIndexMainPoint;
               $this->save();
        
        }
        return $total;
    }
}
