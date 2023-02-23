<?php

namespace App\Admin\Services;

use App\Admin\Repositories\CheckListRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckListService
{
    protected $checkListRepository;

    public function __construct(CheckListRepository $checkListRepository)
    {
        $this->checkListRepository = $checkListRepository;
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $result = $this->checkListRepository->create($data);
            DB::commit();
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create check list]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return false;
        }
    }
}