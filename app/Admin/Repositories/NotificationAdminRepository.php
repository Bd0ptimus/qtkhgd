<?php

namespace App\Admin\Repositories;

use App\Models\NotificationAdmin as Notification;
use Illuminate\Database\Eloquent\Model;

class NotificationAdminRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Notification $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}