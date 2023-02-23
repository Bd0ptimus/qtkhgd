<?php

namespace App\Admin\Repositories;

use App\Admin\Admin;
use App\Models\Ebook;
use Illuminate\Database\Eloquent\Model;


class EbookRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Ebook $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
    
    // get data by params
    public function list($params = null, $checkIsCollaborator = false)
    {
        $query = $this->model->newQuery();
        if(!empty($params['level'])) {
            switch($params['level']) {
                case 1:
                    $grades = [1,2,3,4,5]; break;
                case 2:
                    $grades = [6,7,8,9]; break;
                case 4:
                    $grades = [1,2,3,4,5,6,7,8,9]; break;
                case 3:
                case 7:
                    $grades = [10,11,12]; break;
                case 5:
                    $grades = [6,7,8,9,10,11,12]; break;
                case 6:
                    $grades = [13,14,15,16,17,18]; break;
            }
        }
        
        if (!empty($grades)) {
            $id = null;
            if (!empty($params['grade'])) {
                $id = (int)$params['grade'] ?? null;
            }
            $params['grade'] = array_merge([$id], $grades);
            $query->whereIn('grade', $params['grade']);
        }
    
        if(!empty($params['grade']) && empty($grades)) {
            $query->where('grade', $params['grade']);
        }
    
        if(!empty($params['subjectId'])) {
            $query->where('subject_id', $params['subjectId']);
        }

        if(!empty($params['assemblage'])) {
            $query->where('assemblage', $params['assemblage']);
        }

        if(!empty($params['ebookCategory'])) {
            $query->join('ebook_category_items', function ($join) use ($params) {
                $join->on('ebook.id', '=', 'ebook_category_items.ebook_id')
                     ->where('ebook_category_items.ebook_category_id', $params['ebookCategory']);
            });
        }

        if (!empty($params['search'])) {
            $params['search'] = str_replace('+',' ', $params['search']);
            $query->where('name','like', '%'. $params['search'] .'%');
        }
        if($checkIsCollaborator) {
            $query->where('creator_id', Admin::user()->id);
        }
        if(!empty($params["selectedCollaborator"])) {
            $query->where('creator_id', $params["selectedCollaborator"]);
        }
        $limit = $this->model::PAGE_SIZE;
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }  

      
        // Get the results and return them.       
        return $query->with([
            'attachments',
            'ebookCategories',
        ])
            ->orderBy('ebook.id', 'desc')
            ->paginate($limit);
    }
    
    public function getDataByGradeAndSubject($grade = null, $subjectId = null)
    {
        $query = $this->model->newQuery();
        if (!empty($grade)) $query->where('grade', $grade);
        if (!empty($subjectId)) $query->where('subject_id', $grade);
        
        return $query->orderBy('id', 'desc')->get();
    }
}