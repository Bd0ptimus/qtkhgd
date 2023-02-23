<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Models\TaskComment;
use App\Admin\Repositories\TaskCommentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TaskCommentService
{
    protected $taskCommentRepo;
    protected $taskService;

    public function __construct(TaskCommentRepository $commentRepository, TaskService $taskService)
    {
        $this->taskCommentRepo = $commentRepository;
        $this->taskService = $taskService;
    }


    public function createComment($data)
    {
        DB::beginTransaction();
        try {
            if($this->taskCommentRepo->create($data)) {
                $this->taskService->handleNotiComment($data['task_id'], $data['creator_id'], Admin::user()->name);
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create task comment]',
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