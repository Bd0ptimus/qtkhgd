<?php

namespace App\Admin\Controllers\School;

use App\Admin\Services\CommonService;
use App\Models\RegularGroup;
use App\Models\Subject;
use App\Admin\Services\RegularGroupService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TeacherPlanService;
use App\Admin\Services\TeacherLessonService;
use App\Http\Controllers\Controller;
use App\Models\RegularGroupStaff;
use App\Models\School;
use Exception;
use Illuminate\Http\Request;

class RegularGroupController extends Controller
{

    protected $rgService;
    protected $commonService;
    protected $subjectService;
    protected $schoolService;
    protected $teacherPlanService;
    protected $teacherLessonService;

    public function __construct(RegularGroupService $service, CommonService $commonService, 
    SubjectService $subjectService, SchoolService $schoolService, TeacherPlanService $teacherPlanService, TeacherLessonService $teacherLessonService)
    {
        $this->rgService = $service;
        $this->commonService = $commonService;
        $this->subjectService = $subjectService;
        $this->schoolService = $schoolService;
        $this->teacherPlanService = $teacherPlanService;
        $this->teacherLessonService=$teacherLessonService;
    }

    public function index($id)
    {   
        return view('admin.school.regular_group.index', $this->rgService->allBySchool($id));
    }

    public function init($id) {
        $this->rgService->initBySchoolLevel($id);
        return redirect()->back()->with('success', 'Tạo tổ chuyên môn thành công');
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
            $params = $request->all();
            $params['school_level'] = $school->school_type;
            $result = $this->rgService->create($params, $id);
            return redirect()->route('school.regular_group.index', ['id' => $id])
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }

        return view('admin.school.regular_group.form', [
            'url_action' => route('school.regular_group.create', ['id' => $id]),
            'school' => $school,
            'subjects' => $this->subjectService->getAllBySchool($id),
            'grades' => $grades
        ]);
    }

    public function edit(Request $request, $id, $rgId)
    {
        $school = School::find($id);
        $regularGroup = RegularGroup::where(['id' => $rgId, 'school_id' => $id])->with('subjects')->first();
        if ($request->isMethod('post')) {
            if($regularGroup) {
                $params = $request->all();
                $params['school_level'] = $school->school_type;
                $result = $this->rgService->update($rgId, $params);
                return redirect()->route('school.regular_group.index', ['id' => $id])
                    ->with($result['success'] ? 'success' : 'error', $result['message']);
            }
        }

        return view('admin.school.regular_group.form', [
            'url_action' => route('school.regular_group.edit', ['id' => $id, 'rgId' => $rgId]),
            'regularGroup' => $regularGroup,
            'subjects' => $this->subjectService->getAllBySchool($id),
            'school' => $school,
            'grades' => $this->commonService->getGradeBySchoolType($school->school_type)
        ]);
    }

    public function delete(Request $request, $id, $rgId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                RegularGroup::destroy($rgId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function members(Request $request, $schoolId, $rgId) {
        
    }

    public function assignLeaders(Request $request, $schoolId) {
        if($request->isMethod('post')) {
            foreach($request->regularGroups as $index => $group) {
                if($group['leader']) RegularGroupStaff::where([
                    'regular_group_id' => $group['id'], 
                    'staff_id' => $group['leader'],
                ])->update(['member_role' => 1]);
                    
                if(isset($group['deputies']) && count($group['deputies']) > 0) {
                    foreach($group['deputies'] as $deputy) {
                        RegularGroupStaff::where([
                            'regular_group_id' => $group['id'], 
                            'staff_id' => $deputy
                        ])->update(['member_role' => 2]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Cập nhật dữ liệu thành công');
        }

        $data = $this->rgService->allBySchool($schoolId);
        return view('admin.school.regular_group.group_leader', $data);
    }

    public function plan(Request $request, $schoolId) {
        die;    
    }

    public function staffs($schoolId, $rgId) {
        $data['regularGroup'] = $this->rgService->findByGroupId($rgId);
        $data['school'] = $this->schoolService->findById($schoolId);

        return view('admin.school.regular_group.staffs', $data);
    }

    public function reviewTeacherPlans(Request $request, $schoolId, $rgId) {
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['schoolId'] = $schoolId;
        $data['regularGroup'] = $this->rgService->findByGroupId($rgId);
        $data['teacherPlans'] = $this->teacherPlanService->findPendingPlanByGroup($rgId);
        return view('admin.school.regular_group.review_teacher_plans', $data);
    }

    public function teacherPlans(Request $request, $schoolId, $rgId) {
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['schoolId'] = $schoolId;
        $data['regularGroup'] = $this->rgService->findByGroupId($rgId);
        $data['teacherPlans'] = $this->teacherPlanService->findApprovedPlanByGroup($rgId, $request->staffId ?? null);
        $data['selectedStaff'] = $request->staffId ?? null;
        return view('admin.school.regular_group.teacher_plans', $data);
    }

    public function reviewTeacherLessons(Request $request, $schoolId, $rgId){
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['schoolId'] = $schoolId;
        $data['regularGroup'] = $this->rgService->findByGroupId($rgId);
        $teacherPlanApproved= $this->teacherPlanService->findApprovedPlanByGroup($rgId);
        $data['teacherLessons']= $this->teacherLessonService->findSubmittedByPlans($teacherPlanApproved);
        // dd( $data['teacherLessons']);
        return view('admin.school.regular_group.review_teacher_lessons', $data);

    }
}
