<?php

namespace App\Models;

use App\Admin\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    private static $getList = null;
    public $table = 'user_device';
    protected $guarded = [];
    protected $fillable = ['user_id', 'fcm_token', 'ip', 'device_id', 'identify_token', 'api_token', 'expired'];
    protected $hidden = ['user_id'];

    public function user()
    {
        return $this->belongsTo(AdminUser::class, 'user_id', 'id');
    }
}
