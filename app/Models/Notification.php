<?php

namespace App\Models;

use App\Models\Base\BaseModel as Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $guarded = [];
    protected $fillable = ['user_id', 'title', 'content', 'link', 'type', 'data', 'read'];
    protected $hidden = ['user_id'];

    const TYPE_POST = 0;
    const TYPE_ABNORMAL = 1;
}
