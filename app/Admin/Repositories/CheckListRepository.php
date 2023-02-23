<?php

namespace App\Admin\Repositories;

use App\Models\CheckList;
use Illuminate\Database\Eloquent\Model;

class CheckListRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(CheckList $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}