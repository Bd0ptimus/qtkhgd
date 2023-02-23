<?php

namespace App\Admin\Repositories;

use App\Models\TaskAssignee;
use Illuminate\Database\Eloquent\Model;

class TaskAssigneeRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(TaskAssignee $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function deleteByTaskId(int $taskId): bool
    {
        $query = $this->model->newQuery();

        return $query->where('task_id', $taskId)->delete();
    }
}