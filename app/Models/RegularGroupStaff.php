<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Models\AdminUser;

class RegularGroupStaff extends Model
{
    protected $table = 'regular_group_staff';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'regular_group_id',
        'staff_id',
        'member_role'
    ];

    public function regularGroup() {
        return $this->belongsTo(RegularGroup::class, 'regular_group_id', 'id');
    }
     
    public function staff() {
        return $this->belongsTo(SchoolStaff::class, 'staff_id', 'id');
    }
}
