<?php

namespace App\Admin\Controllers\School;

use App\Models\RegularGroup;
use App\Models\Subject;
use App\Admin\Services\RegularGroupService;
use App\Admin\Services\SchoolClassService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\StaffService;
use App\Admin\Services\SubjectService;
use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class TeachingAssignmentController extends Controller
{

    protected $rgService;
    protected $schoolClassService;
    protected $schoolService;
    protected $subjectService;
    protected $staffService;
    
    public function __construct(
        RegularGroupService $rgService,
        SchoolClassService $schoolClassService,
        SchoolService $schoolService,
        SubjectService $subjectService,
        StaffService $staffService
    ) {
        $this->rgService = $rgService;
        $this->schoolClassService = $schoolClassService;
        $this->schoolService = $schoolService;
        $this->subjectService = $subjectService;
        $this->staffService = $staffService;
    }

    public function index($id)
    {   
        return view('admin.school.regular_group.index', $this->rgService->allBySchool($id));
    }

    public function homeroomTeacher(Request $request, $schoolId) {
        
        if ($request->isMethod('post')) {
           $result = $this->schoolClassService->bulkUpdate($request->classes);
           return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
        }
        
        $school = $this->schoolService->findById(($schoolId));
        
        return view('admin.school.teaching_assignment.homeroom_teacher', [
            'school' => $school,
            'breadcrumbs' => [
                ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
                ['name' => 'Phân công giáo viên chủ nhiệm'],
            ]
        ]);
    }

    public function teacherClasses(Request $request, $schoolId) {

    }
    
    public function classSubjects(Request $request, $schoolId) {

        if ($request->isMethod('post')) {
            //TODO check number off lesson is valid
            $school = $this->schoolService->findById($schoolId);
            $classId = $request->class;
            $maximumLessons = $school->getMaximumLesson();
            $checkClassLesson = $this->schoolClassService->checkClassLesson($request->classSubjects, $maximumLessons);
            $checkTeacherLesson = $this->staffService->checkTeacherLesson($schoolId, $classId, $request->classSubjects, $maximumLessons);
            if($checkClassLesson['success'] == true && $checkTeacherLesson['success'] == true) {
                foreach($request->classSubjects as $classSubject) {
                    if($classSubject['staff_id'] !== null) {
                        ClassSubject::find($classSubject['id'])->update($classSubject);
                    }
                }
                return redirect()->back()->with('success', 'Cập nhật môn học cho lớp thành công');
            } else {
                return redirect()->back()->with('error',$checkClassLesson['success'] == false ?  $checkClassLesson['message'] :  $checkTeacherLesson['message']);
            }
            
        }

        $school = $this->schoolService->findById($schoolId);
        $classId = $request->class ?? $school->classes[0]->id;
        $selectedClass = $this->schoolClassService->findById($classId, $schoolId);
        // do danh sách trên sẽ lấy cả giáo viên phụ trách bộ môn ở cả 2 trường hiện tại và trường liên kết
        // gỡ bỏ những giáo viên ở trường liên kết mà ko được cấu hình liên kết
        foreach ($selectedClass->classSubjects as $classSubject) {
            foreach($classSubject->staffSubjects as $key =>  $staffSubject) {
                if($staffSubject->staff && in_array($selectedClass->grade, $staffSubject->staff->staffGrades->pluck('grade')->toArray())) {
                    if ($school->id != $staffSubject->staff->school_id && !$staffSubject->staff->is_linking_staff) {
                        unset($classSubject->staffSubjects[$key]);
                    }
                }
            }
        }
        //if(count($selectedClass->subjects) == 0 ) {
            $selectedClass = $this->schoolClassService->autoGenerateSubject($selectedClass, $school);
        //}

        return view('admin.school.teaching_assignment.class_subjects', [
            'selectedClass' => $selectedClass,
            'school' => $school
        ]);
    }
}
