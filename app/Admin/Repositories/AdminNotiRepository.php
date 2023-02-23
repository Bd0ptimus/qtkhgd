<?php

namespace App\Admin\Repositories;

use App\Models\NotificationAdmin;
use Illuminate\Database\Eloquent\Model;

class AdminNotiRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(NotificationAdmin $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}