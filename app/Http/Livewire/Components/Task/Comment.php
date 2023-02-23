<?php

namespace App\Http\Livewire\Components\Task;

use App\Models\TaskComment;
use App\Admin\Services\TaskCommentService;
use App\Admin\Services\TaskService;
use Livewire\Component;
use App\Admin\Admin;

class Comment extends Component
{
    public $comment;
    public $task_id;
    public $creator_id;

    protected $rules = [
        'comment' => 'required',
    ];

    public function mount($taskId)
    {
        $this->task_id = $taskId;
    }

    public function render()
    {
        $comments = TaskComment::with('creator')
            ->where('task_id', $this->task_id)
            ->orderBy('id', 'desc')
            ->paginate(TaskComment::PAGE_SIZE);

        return view('livewire.components.task.comment', ['comments' => $comments]);
    }

    public function saveComment(TaskCommentService $commentService, TaskService $taskService)
    {
        $validatedData = $this->validate($this->rules);
        $validatedData['task_id'] = $this->task_id;
        $validatedData['creator_id'] = Admin::user()->id;

        if($commentService->createComment($validatedData)) $this->resetField();
    }

    public function resetField()
    {
        $this->comment = '';
    }
}
