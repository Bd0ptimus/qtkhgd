<?php 
namespace App\Models;

use App\Models\Base\BaseModel as Model;
use App\Admin\Models\AdminUser;
use App\Admin\Admin;
use App\Models\Province;
use App\Models\District;

class Ward extends Model
{
    public $table           = 'wards';
    protected $guarded      = [];
    private static $getList = null;

    public function district() {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function users()
    {
        return $this->morphToMany(AdminUser::class, 'agency', 'user_agency', 'agency_id','user_id','id', 'id');
    }
}