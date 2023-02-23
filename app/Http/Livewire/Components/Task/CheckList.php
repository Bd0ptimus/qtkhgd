<?php

namespace App\Http\Livewire\Components\Task;

use App\Models\TaskCheckList;
use App\Admin\Services\CheckListService;
use Livewire\Component;
use App\Models\Task;
use App\Admin\Admin;

class CheckList extends Component
{
    public $title;
    public $description;
    public $task_id;
    public $task;
    public $roleAddCheckList;
    public $percent;
    public $widthBar;
    public $count;
    public $checked = [];

    protected $rules = [
        'title' => 'required',
        'description' => 'nullable',
    ];

    public function mount($task)
    {
        $this->task = $task;
        $this->task_id = $task->id;
        $this->roleAddCheckList = $this->checkRoleAdd();
    }

    public function render()
    {
        $checkLists = Task::findOrFail($this->task_id)->checklists()->get();
        $this->count = count($checkLists);

        return view('livewire.components.task.check-list', [
            'checkLists' => $checkLists
        ]);
    }

    public function updatedChecked()
    {
        if($this->checkRoleEdit()) {
            if ($this->checked && count($this->checked)) {
                $collection = collect($this->checked);

                $checked = $collection->filter(function ($value, $key) {
                    return $value;
                });
                $this->widthBar = count($checked) * 100 / $this->count . '%';
                $this->percent = number_format(count($checked) * 100 / $this->count, 2, ",", ".") . '%';
            }
        }
    }

    public function saveCheckList(CheckListService $checkListService)
    {
        if($this->checkRoleAdd()) {
            $validatedData = $this->validate($this->rules);
            $check = $checkListService->create($validatedData);
            if ($check) {
                TaskCheckList::create([
                    'task_id' => $this->task_id,
                    'check_list_id' => $check->id
                ]);
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Tạo danh mục thành công']);
                $this->resetField();
            } else {
                $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Tạo danh mục thất bại']);
            }
            $this->dispatchBrowserEvent('closeModal');
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

    public function checkRoleAdd()
    {
        //if user is creator
        return (Admin::user()->id === $this->task->creator_id)? true : false;
    }

    public function resetField()
    {
        $this->title = '';
        $this->description = '';
    }
}
