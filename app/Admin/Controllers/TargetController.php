<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Services\ImportWordService;
use App\Admin\Services\SchoolPlanService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Admin\Permission;
use App\Models\Target;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    protected $schoolService;
    protected $schoolPlanService;
    protected $importWordService;
    protected $subjectService;
    protected $taskService;
    
    public function __construct(
        SchoolService $schoolService,
        SchoolPlanService $schoolPlanService,
        ImportWordService $importWordService,
        SubjectService $subjectService,
        TaskService $taskService
    ) {
        $this->schoolService = $schoolService;
        $this->schoolPlanService = $schoolPlanService;
        $this->importWordService = $importWordService;
        $this->subjectService = $subjectService;
        $this->taskService = $taskService;
    }

    public function index() {
        $targets = Target::where([
            'school_id' => null,
            'staff_id' => null,
        ])->get();

        return view('admin.target.index', [
            'targets' => $targets,
            'permission' => Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]),
        ]);
    }

    public function create(Request $request) {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM])) {
            return Permission::error();
        }
        if ($request->isMethod('post')) {
            $messages = [
                'required' => 'Chỉ tiêu không được để trống',
                'numeric'=>'Chỉ tiêu phải là số',
                'min'=>'Chỉ tiêu phải lớn hơn 0',
                'max'=>'Chỉ tiêu phải bé hơn 100',
              ];
            $validator = Validator::make($request->all(), [
                'final_target' => 'required|numeric|min:1|max:100',
            ], $messages );

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            Target::create($request->all());
            return redirect()->route('target.index')->with('success', 'Đã lưu chỉ tiêu năm học');
        }
        return view('admin.target.form', [
            'create' => true,
        ]);
    }

    public function edit(Request $request,$targetId) {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM])) {
            return Permission::error();
        }

        $data['target'] = Target::find($targetId);
        
        if ($request->isMethod('post')) {
            $data['target']->update([
                'title' => $request->title,
                'type' => $request->type,
                'final_target' => $request->final_target,
                'school_type' => $request->school_type,
                'description' => $request->description,
                'solution' => $request->solution,
            ]);
            return redirect()->route('target.index')->with('success', 'Đã lưu chỉ tiêu');
        }
        $view = 'admin.target.form';

        return view($view, $data);
    }

    public function delete($targetId) {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM])) {
            return Permission::error();
        }
        
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                Target::destroy($targetId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
    
}
