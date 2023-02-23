<?php

namespace App\Admin\Repositories;

use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Model;

class TaskStatusRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(TaskStatus $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getList()
    {
        return $this->model->orderBy('position', 'ASC')->get();
    }
}