<?php

namespace App\Models;

use App\Models\Base\BaseModel as Model;

class NotificationAdmin extends Model
{
    const TYPE = [
        "teacher_plan" => 1,
        "group_plan" => 2, 
        "school_plan" => 3, 
        "nhiem_vu" => 4
    ];

    const NOTI_TASK_ASSIGNED = 1;
    const NOTI_TASK_FOLLOWER = 2;
    const NOTI_TASK_COMMENT = 3;

    protected $table = 'notification_admin';
    protected $guarded = [];
    protected $fillable = ['user_id', 'title', 'content', 'type', 'data', 'read'];
    protected $hidden = ['user_id'];
    protected $appends = ['link'];

    public function getLinkAttribute()
    {
        $data = json_decode($this->data);
        $type = $this->getOriginal('type');
        switch ($type) {
            case self::TYPE["nhiem_vu"]:
                $task = json_decode($this->data, true);
                $route = @$task['task_id'] ?? null;
                break;
            default:
                $route = null;
                break;
        }

        return $route;
    }

    public function isTypeNotification(): bool
    {
        return in_array($this->getOriginal('type'), []);
    }

    public function isTypeDanger(): bool
    {
        return in_array($this->getOriginal('type'), []);
    }
}
