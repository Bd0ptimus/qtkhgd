<?php

namespace App\Http\Livewire\Components\Task;

use App\Models\TaskFollower;
use App\Admin\Services\TaskService;
use App\Admin\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Component;

class Follower extends Component
{
    public $task;
    public $users;
    public $follower;
    public $followers;
    public $isFollower;
    public $roleEdit = false;
    public $showAction = false;

    public function mount($task, $users)
    {
        $this->task = $task;
        $this->users = !is_array($users) ? $users->toArray() : $users;
        $this->follower = $this->getListUserFollower();
        $this->roleEdit = $this->checkRoleEdit();
        $this->isFollower = $this->getListUserFollower();
    }

    public function updatedFollower()
    {
        $this->showAction = true;
        $this->followers = json_encode($this->follower);
        $this->emit('followerSuccess');
    }

    public function handleUpdateFollower(TaskService $taskService)
    {
        if ($this->roleEdit) {
            if ($this->followers) {
                DB::beginTransaction();
                try {
                    TaskFollower::where('task_id', $this->task->id)->delete();
                    foreach (json_decode($this->followers) as $item) {
                        TaskFollower::create([
                            'user_id' => $item,
                            'task_id' => $this->task->id,
                        ]);
                    }
                    DB::commit();
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật người theo dõi thành công']);
                    $this->updatedFollower();
                } catch (Exception $ex) {
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật người theo dõi thất bại']);
                    DB::rollBack();
                    if(env('APP_ENV') !== 'production') dd($ex);
                    Log::error('[update task follower]'.$ex->getMessage());
                }
            }

            $this->followers = [];
            $this->showAction = false;
        }
    }

    public function cancelUpdateFollower()
    {
        $this->showAction = false;
    }

    public function checkRoleEdit()
    {
        //if user is creator
        return (Admin::user()->id === $this->task->creator_id) ? true : false;
    }

    public function checkDiffFollower($old, $new)
    {
        // flag = true check follower remove
        $collection = collect($old)->unique();
        $diff = $collection->diff($new);
        return $diff->toArray();
    }

    public function getListUserFollower()
    {
        return explode(',', collect($this->task->followers)->implode('id', ','));
    }
    public function render()
    {
        return view('livewire.components.task.follower');
    }
}
