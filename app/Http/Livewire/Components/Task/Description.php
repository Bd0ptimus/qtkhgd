<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Admin\Services\TaskService;
use Livewire\Component;

class Description extends Component
{
    public $task;
    public $description;
    public $task_id;
    public $showEdit = false;
    public $roleEdit = false;

    protected $rules = [
        'description' => 'required'
    ];

    public function mount($task)
    {
        $this->task = $task;
        $this->task_id = $task->id;
        $this->description = $task->description;
        $this->roleEdit = $this->checkRoleEdit();
    }

    public function updateTaskDescription(TaskService $taskService)
    {
        if($this->checkRoleEdit()) {
            $validatedData = $this->validate($this->rules);

            $updated = $taskService->updateOnlyFiled($this->task_id, $validatedData);

            if($updated) {
                $this->cancelUpdateTaskDescription();
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật mô tả thành công']);
            } else {
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật mô tả thất bại']);
            }
        }
    }

    public function cancelUpdateTaskDescription()
    {
        $this->showEdit = false;
    }

    public function showEditDescription()
    {
        if($this->checkRoleEdit()) {
            $this->dispatchBrowserEvent('contentTaskDetail', ['description' => $this->description]);
            $this->showEdit = true;
        }
    }

    public function checkRoleEdit()
    {
        //if user is creator
        return (Admin::user()->id === $this->task->creator_id)? true : false;
    }

    public function render()
    {
        return view('livewire.components.task.description');
    }
}
