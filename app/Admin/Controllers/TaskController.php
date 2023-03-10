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
            'title.required' => __('validation.required', ['attribute' => 'ti??u ????? nhi???m v???']),
            'description.required' => __('validation.required', ['attribute' => 'm?? t??? nhi???m v???']),
            //'assignee_ids.required' => __('validation.required', ['attribute' => 'ng?????i ??u???c ch??? ?????nh']),
            'priority.required' => __('validation.required', ['attribute' => '????? ??u ti??n']),
            'start_date.required' => __('validation.required', ['attribute' => 'ng??y b???t ?????u']),
        ]);

        $request = $request->all();

        if ($this->taskService->create($request)) {
            return redirect()->route('tasks.index')->with('success', 'T???o nhi???m v??? th??nh c??ng');
        } else {
            return redirect()->back()->with('error', 'T???o nhi???m v??? kh??ng th??nh c??ng. Vui l??ng ki???m tra l???i th??ng tin.');
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
            return redirect()->route('tasks.show', $request['task_id'])->with('success', 'B??nh lu???n th??nh c??ng');
        } else {
            return redirect()->back()->with('error', 'B??nh lu???n kh??ng th??nh c??ng. Vui l??ng ki???m tra l???i th??ng tin.');
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
            return redirect()->route('tasks.index')->with('success', 'S???a nhi???m v??? th??nh c??ng');
        } else {
            return redirect()->back()->with('error', 'S???a nhi???m v??? kh??ng th??nh c??ng. Vui l??ng ki???m tra l???i th??ng tin.');
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
            return redirect()->route('tasks.index')->with('success', 'Xo?? nhi???m v??? th??nh c??ng');
        } else {
            return redirect()->back()->with('error', 'Xo?? nhi???m v??? kh??ng th??nh c??ng. Vui l??ng ki???m tra l???i th??ng tin.');
        }
    }

    public function toJsonTasks(Request $request)
    {
        return $this->taskService->getTasks($request->only(['start', 'end']));
    }
}
