<?php

namespace App\Admin\Repositories;

use App\Models\HomeworkSheet;
use App\Models\StaffSubject;
use App\Models\StaffGrade;
use App\Models\SchoolStaff;
use App\Admin\Admin;
use Illuminate\Database\Eloquent\Model;

class HomeworkSheetRepository extends BaseRepository
{
    protected $model;
    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(HomeworkSheet $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
    
    // get data by params
    public function list($params = null, $gradeSubjectData, $isCollaborator = false)
    {
        // dd($params);
        $query = $this->model->newQuery();

        if(empty($params['level'])){
            $params['level'] = $gradeSubjectData['school_level']??[];
        }
        

        $grades=[];
        if (!empty($params['level'])) {
            switch($params['level']) {
                case 1:
                    $grades = [1,2,3,4,5];
                    break;
                case 2:
                    $grades = [6,7,8,9];
                    break;
                case 4:
                    $grades = [1,2,3,4,5,6,7,8,9];
                    break;
                case 3:
                    $grades = [10,11,12];
                    break;
                case 5:
                    $grades = [6,7,8,9,10,11,12];
                    break;
                case 6:
                    $grades = [13,14,15,16,17,18];
                    break;
            }
        }

        if (!empty($grades)) {
            if (!empty($params['grade'])) {
                if(in_array((int)$params['grade'],$grades)){
                    $query->where('grade', (int)$params['grade']);
                }else{
                    $query->where('grade',null);
                }
            }else{
                $query->whereIn('grade',$grades);
            }
        }
        if (!empty($params['grade']) && empty($grades)) {
            $query->where('grade', $params['grade']);
        }

        if(Admin::user()->isRole(ROLE_GIAO_VIEN)){
            $staffSubjects = SchoolStaff::where('staff_code',Admin::user()->username)->first()->subjects->pluck('id')->toArray();
            $query->whereIn('subject_id', $staffSubjects);
        }
        
        if (!empty($params['subjectId'])) {
            $query->where('subject_id', $params['subjectId']);
        }

        if(!empty($params['selectedAssemblage'])){
            $query->where('assemblage', $params['selectedAssemblage']);
        }

        if (!empty($params['search'])) {
            $params['search'] = str_replace('+', ' ', $params['search']);
            $query->where('title', 'like', '%'. $params['search'] .'%');
        }
        if($isCollaborator){
            $query->where('creator_id', Admin::user()->id);
        }
        if(!empty($params["selectedCollaborator"])) {
            $query->where('creator_id', $params["selectedCollaborator"]);
        }
        $limit = $this->model::PAGE_SIZE;
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }

        if(!empty($gradeSubjectData)){ //Account Gv-True, Admin-false
            $query->whereIn('grade', $gradeSubjectData['grades']);
            if($gradeSubjectData['school_level']  == 2 || $gradeSubjectData['school_level']  == 3){
                $query->whereIn('subject_id', $gradeSubjectData['subjects']??null);
            } 
        }
        // Get the results and return them.
        return $query->orderBy('id', 'desc')->paginate($limit);
    }
    
    public function getDataByGradeAndSubject($grade = null, $subjectId = null)
    {
        $query = $this->model->newQuery();
        if (!empty($grade)) $query->where('grade', $grade);
        if (!empty($subjectId)) $query->where('subject_id', $grade);
    
        return $query->orderBy('id', 'desc')->get();
    }
}