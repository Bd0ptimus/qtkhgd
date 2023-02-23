<?php

namespace App\Admin\Repositories;

use App\Models\Task as TaskModel;
use App\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TaskRepository extends BaseRepository
{
    protected $task;

    /**
     * BaseRepository constructor.
     *
     * @param Model $task
     */
    public function __construct(TaskModel $task)
    {
        parent::__construct($task);
        $this->task = $task;
    }

    public function search($filters)
    {
        $user_id = Admin::user()->id;
        $isAdmin = Admin::user()->isRole('administrator');
        $query = $this->task->newQuery();

        // if is not admin
        if(!$isAdmin) {
            $taskIds = DB::table('tasks_assignee')
                ->where('user_id', $user_id)
                ->get('task_id as id');
            $taskCreateIds = DB::table('tasks')
                ->where('creator_id', $user_id)
                ->get('id');
            $ids = $taskIds->merge($taskCreateIds)->unique()->pluck('id');
            $query->WhereIn('id', $ids);
        }

        if(isset($filters['priorityId']) && $filters['priorityId']) {
            $query->where('priority', $filters['priorityId']);
        }

        if(isset($filters['startDate']) && $filters['startDate']) {
            $query->where('start_date', $filters['startDate']);
        }

        if(isset($filters['dueDate']) && $filters['dueDate']) {
            $query->where('due_date', $filters['dueDate']);
        }

        if(isset($filters['created']) && $filters['created']) {
            $query->where('created_at', 'LIKE', '%' . date('Y-m-d', strtotime($filters['created'])) . '%');
        }

        if(isset($filters['statusId']) && $filters['statusId']) {
            $query->where('status', $filters['statusId']);
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('tasks', request('orderby'))) {
                $query->orderBy(request('orderby'), request('sortorder'));
            }
        }

        //eager load
        $query->with([
            'assigned',
            'followers',
            'comments',
            'checklists',
            'attachments',
            'currentStatus',
            'creator',
        ]);

        //count relationships
        $query->withCount([
            'comments',
            'attachments',
            'checklists',
            'followers',
        ]);

        // Get the results and return them.
        return $query->orderBy('id', 'desc')->paginate($this->task::PAGE_SIZE);
    }


    public function getCountStatus($statusId)
    {
        $query = $this->task->newQuery();

        return $query->where('status', $statusId)->count();
    }

    public function checkCreatorTask($userId, $taskId)
    {
        $query = $this->task->newQuery();

        return $query->where('creator_id', $userId)
                     ->where('id', $taskId)
                     ->first();
    }


    public function getStatusTaskById($taskId)
    {
        $query = $this->task->newQuery();

        return $query->findOrFail($taskId)->id;
    }

    public function getTasks(array $params = [])
    {
        $user_id = Admin::user()->id;
        $isAdmin = Admin::user()->isRole('administrator');
        $query = $this->task->newQuery();

        // if is not admin
        if(!$isAdmin) {
            $taskIds = DB::table('tasks_assignee')
                ->where('user_id', $user_id)
                ->get('task_id as id');
            $taskCreateIds = DB::table('tasks')
                ->where('creator_id', $user_id)
                ->get('id');
            $ids = $taskIds->merge($taskCreateIds)->unique()->pluck('id');
            $query->whereIn('id', $ids);
        }
 
        if(!empty($params)) {
            $query->whereBetween('due_date', [$params['start'], $params['end']]);
        };

        //eager load
        $query->with([
            'assigned',
            'followers',
            'creator',
        ]);

        return $query->get();
    }
}
