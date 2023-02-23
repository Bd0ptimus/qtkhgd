<?php

namespace App\Admin\Controllers\SystemConfig;

use App\Http\Controllers\Controller;

use App\Admin\Admin;
use DB;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Admin\Services\SubjectService;
use Exception;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectService $service)
    {
        $this->subjectService = $service;
    }

    public function index(Request $request) {
        return view('admin.sysconf.subject.index',$this->subjectService->index());
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:subject,name',
                'grades' => 'required',
            ], [
                'name.required' => __('validation.required', ['attribute' => 'tên môn học']),
                'name.unique' => __('validation.unique', ['attribute' => 'tên môn học']),
                'grades.required' => __('validation.required', ['attribute' => 'khối']),
            ]);

            $result = $this->subjectService->create($request->all());

            return redirect()->route('sysconf.subject.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
        return view('admin.sysconf.subject.form', [
            'url_action' => route('sysconf.subject.create')
        ]);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required',
                'grades' => 'required',
            ], [
                'name.required' => __('validation.required', ['attribute' => 'tên môn học']),
                'grades.required' => __('validation.required', ['attribute' => 'khối']),
            ]);

            $result = $this->subjectService->update($id, $request->all());
            return redirect()->route('sysconf.subject.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
        return view('admin.sysconf.subject.form', [
            'url_action' => route('sysconf.subject.edit', ['id' => $id]),
            'subject' => Subject::with('grades')->find($id)
        ]);
    }

    public function delete(Request $request, $id) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                Subject::destroy($id);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function subjectByGrade(Request $request) {
        if ($request->isMethod('post')) {
            foreach($request->grades as $grade => $data) {
                $this->subjectService->updateSubjectByGrade($grade, $data['subjects']);
            }
            return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
        }
        $data = $this->subjectService->allGroupByGrade();
        return view('admin.sysconf.subject.subject_by_grade',['data' => $data]);
    }
}