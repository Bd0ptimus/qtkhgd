<?php

namespace App\Admin\Repositories;

use App\Models\TaskComment;
use Illuminate\Database\Eloquent\Model;

class TaskCommentRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(TaskComment $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getCommentByTask(int $taskId, $pageSize)
    {
        $query = $this->model->newQuery();

        return $query->with('creator')
            ->where('task_id', $taskId)
            ->orderBy('id', 'desc')
            ->paginate($pageSize);
    }

    public function deleteByTaskId(int $taskId): bool
    {
        $query = $this->model->newQuery();

        return $query->where('task_id', $taskId)->delete();
    }
}