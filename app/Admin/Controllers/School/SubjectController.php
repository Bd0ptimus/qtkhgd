<?php

namespace App\Admin\Controllers\School;

use App\Admin\Services\CommonService;
use App\Models\GradeSubject;
use App\Models\Subject;
use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use App\Admin\Services\SubjectService;
use Exception;

class SubjectController extends Controller
{

    protected $subjectService;
    protected $commonService;

    public function __construct(SubjectService $service, CommonService $commonService)
    {
        $this->subjectService = $service;
        $this->commonService = $commonService;
    }

    public function index($id)
    {   
        return view('admin.school.subject.index', $this->subjectService->allBySchool($id));
    }

    public function create(Request $request, $id)
    {
        $school = School::find($id);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        // fitler by shool type
        $grades = $this->commonService->getGradeBySchoolType($school->school_type);

        if ($request->isMethod('post')) {
            $result = $this->subjectService->create($request->all(), $id);
            return redirect()->route('school.subject.index', ['id' => $id])
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }

        return view('admin.school.subject.form', [
            'url_action' => route('school.subject.create', ['id' => $id]),
            'grades' => $grades
        ]);
    }

    public function edit(Request $request, $id, $subject_id)
    {
        $school = School::find($id);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        // fitler by shool type
        $grades = $this->commonService->getGradeBySchoolType($school->school_type);

        $subject = Subject::where(['id' => $subject_id, 'school_id' => $id])->with('grades')->first();
        if ($request->isMethod('post')) {
            if($subject) {
                $result = $this->subjectService->update($id, $request->all());
                return redirect()->route('school.subject.index', ['id' => $id])
                    ->with($result['success'] ? 'success' : 'error', $result['message']);
            }
        }

        return view('admin.school.subject.form', [
            'url_action' => route('school.subject.edit', ['id' => $id, 'subject_id' => $subject_id]),
            'subject' => $subject,
            'grades' => $grades
        ]);
    }

    public function delete(Request $request, $id, $subject_id) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                Subject::destroy($subject_id);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function subjectByGrade(Request $request, $schoolId) {
        $data = $this->subjectService->getBySchoolGroupByGrade($schoolId);
        return view('admin.school.subject.subject_by_grade',['data' => $data]);
    }
}
