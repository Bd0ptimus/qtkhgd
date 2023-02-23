<?php

namespace App\Admin\Repositories;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;

class SchoolRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(School $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }  
    
    
    public function takeSchoolByDistrictWithCondition($districtId, $params=[]){
        $query = $this->model->newQuery();
        if(isset($params['level'])){
            $query->where('school_type',$params['level'] );
        }
        return $query->where('district_id',$districtId)->get();
    }
}