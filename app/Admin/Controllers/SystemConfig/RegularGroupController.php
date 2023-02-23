<?php

namespace App\Admin\Controllers\SystemConfig;

use App\Http\Controllers\Controller;
use App\Admin\Admin;
use App\Models\SchoolClass;
use DB;
use Illuminate\Http\Request;
use App\Models\RegularGroup;
use Exception;
use App\Admin\Services\RegularGroupService;

class RegularGroupController extends Controller
{
    protected $rgService;

    public function __construct(RegularGroupService $service)
    {
        $this->rgService = $service;
    }

    public function index() {
        return view('admin.sysconf.regular_group.index', $this->rgService->index());
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required',
                'school_level' => 'required',
                'subjects' => 'required',
                //'grades' => 'required',
            ], [
                'name.required' => __('validation.required', ['attribute' => 'tên tổ chuyên môn']),
                'school_level.required' => __('validation.required', ['attribute' => 'cấp học']),
                'subjects.required' => __('validation.required', ['attribute' => 'môn học']),
                //'grades.required' => __('validation.required', ['attribute' => 'khối']),
            ]);

            $result = $this->rgService->create($request->all());
            return redirect()->route('sysconf.regular_group.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }

        return view('admin.sysconf.regular_group.form', [
            'url_action' => route('sysconf.regular_group.create'),
            'grades' => SchoolClass::GRADES
        ]);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required',
                'school_level' => 'required',
                'subjects' => 'required',
                //'grades' => 'required',
            ], [
                'name.required' => __('validation.required', ['attribute' => 'tên tổ chuyên môn']),
                'school_level.required' => __('validation.required', ['attribute' => 'cấp học']),
                'subjects.required' => __('validation.required', ['attribute' => 'môn học']),
                //'grades.required' => __('validation.required', ['attribute' => 'khối']),
            ]);

            $result = $this->rgService->update($id,$request->all());
            return redirect()->route('sysconf.regular_group.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }

        return view('admin.sysconf.regular_group.form', [
            'url_action' => route('sysconf.regular_group.edit', ['id' => $id]),
            'regularGroup' => RegularGroup::find($id), 
            'grades' => SchoolClass::GRADES
        ]);
    }

    public function delete(Request $request, $id) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                RegularGroup::destroy($id);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}