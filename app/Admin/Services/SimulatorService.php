<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Repositories\SimulatorRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SimulatorService
{
    protected $simulatorRepo;

    public function __construct(
        SimulatorRepository $simulatorRepo
    )
    {
        $this->simulatorRepo = $simulatorRepo;
    }


    public function loadIndex($params){
        return $this->simulatorRepo->findWithParams($params);
    }
    public function findById($simulatorId) {
        return $this->simulatorRepo->findById($simulatorId, ['*'], ['simulatorGrades','subject']);
    }
    
    public function findAll(){
        return $this->simulatorRepo->all(['*'], ['simulatorGrades','subject']);
    }

    public function deleteById($simulatorId){
        $this->simulatorRepo->deleteById($simulatorId);
        $message = 'Xóa bài mô phỏng thành công';
        return [
            'message' => $message,
            'success' => true,
        ];
    }

    public function updateSimulator($id, $data){
        DB::beginTransaction();
        try {
            $this->simulatorRepo->updateSimulator($id,$data);
            DB::commit();
            $message = 'Sửa bài mô phỏng thành công';
            return [
                'message' => $message,
                'success' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function createSimulator($data){
        DB::beginTransaction();
        try {
            $this->simulatorRepo->createSimulator($data);
            DB::commit();
            $message = 'Thêm mới bài mô phỏng thành công';
            return [
                'message' => $message,
                'success' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}