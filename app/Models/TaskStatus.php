<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    protected $table = 'tasks_status';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'title',
        'color',
        'position',
        'creator_id',
    ];

    /**
     * relatioship business rules:
     * - the Task Status can have many Tasks
     * - the Task belongs to one Task Status
     */
    public function tasks() {
        return $this->hasMany('App\Models\Task', 'task_status', 'id');
    }
}
