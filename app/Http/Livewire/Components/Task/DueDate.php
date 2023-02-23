<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Component;

class DueDate extends Component
{
    public $due_date;
    public $task;
    public $roleEdit = false;

    public function mount($task)
    {
        $this->task = $task;
        $this->due_date = $task->due_date?$this->dateFormat($task->due_date, 'Y-m-d', 'd/m/Y') : '';
        $this->roleEdit = $this->checkRoleEdit();
    }

    public function updatedDueDate($val)
    {

        if($val || $this->due_date) {
            // check if user have role edit and due_date new !== due_date old
            if ($this->roleEdit && $this->task->due_date != $this->due_date) {
                DB::beginTransaction();
                try {
                    $this->due_date = $this->dateFormat($this->due_date, 'd/m/Y', 'Y-m-d');
                    // update pridue_dateority task
                    $check = Task::where('id', $this->task->id)
                        ->update([
                            'due_date' => $this->due_date
                        ]);

                    if($check) {
                        $this->due_date = $this->dateFormat($this->due_date, 'Y-m-d', 'd/m/Y');
                    }
                    DB::commit();
                    //session()->flash('success', 'Cập nhật thành công');
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật hạn hoàn thành thành công']);
                } catch (Exception $ex) {
                    //session()->flash('error', 'Cập nhật thất bại');
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật hạn hoàn thành thất bại']);
                    DB::rollBack();
                    if(env('APP_ENV') !== 'production') dd($ex);
                    Log::error('[update task due date]'.$ex->getMessage());
                }
                $this->emit('alert_remove');
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
        return view('livewire.components.task.due-date');
    }

    public function dateFormat($date, $formatStart, $formatEnd)
    {
        return \Carbon\Carbon::createFromFormat($formatStart, $date)
            ->format($formatEnd);
    }
}
