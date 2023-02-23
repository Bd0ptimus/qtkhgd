<?php

namespace App\Admin\Repositories;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;

class AttachmentRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Attachment $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }


    public function deleteByTaskId(int $taskId, string $type): bool
    {
        $query = $this->model->newQuery();

        return $query->where('attachable_id', $taskId)
                     ->where('attachable_type', $type)
                     ->delete();
    }
}