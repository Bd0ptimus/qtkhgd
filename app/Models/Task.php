<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Models\AdminUser;

class Task extends Model
{
    protected $table = 'tasks';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PAGE_SIZE = 10;

    protected $fillable = [
        'title',
        'description',
        'creator_id',
        'position',
        'priority',
        'status',
        'active_state',
        'start_date',
        'due_date',
        'visibility',
        'object_type',
        'object_id',
        'overdue_notification_sent'
    ];

    /**
     * relatioship business rules:
     *  - the Project can have many Task
     *  - the Task belongs to one Project
     */
    public function project() {
        return $this->belongsTo('App\Admin\Models\Project', 'project_id', 'id');
    }

    /**
     * relatioship business rules:
     *  - the Creator (user) can have many Tasks
     *  - the Task belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo(AdminUser::class, 'creator_id', 'id')->select('id', 'name', 'avatar');
    }

    /**
     * relatioship business rules:
     *  - the Task can have many Comments
     *  - the Comment belongs to one Task
     *  - other Comments can belong to other tables
     */
    public function comments() {
        return $this->hasMany(TaskComment::class, 'task_id', 'id');
    }

    /**
     * relatioship business rules:
     *  - the Task can have many Comments
     *  - the Checklist belongs to one Task
     */
    public function checklists() {
        return $this->belongsToMany(CheckList::class, 'tasks_checklist', 'task_id', 'check_list_id');
    }

    /**
     * relatioship business rules:
     *  - the Task can have many Attachments
     *  - the Attachment belongs to one Task
     *  - other Attachments can belong to other tables
     */
    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * The Users that are assigned to the Task.
     */
    public function assigned() {
        return $this->belongsToMany(AdminUser::class, 'tasks_assignee', 'task_id', 'user_id');
    }

    /**
     * The Users that are assigned to the Task.
     */
    public function followers() {
        return $this->belongsToMany(AdminUser::class, 'tasks_follower', 'task_id', 'user_id');
    }

    public function currentStatus()
    {
        return $this->hasOne(TaskStatus::class, 'id', 'status')
                    ->select('title', 'color', 'id');
    }

    public function setDueDateAttribute($value)
    {
        if($value) $this->attributes['due_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)
            ->format('Y-m-d');
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)
            ->format('Y-m-d');
    }

    public function getDueDateAttribute($value)
    {
        if (!empty($value)) return date(DATETIME_SHORT_FORMAT, strtotime($value)); 
    }

    public function getStartDateAttribute($value)
    {
        if (!empty($value)) return date(DATETIME_SHORT_FORMAT, strtotime($value)); 
    }
}
