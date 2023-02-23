<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCheckList extends Model
{
    protected $table = 'tasks_checklist';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'task_id',
        'check_list_id',
    ];
}
