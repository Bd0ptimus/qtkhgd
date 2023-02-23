<?php

namespace App\Admin\Repositories;

use App\Admin\Admin;
use App\Models\EbookCategory;
use Illuminate\Database\Eloquent\Model;

class EbookCategoryRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(EbookCategory $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getAll(array $params = [], $checkIsCollaborator = false)
    {
        $qb = $this->model->newQuery();
        if (!empty($params['search'])) {
            $qb->where('name', 'LIKE', '%' . $params['search'] . '%');
        }
        
        if(!empty($params["selectedCollaborator"])) {
            $qb->where('creator_id', $params["selectedCollaborator"]);
        }
        return $qb->paginate($this->model::PAGE_SIZE);
    }
}