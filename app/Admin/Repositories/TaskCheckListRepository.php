<?php

namespace App\Admin\Repositories;

use App\Models\TaskCheckList;
use Illuminate\Database\Eloquent\Model;

class TaskCheckListRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(TaskCheckList $model)
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