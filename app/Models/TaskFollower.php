<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFollower extends Model
{
    protected $table = 'tasks_follower';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'user_id',
        'task_id'
    ];
}
