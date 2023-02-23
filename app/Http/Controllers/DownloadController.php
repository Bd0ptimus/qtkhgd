<?php

namespace App\Http\Controllers;

use App\Admin\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function downloadAttachment($id)
    {
        $attachment = $this->taskService->getAttachmentById($id);
        //confirm thumb exists
        if($attachment && $attachment->path != '') {
            if (Storage::exists($attachment->name)) {
                session()->flash('success', 'Tải tệp tin thành công');
                return response()->download(storage_path($attachment->path));
            }
        } else {
            session()->flash('error', 'Tải tệp tin thất bại');
        }
        return false;
    }
}
