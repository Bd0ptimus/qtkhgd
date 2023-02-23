<?php

namespace App\Admin\Services;

use App\Admin\Repositories\AdminNotiRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminNotiService
{
    protected $adminNotiRepository;

    public function __construct(AdminNotiRepository $adminNotiRepository)
    {
        $this->adminNotiRepository = $adminNotiRepository;
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $result = $this->adminNotiRepository->create($data);
            DB::commit();
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create noti admin]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return false;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $this->adminNotiRepository->update($id, $data);
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[update noti admin]',
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