<?php 
namespace App\Models;

use App\Models\Base\BaseModel as Model;
use App\Admin\Models\AdminUser;
use App\Admin\Admin;
use App\Models\District;


class Province extends Model
{
    public $table           = 'provinces';
    protected $guarded      = [];
    private static $getList = null;

    public function districts() {
        return $this->hasMany(District::class, 'province_id', 'id');
    }

    public function users()
    {
        return $this->morphToMany(AdminUser::class, 'agency', 'user_agency', 'agency_id','user_id','id', 'id');
    }

    public function getTotalThptAttribute()
    {
        return $this->districts->reduce(function ($count, $district) {
            return $count + $district->total_thpt;
        }, 0);
    }

    public function getTotalSchoolAttribute()
    {
        return $this->districts->reduce(function ($count, $district) {
            return $count + $district->schools->count();
        }, 0);
    }

    public function getTotalStudentAttribute()
    {
        return $this->districts->reduce(function ($count, $district) {
            return $count + $district->total_student;
        }, 0);
    }

    public function getTotalStaffAttribute()
    {
        return $this->districts->reduce(function ($count, $district) {
            return $count + $district->total_staff;
        }, 0);
    }

    public function getTotalPhongGdAttribute()
    {
        return $this->districts->reduce(function ($count, $district) {
            $active_district = (count($district->users) > 0) ? 1 : 0;
            return $count + $active_district;
        }, 0);
    }
}