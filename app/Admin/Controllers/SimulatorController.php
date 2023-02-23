<?php

namespace App\Admin\Controllers;

use App\Models\Subject;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Admin\Repositories\SubjectRepository;
use App\Admin\Repositories\SchoolClassRepository;
use App\Admin\Services\SimulatorService;


class SimulatorController extends Controller
{
    protected $simulatorService;
    protected $subjectRepo;
    protected $schoolClassRepo;
    public function __construct(SimulatorService $simulatorService,
     SubjectRepository $subjectRepo,
     SchoolClassRepository $schoolClassRepo
     ){
        $this->simulatorService = $simulatorService;
        $this->subjectRepo = $subjectRepo;
        $this->schoolClassRepo = $schoolClassRepo;
    }
    public function index() {
        $params = request()->query();
        $levelParam=isset($params['level'])?$params['level']:null; //out int or null
        $gradeParam=isset($params['grade'])?[$params['grade']]:[]; //out array
        
        $gradesFilter= $levelParam? $this->schoolClassRepo->getGradeBySchoolType($levelParam):array_keys(GRADES); //out : array - getGradeBySchoolType()-in:int
        $gradesForSubjectFilter=!empty($gradeParam)?$gradeParam:$gradesFilter; //out arr
        $params['gradeFilter']=$gradesForSubjectFilter;
        $subjectsFilter=$this->subjectRepo->findAllByGrades($gradesForSubjectFilter); //out arr - findAllByGrades() in:array

        $simulators = $this->simulatorService->loadIndex($params);
        return view('admin.simulator.index', [
            'simulators' =>$simulators,  //Config::get('simulator'),
            'schoolLevelsFilter'=>SCHOOL_TYPES,
            'gradesFilter'=>$gradesFilter,
            'subjectsFilter'=>$subjectsFilter,
            'schoolLevelSelected' =>$params['level']??null,
            'gradeSelected' =>$params['grade']??null,
            'subjectSelected' =>$params['subjectId']??null,
            'search' =>$params['search']??'',
        ]);
    }

    public function edit(Request $request, $simulatorId){
        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required',
                'grades' => 'required',
                'subject' => 'required',
                'guide' => 'required',
                // 'url' => 'url'
            ], [
                'title.required' => __('validation.required', ['attribute' => 'Tên bài mô phỏng']),
                'grades.required' => __('validation.required', ['attribute' => 'Khối học']),
                'subject.required' => __('validation.required', ['attribute' => 'Môn học']),
                'guide.required' => __('validation.required', ['attribute' => 'Hướng dẫn']),
                // 'url' => __('validation.active_url', ['attribute' => 'Link mô phỏng']),

            ]);
            $result = $this->simulatorService->updateSimulator($simulatorId, $request);
            return redirect()->route('simulator.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
        $simulator = $this->simulatorService->findById($simulatorId);
        $subjects=$this->subjectRepo->findAllByGrades($simulator->simulatorGrades->pluck('grade')->toArray());

        return view('admin.simulator.form', [
            'title_description' => "Chỉnh sửa bài mô phỏng",
            'url_action' => route('simulator.edit', ['simulatorId' => $simulatorId]),
            'simulator' => $simulator,
            'grades' => GRADES,
            'subjects' => $subjects,
        ]);
    }   

    public function delete($simulatorId){
        $result = $this->simulatorService->deleteById($simulatorId);
        $simulators =$this->simulatorService->findAll();
        $subjectFilter = Subject::where('school_id', null)->get();
        // dd($simulators);
        return view('admin.simulator.index', [
            'simulators' =>$simulators,  //Config::get('simulator'),
            'subjectFilter' => $subjectFilter,
            $result['success'] ? 'success' : 'error', $result['message']
        ]);
    }

    public function view($simulatorId){
        $simulator = $this->simulatorService->findById($simulatorId);
        return view('admin.simulator.view', [
            'title_description' => "Bài mô phỏng ".$simulator->name_simulator,
            'simulator' => $simulator,
        ]);
    }
    public function create(Request $request){
        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required',
                'grades' => 'required',
                'subject' => 'required',
                'guide' => 'required',
                // 'url' => 'url'
            ], [
                'title.required' => __('validation.required', ['attribute' => 'Tên bài mô phỏng']),
                'grades.required' => __('validation.required', ['attribute' => 'Khối học']),
                'subject.required' => __('validation.required', ['attribute' => 'Môn học']),
                'guide.required' => __('validation.required', ['attribute' => 'Hướng dẫn']),
                // 'url' => __('validation.active_url', ['attribute' => 'Link mô phỏng']),
            ]);
            $result = $this->simulatorService->createSimulator($request);
            return redirect()->route('simulator.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
        $subjects=$this->subjectRepo->findAllByGrades(array_keys(GRADES));
        return view('admin.simulator.form', [
            'title_description' => "Chỉnh sửa bài mô phỏng",
            'url_action' => route('simulator.create'),
            'grades' => GRADES,
            'subjects' => $subjects,
        ]);
    }

    public function groupChange(Request $request){
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $grades = !empty($request->data)?$request->data:array_keys(GRADES);
            $subjects=$this->subjectRepo->findAllByGrades($grades);
            return response()->json(['error' => 0, 'subjects' => $subjects]);
        }
    }
}
