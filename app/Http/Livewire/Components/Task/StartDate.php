<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Models\Task;
use App\Admin\Services\TaskService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Component;
use Carbon\Carbon;


class StartDate extends Component
{
    public $start_date;
    public $task;
    public $roleEdit = false;

    public function mount($task)
    {
        $this->task = $task;
        $this->start_date = $task->start_date;
        $this->roleEdit = $this->checkRoleEdit();
    }

    public function updatedStartDate($val)
    {

        if($val || $this->start_date) {
            // check if user have role edit and start_date new !== start_date old
            if ($this->roleEdit && $this->task->start_date != $this->start_date) {
                DB::beginTransaction();
                try {
                    $this->start_date = $this->dateFormat($this->start_date, 'd/m/Y', 'Y-m-d');
                    // update pristart_dateority task
                    $check = Task::where('id', $this->task->id)
                        ->update([
                            'start_date' => $this->start_date
                        ]);
                    if($check) {
                        $this->start_date = $this->dateFormat($this->start_date, 'Y-m-d', 'd/m/Y');
                    }
                    DB::commit();
                    //session()->flash('success', 'Cập nhật thành công');
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Cập nhật ngày bắt đầu thành công']);
                } catch (Exception $ex) {
                    //session()->flash('error', 'Cập nhật thất bại');
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Cập nhật ngày bắt đầu thất bại']);
                    DB::rollBack();
                    if(env('APP_ENV') !== 'production') dd($ex);
                    Log::error('[update task start date]'.$ex->getMessage());
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
        return view('livewire.components.task.start-date');
    }

    public function dateFormat($date, $formatStart, $formatEnd)
    {
        return Carbon::createFromFormat($formatStart, $date)
            ->format($formatEnd);
    }
}
