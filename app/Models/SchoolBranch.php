<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolBranch extends Model
{
    private static $getList = null;
    public $table = 'school_branch';
    protected $guarded = [];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'school_branch_id', 'id');
    }

    public function staffs()
    {
        return $this->hasMany(SchoolStaff::class, 'school_branch_id', 'id');
    }

    public function insurances()
    {
        return $this->hasMany(SchoolHealthInsurance::class, 'school_branch_id', 'id');
    }

    public function classes() {
        return $this->hasMany(SchoolClass::class, 'school_branch_id', 'id');
    }

    public function medicines()
    {
        return $this->hasMany(SchoolMedicine::class, 'school_branch_id', 'id');
    }

    public function medicineHistories() {
        return $this->hasMany(SchoolMedicineHistory::class, 'school_branch_id', 'id');
    }

    public function medicalEquipments()
    {
        return $this->hasMany(SchoolMedicalEquipment::class, 'school_branch_id', 'id');
    }

    public function medicalEquipmentHistories()
    {
        return $this->hasMany(SchoolMedicalEquipmentHistory::class, 'school_branch_id', 'id');
    }

    public function foodProviders()
    {
        return $this->hasMany(SchoolFoodProvider::class,'school_branch_id', 'id');
    }

    public function foodInspections() {
        return $this->hasMany(FoodInspection::class, 'school_branch_id', 'id');
    }

    public function checkRooms() {
        return $this->hasMany(CheckRoom::class, 'school_branch_id', 'id');
    }

    public function checkFurnitures() {
        return $this->hasMany(CheckFurniture::class, 'school_branch_id', 'id');
    }
}