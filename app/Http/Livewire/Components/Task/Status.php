<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Admin\Services\TaskService;
use Livewire\Component;

class Status extends Component
{
    public $task_status;
    public $task;
    public $status;
    public $roleEdit = false;

    public function mount($task, $status)
    {
        $this->task = $task;
        $this->task_status = $task->status;
        $this->status = $status;
        $this->roleEdit = $this->checkRoleEdit();
    }

    public function handleUpdatedTaskStatus(TaskService $taskService)
    {
        // check if user have role edit and status new !== status old
        if($this->roleEdit && $this->task->status != $this->task_status) {
            // update status task
            $check = $taskService->updateOnlyFiled($this->task->id, [
                'status' => $this->task_status
            ]);
            if($check) {
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật trạng thái thành công']);
                // send email update status
                $taskService->sendMailTaskUpdateStatus($this->task->id, []);
            } else {
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật trạng thái thất bại']);
            }
        }
    }

    public function checkRoleEdit()
    {
        // get current user login
        $user = Admin::user()->id;
        // get list user was assigned
        $assigned = explode(',', collect($this->task->assigned)->implode('id', ','));

        //if user was assigned or user is creator
        if(in_array($user, $assigned) || $user === $this->task->creator_id) return true;

        return false;
    }

    public function render()
    {
        return view('livewire.components.task.status');
    }
}
