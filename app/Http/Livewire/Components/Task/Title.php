<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Admin\Services\TaskService;
use Livewire\Component;

class Title extends Component
{
    public $task;
    public $title;
    public $task_id;
    public $showEdit = false;
    public $roleEdit = false;

    protected $rules = [
        'title' => 'required'
    ];

    public function mount($task)
    {
        $this->task = $task;
        $this->task_id = $task->id;
        $this->title = $task->title;
        $this->roleEdit = $this->checkRoleEdit();
    }

    public function updateTaskTitle(TaskService $taskService)
    {
        if($this->checkRoleEdit()) {
            $validatedData = $this->validate($this->rules);

            $updated = $taskService->updateOnlyFiled($this->task_id, $validatedData);

            if ($updated) {
                $this->cancelUpdateTaskTitle();
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật tiêu đề thành công']);
            } else {
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật tiêu đề thất bại']);
            }
        }
    }

    public function cancelUpdateTaskTitle()
    {
        $this->showEdit = false;
    }

    public function showEditTitle()
    {
        if($this->checkRoleEdit()) $this->showEdit = true;
    }

    public function checkRoleEdit()
    {
        //if user is creator
        return (Admin::user()->id === $this->task->creator_id)? true : false;
    }

    public function render()
    {
        return view('livewire.components.task.title');
    }
}
