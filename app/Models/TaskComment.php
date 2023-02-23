<?php

namespace App\Models;
use App\Admin\Models\AdminUser;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $table = 'tasks_comment';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const PAGE_SIZE = 10;

    protected $fillable = [
        'task_id',
        'creator_id',
        'comment',
    ];

    public function creator()
    {
        return $this->hasOne(AdminUser::class, 'id', 'creator_id')
            ->select('id', 'name', 'avatar');
    }
}
