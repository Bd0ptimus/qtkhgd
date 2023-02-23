<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Admin\Services\TaskService;
use Livewire\Component;

class Priority extends Component
{
    public $task_priority;
    public $task;
    public $priority;
    public $roleEdit = false;

    public function mount($task, $priority)
    {
        $this->task = $task;
        $this->task_priority = $task->priority;
        $this->priority = PRIORITY_VALUE;
        $this->roleEdit = $this->checkRoleEdit();
    }

    public function handleUpdatedTaskPriority(TaskService $taskService)
    {
        // check if user have role edit and priority new !== priority old
        if($this->roleEdit && $this->task->priority != $this->task_priority) {
            // update priority task
            $check = $taskService->updateOnlyFiled($this->task->id, [
                'priority' => $this->task_priority
            ]);
            if($check) {
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật sự ưu tiên thành công']);
            } else {
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật sự ưu tiên thất bại']);
            }
        }
    }

    public function checkRoleEdit()
    {
        //if user is creator
        return (Admin::user()->id === $this->task->creator_id)? true : false;
    }

    public function render()
    {
        return view('livewire.components.task.priority');
    }
}
