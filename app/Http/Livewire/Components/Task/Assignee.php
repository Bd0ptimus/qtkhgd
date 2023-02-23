<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Models\TaskAssignee;
use App\Admin\Services\TaskService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Component;

class Assignee extends Component
{
    public $task;
    public $users;
    public $assignee;
    public $assignees;
    public $assigned;
    public $roleEdit = false;
    public $showAction = false;

    public function mount($task, $users)
    {
        $this->task = $task;
        $this->users = !is_array($users) ? $users->toArray() : $users;
        $this->assignee = $this->getListUserAssigned();
        $this->roleEdit = $this->checkRoleEdit();
        $this->assigned = $this->getListUserAssigned();
    }

    public function updatedAssignee()
    {
        $this->assignees = json_encode($this->assignee);
        $this->emit('assigneeSuccess');
        $this->showAction = true;
    }

    public function handleUpdateAssignee(TaskService $taskService)
    {
        if ($this->roleEdit) {
            if ($this->assignees) {
                DB::beginTransaction();
                try {
                    TaskAssignee::where('task_id', $this->task->id)->delete();
                    foreach (json_decode($this->assignees) as $item) {
                        TaskAssignee::create([
                            'user_id' => $item,
                            'task_id' => $this->task->id,
                        ]);
                    }
                    DB::commit();
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật người chỉ định thành công']);
                    // send mail for new user assigned
                    $taskService->sendMailTaskUpdateAssignee($this->task->id, json_decode($this->assignees));
                    $this->updatedAssignee();
                } catch (Exception $ex) {
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật người chỉ định thất bại']);
                    DB::rollBack();
                    if (env('APP_ENV') !== 'production') dd($ex);
                    Log::error('[update task due date]' . $ex->getMessage());
                }
            }
            $this->assignees = [];
            $this->showAction = false;
            $this->emit('alert_remove');
        }
    }

    public function cancelUpdateAssignee()
    {
        $this->showAction = false;
    }

    public function checkRoleEdit()
    {
        //if user is creator
        return (Admin::user()->id === $this->task->creator_id) ? true : false;
    }

    public function checkDiffAssignee($old, $new)
    {
        // flag = true check assignee remove
        $collection = collect($old)->unique();
        $diff = $collection->diff($new);
        return $diff->toArray();
    }

    public function getListUserAssigned()
    {
        return explode(',', collect($this->task->assigned)->implode('id', ','));
    }

    public function render()
    {
        return view('livewire.components.task.assignee');
    }
}
