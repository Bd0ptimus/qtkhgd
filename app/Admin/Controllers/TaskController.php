<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Services\TaskService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $statusId = request()->query('statusId', null);
        $priorityId = request()->query('priorityId', null);
        $startDate = request()->query('startDate', null);
        $dueDate = request()->query('dueDate', null);
        $created = request()->query('created', null);
        $request = $request->all();

        $result = $this->taskService->index($request);
        $result['statusId'] = $statusId;
        $result['priorityId'] = $priorityId;
        $result['startDate'] = $startDate;
        $result['dueDate'] = $dueDate;
        $result['created'] = $created;
        $result['user_id'] = Admin::user()->id;
        $result['isAdmin'] = Admin::user()->isAdministrator();

        return $this->renderView('admin.tasks.index', $result);
    }

    public function create(Request $request)
    {
        $result = $this->taskService->index($request);
        $result['user_id'] = Admin::user()->id;
        $result['isAdmin'] = Admin::user()->isAdministrator();

        return $this->renderView('admin.tasks.create', $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            //'assignee_ids' => 'required',
            'priority' => 'required',
            'start_date' => 'required',
        ], [
            'title.required' => __('validation.required', ['attribute' => 'tiêu đề nhiệm vụ']),
            'description.required' => __('validation.required', ['attribute' => 'mô tả nhiệm vụ']),
            //'assignee_ids.required' => __('validation.required', ['attribute' => 'người đuợc chỉ định']),
            'priority.required' => __('validation.required', ['attribute' => 'độ ưu tiên']),
            'start_date.required' => __('validation.required', ['attribute' => 'ngày bắt đầu']),
        ]);

        $request = $request->all();

        if ($this->taskService->create($request)) {
            return redirect()->route('tasks.index')->with('success', 'Tạo nhiệm vụ thành công');
        } else {
            return redirect()->back()->with('error', 'Tạo nhiệm vụ không thành công. Vui lòng kiểm tra lại thông tin.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeComment(Request $request)
    {
        $request = $request->all();

        if ($this->taskService->createComment($request)) {
            return redirect()->route('tasks.show', $request['task_id'])->with('success', 'Bình luận thành công');
        } else {
            return redirect()->back()->with('error', 'Bình luận không thành công. Vui lòng kiểm tra lại thông tin.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->taskService->show($id);
        return $this->renderView('admin.tasks.show', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $request = $request->all();

        if ($this->taskService->update($id, $request)) {
            return redirect()->route('tasks.index')->with('success', 'Sửa nhiệm vụ thành công');
        } else {
            return redirect()->back()->with('error', 'Sửa nhiệm vụ không thành công. Vui lòng kiểm tra lại thông tin.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->taskService->deleteById($id)) {
            return redirect()->route('tasks.index')->with('success', 'Xoá nhiệm vụ thành công');
        } else {
            return redirect()->back()->with('error', 'Xoá nhiệm vụ không thành công. Vui lòng kiểm tra lại thông tin.');
        }
    }

    public function toJsonTasks(Request $request)
    {
        return $this->taskService->getTasks($request->only(['start', 'end']));
    }
}
