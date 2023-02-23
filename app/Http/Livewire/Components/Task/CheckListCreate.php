<?php

namespace App\Http\Livewire\Components\Task;

use App\Admin\Admin;
use App\Admin\Services\CheckListService;
use Livewire\Component;

class CheckListCreate extends Component
{
    public $title;
    public $description;
    public $checkLists = [];

    protected $rules = [
        'title' => 'required',
        'description' => 'nullable',
    ];

    public function saveCheckList(CheckListService $checkListService)
    {
        $validatedData = $this->validate($this->rules);
        $check = $checkListService->create($validatedData);

        if($check) {
            $this->checkLists = collect($this->checkLists)->push($check)->toArray();
            $this->resetField();
            $this->dispatchBrowserEvent('showFlashMessage', ['status' => 1, 'message' => 'Tạo danh mục thành công']);
        } else {
            $this->dispatchBrowserEvent('showFlashMessage', ['status' => 0, 'message' => 'Tạo danh mục thất bại']);
        }
        $this->dispatchBrowserEvent('closePopup');
    }

    public function resetField()
    {
        $this->title = '';
        $this->description = '';
    }

    public function closePopup()
    {
        $this->dispatchBrowserEvent('closePopup');
    }

    public function render()
    {
        return view('livewire.components.task.check-list-create');
    }
}
