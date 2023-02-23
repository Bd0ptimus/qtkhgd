<?php

namespace App\Admin\Repositories;

use App\Models\Simulator;
use App\Models\SimulatorGrade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SimulatorRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Simulator $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function updateSimulator($id, $data)
    {
        $this->model->find($id)->update([
            "name_simulator"=>$data->title,
            "subject_id"=>$data->subject,
            "related_lesson"=>$data->lesson,
            "user_guide"=>$data->guide,
            "url_simulator"=>$data->url,
        ]);
        $this->model->find($id)->simulatorGrades()->delete();
        foreach ($data->grades as $grade) {
            Log::debug('grade : ' . $grade);
            SimulatorGrade::create([
                "grade" => $grade,
                "simulator_id" => $id
            ]);
        }
    }

    public function createSimulator($data){
        $ceatedRecord=$this->model->create([
            "name_simulator"=>$data->title,
            "subject_id"=>$data->subject,
            "related_lesson"=>$data->lesson,
            "user_guide"=>$data->guide,
            "url_simulator"=>$data->url,
        ]);

        foreach($data->grades as $grade){
            SimulatorGrade::create([
                "grade" => $grade,
                "simulator_id" => $ceatedRecord->id
            ]);
        }
    }

    public function findWithParams($params){
        $query = $this->model->newQuery();
        if(isset($params['grade'])){
            $query->whereIn('id',SimulatorGrade::where('grade', $params['grade'])->pluck('simulator_id')->toArray());
        }else{
            $query->whereIn('id',SimulatorGrade::whereIn('grade', $params['gradeFilter'])->pluck('simulator_id')->toArray());
        }
        if(isset($params['subjectId'])){
            $query->where('subject_id', $params['subjectId']);
        }

        if(isset($params['search'])){
            $params['search'] = str_replace('+', ' ', $params['search']);
            $query->where('name_simulator', 'like' , '%'.$params['search'].'%');
        }   
        $limit = $this->model::PAGE_SIZE;
        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }
        return $query->orderBy('id', 'desc')->paginate($limit);
    }

}
