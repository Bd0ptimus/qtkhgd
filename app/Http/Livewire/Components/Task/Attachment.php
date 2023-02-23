<?php

namespace App\Http\Livewire\Components\Task;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Attachment extends Component
{
    use WithFileUploads;

    public $files = [];
    public $uploaded = [];
    public $path = 'app/public/';

    public function render()
    {
        return view('livewire.components.task.attachment');
    }

    public function updatedFiles()
    {
        if($this->files && count($this->files)) {
            $this->validate([
                'files.*' => 'file|max:25600', // 25MB Max
            ]);
            foreach ($this->files as $key => $file) {
                $file_name = $file->getClientOriginalName();
                Storage::putFileAs('', $file, $file_name);
                array_push($this->uploaded, [
                    'name' => $file_name,
                    'path' => $this->path . $file_name
                ]) ;
            }
            $this->resetFile();
        }
    }

    public function resetFile()
    {
        $this->files = [];
    }
}
