<?php

namespace App\Http\Livewire\Components\Task;

use App\Models\Attachment;
use App\Models\Task;
use App\Admin\Services\TaskService;
use Livewire\Component;
use App\Admin\Admin;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Attachments extends Component
{
    use WithFileUploads;

    public $files = [];
    public $chooses = [];
    public $task_id;
    public $task;
    public $roleAddAttachment;
    public $roleDelAttachment;
    public $path = 'app/public/';

    public function mount($task)
    {
        $this->task = $task;
        $this->task_id = $task->id;
        $this->roleAddAttachment = $this->checkRoleEdit();
        $this->roleDelAttachment = $this->checkRoleDel();
    }

    public function render()
    {
        return view('livewire.components.task.attachments', [
            'attachments' => Task::findOrFail($this->task_id)->attachments()->get()
        ]);
    }

    public function checkRoleDel()
    {
        return $this->task->creator_id === Admin::user()->id;
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

    public function deleteFileById($id, TaskService $taskService)
    {
        if($taskService->deleteAttachmentById($id)) {
            //session()->flash('success', 'Xoá tệp tin thành công');
            $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Xoá tệp tin thành công']);
        } else {
            //session()->flash('success', 'Xoá tệp tin thất bại');
            $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Xoá tệp tin thất bại']);
        }
    }

    public function checkRoleAdd()
    {
        //if user is creator
        return (Admin::user()->id === $this->task->creator_id)? true : false;
    }

    public function updatedFiles()
    {
        if($this->files && count($this->files)) {
            $this->validate([
                'files.*' => 'file|max:25600', // 25MB Max
            ]);

            foreach ($this->files as $key => $file) {
                $file_name = $file->getClientOriginalName();
                array_push($this->chooses, $file_name) ;
            }
        }
    }

    public function addFiles(TaskService $taskService)
    {
        if($this->files && count($this->files)) {
            $this->validate([
                'files.*' => 'file|max:5120', // 5MB Max
            ]);

            foreach ($this->files as $key => $file) {
                $file_name = $file->getClientOriginalName();
                Storage::putFileAs('', $file, $file_name);

                $attachment = new Attachment();
                $attachment->path = $this->path . $file_name;
                $attachment->name = $file_name;
                if($check = $this->task->attachments()->save($attachment)) {
                    //send mail
                    $taskService->sendMailTaskUploadFile($this->task_id, [
                        "filename" => $file_name,
                        "attachment_id" => $check->id,
                        "uploader_name" => Admin::user()->name,
                    ]);
                    //session()->flash('success', 'Tải tệp tin thành công');
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Tải tệp tin thành công']);
                } else {
                    //session()->flash('error', 'Tải tệp tin thất bại');
                    $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Tải tệp tin thất bại']);
                }
            }
            $this->resetFile();
        }
    }

    public function resetFile()
    {
        $this->files = [];
        $this->chooses = [];
    }
}
