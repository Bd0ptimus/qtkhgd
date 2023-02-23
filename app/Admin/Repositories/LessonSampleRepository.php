<?php

namespace App\Admin\Repositories;

use App\Models\Ebook;
use App\Models\LessonSample as LessonSample;
use App\Models\StaffSubject;
use App\Models\StaffGrade;
use App\Models\SchoolStaff;
use App\Admin\Admin;
use Illuminate\Database\Eloquent\Model;

class LessonSampleRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(LessonSample $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    // get data by params
    public function list($params = null, $gradeSubjectData, $isCollaborator)
    {
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
        //add
        if(!empty($params['filter_powerpoint'])){
            if($params['filter_powerpoint'] == "yes") {
                 $query->has("attachments");
            }
            else {
                 $query->has("attachments", "==", 0);
            }  
        }
        if(!empty($params['filter_digital_device'])){
            $this->queryCondition($params['filter_digital_device'],'video_thiet_bi_so',$query);
        }
        if(!empty($params['filter_homesheet'])){
            $this->queryCondition($params['filter_homesheet'],'homesheet_id',$query);
        }
        if(!empty($params['filter_exercise'])){
            $this->queryCondition($params['filter_exercise'],'exercise_id',$query);
        }
        if(!empty($params['filter_diagram_simulator'])){
            $this->queryCondition($params['filter_diagram_simulator'],'diagram_simulator',$query);
        }
        if(!empty($params['filter_game'])){
            $this->queryCondition($params['filter_game'],'game_simulator',$query);
        }

        if (!empty($params['search'])) {
            $params['search'] = str_replace('+', ' ', $params['search']);
            $query->where('title', 'like', '%'. $params['search'] .'%');
        }

        if($isCollaborator){
            $query->where('creator_id', Admin::user()->id);
        }

        $limit = $this->model::PAGE_SIZE;
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }
        if(!empty($params["selectedCollaborator"])) {
            $query->where('creator_id', $params["selectedCollaborator"]);
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


    public function queryCondition($result, $field, $query){
        if($result == "yes") {
         return $query->whereNotNull($field);
        }
        else if($result == "no") {
         return $query->whereNull($field);
        }
        return $query;
    }

    public function updateVideo($id,$file){
        $this->model->where('id',$id)->update([
            "video_thiet_bi_so" => $file['filepath'],
        ]);
    }


}