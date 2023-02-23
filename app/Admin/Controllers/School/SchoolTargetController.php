<?php

namespace App\Admin\Controllers\School;

use App\Admin\Admin;
use App\Admin\Services\ImportWordService;
use App\Admin\Services\SchoolPlanService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TaskService;
use App\Admin\Services\StaffService;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolPlan;
use App\Models\SchoolStaff;
use App\Models\Target;
use App\Models\TargetPoint;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class SchoolTargetController extends Controller
{
    protected $schoolService;
    protected $schoolPlanService;
    protected $importWordService;
    protected $subjectService;
    protected $taskService;
    protected $breadcrumbs;
    
    public function __construct(
        SchoolService $schoolService,
        SchoolPlanService $schoolPlanService,
        ImportWordService $importWordService,
        SubjectService $subjectService,
        TaskService $taskService,
        StaffService $staffService
    ) {
        $this->schoolService = $schoolService;
        $this->schoolPlanService = $schoolPlanService;
        $this->importWordService = $importWordService;
        $this->subjectService = $subjectService;
        $this->taskService = $taskService;
        $this->staffService = $staffService;

        $this->breadcrumbsRoot = [
            ['name' => trans('admin.home'), 'link' => route('admin.home')]
        ];
    }

    public function index($schoolId) {
        $school = School::with(['targets' => function($query) {
            $query->where('staff_id',null);
        }, 'targets.staffTargets'])->find($schoolId);
        $targets = $school->targets; 
        $mainPoint = TargetPoint::find(4);
        if (Admin::user()->inRoles([ROLE_GIAO_VIEN])) {
            $staff = SchoolStaff::where([
                'staff_code' => Admin::user()->username
            ])->with(['targetPoints', 'targetPoints.teacherClass', 'targetPoints.teacherSubject'])->first();
            if($staff) $targetPoints = $staff->targetPoints;
            $breadcrumbs = [
                ['name' => trans('admin.home'), 'link' => route('admin.home')],
                ['name' => 'Danh sách tiêu chí'],
            ];
            return view('admin.school.target.index_target_point', [
                'school' => $school,
                'targetPoints' =>  $targetPoints,
                'breadcrumbs' => $breadcrumbs
            ]);
        }
        $breadcrumbs = [
            ['name' => trans('admin.home'), 'link' => route('admin.home')],
            ['name' => 'Danh sách chỉ tiêu của trường'],
        ];
        return view('admin.school.target.index', [
            'school' => $school,
            'targets' =>  $targets,
            'breadcrumbs' => $breadcrumbs
        ]);
    }


    public function summaryTarget(Request $request, $schoolId, $targetId) {
        $data['target'] = Target::find($targetId);
        $data['school'] = $this->schoolService->findById($schoolId);
        array_push($this->breadcrumbsRoot,
            ['name' => 'Danh sách chỉ tiêu của trường', 'link'=>route('school.target.index',['id'=>$schoolId])],
            ['name' => 'Thống kê chỉ tiêu']);
            
        $data['breadcrumbs']=$this->breadcrumbsRoot;
        return view('admin.school.target.summary_target', $data);
    }

    public function create(Request $request, $schoolId) {
        if ($request->isMethod('post')) {
            $this->schoolService->createTarget($request);
            return redirect()->route('school.target.index', ['id' => $schoolId ])->with('success', 'Đã lưu chỉ tiêu năm học');
        }

        $systemTargets = Target::where([
            'school_id' => null,
            'staff_id' => null,
        ])->get();

        $school = $this->schoolService->findById($schoolId);

        array_push($this->breadcrumbsRoot,
            ['name' => 'Danh sách chỉ tiêu của trường', 'link'=>route('school.target.index',['id'=>$schoolId])],
            ['name' => 'Thêm chỉ tiêu']);

        return view('admin.school.target.form', [
            'school' => $school,
            'create' => true,
            'systemTargets' => $systemTargets,
            'breadcrumbs'=> $this->breadcrumbsRoot,
        ]);
    }

    public function edit(Request $request, $schoolId, $targetId) {
        if ($request->isMethod('post')) {
            $messages = [
                'required' => 'Chỉ tiêu không được để trống',
                'numeric'=>'Chỉ tiêu phải là số',
                'min'=>'Chỉ tiêu phải lớn hơn 0',
                'max'=>'Chỉ tiêu phải bé hơn 100',
              ];
            $validator = Validator::make($request->all(), [
                'target_index' => 'numeric|min:1|max:100',
            ], $messages );

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $this->schoolService->updateTarget($request,$schoolId,$targetId);
            return redirect()->route('school.target.index', ['id' => $schoolId ])->with('success', 'Đã lưu chỉ tiêu');
        }

        $data['target'] = Target::with(['staffTargets','points'])->withCount("points")->find($targetId);
        $data['school'] = $this->schoolService->findById($schoolId);
        array_push($this->breadcrumbsRoot,
            ['name' => 'Danh sách chỉ tiêu của trường', 'link'=>route('school.target.index',['id'=>$schoolId])],
            ['name' => 'Thêm chỉ tiêu']);

        $data['systemTargets'] = Target::where([
            'school_id' => null,
            'staff_id' => null,
        ])->get();

        $data['breadcrumbs'] = $this->breadcrumbsRoot ;
        $view = 'admin.school.target.form';
        return view($view, $data);
    }

    //--Update result  :for open popup-- 
    public function result(Request $request, $schoolId, $targetId) {
        $data['target'] = Target::with(['staffTargets', 'staffTargets.staff'])->find($targetId);
        $data['school'] = $this->schoolService->findById($schoolId);
        if ($request->isMethod('post')) {
            $data['target']->update([
                'result' => ($request->result) ?? 0,
                'final_target' => $request->result * $data['target']->target_index / 100
            ]);
            return response()->json(['error' => 0]);
        }
        $data['systemTargets'] = Target::where([
            'school_id' => null,
            'staff_id' => null,
        ])->get();
        
        $view = 'admin.school.target.result';
        return view($view, $data);
    }
    public function resultPoint(Request $request, $schoolId, $pointId) {
        try {
            $point = TargetPoint::find($pointId);  
            $mainPoint = TargetPoint::find($point->main_point);   
            $totalIndexPoint = $mainPoint->getTotalIndexPoint();
            $target = Target::find($mainPoint->target_id);
            $totalIndexMainPoint = $target->getTotalIndexPoint();
            $point->result = ($request->result) ?? 0;
            $point->final_point = $request->result  * $point->index_point / $totalIndexPoint;
            $point->save();
            // check withclass
            $checkWithClass = false;
            if(!is_null($point->class_id) && !is_null($point->subject_id)) $checkWithClass = true;
            if($mainPoint) {
                $mainPoint->updateResultMainPoint($checkWithClass, $totalIndexMainPoint, $point);
            }
            if($target) {
                $target->updateResultTarget($mainPoint);
            }
            return response()->json(['error' => 0]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th]);
        }
     
    }
    public function resultMainPoint(Request $request, $schoolId, $pointId) {
        try {
            $mainPoint = TargetPoint::find($pointId);    
            $target = Target::find($mainPoint->target_id);
            $totalIndexPoint = $target->getTotalIndexPoint();
            $mainPoint->update([
                'result' => ($request->result) ?? 0,
                'final_point'=> $request->result  * $mainPoint->index_point / $totalIndexPoint
            ]);
            if($target) {
                $target->updateResultTarget($mainPoint);
            }
            return response()->json(['error' => 0]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th]);
        }
    }

    public function assignStaff(Request $request, $schoolId, $targetId) {
        $data['target'] = Target::where("id",$targetId)->first();
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['staffTargets'] = Target::where('main_target', $targetId)->get();
        $data["point_id_selected"] = $request->point_id ?? null;
        $data["urlAssign"] = url()->current();
        $data["staffPoints"] =TargetPoint::where('main_point', $data["point_id_selected"] ?? false)->groupBy("staff_id")->get();  
       
        if ($request->isMethod('post')) {
          
            $point = TargetPoint::findOrFail($request->point_id);
            $target = $point->target;
            $totalIndexMainPoint = $target->getTotalIndexPoint();
            if(is_null($request->index_point_class) && is_null($request->index_point)) {
                TargetPoint::where('main_point', $request->point_id)->delete();
                goto result;
            }
            if($request->optradio == 2) {
                $totalIndexPoint = collect($request->index_point_class)->sum();
                if($request->assign_classes) {
                    TargetPoint::where('main_point', $request->point_id)->whereNotIn("staff_id",$request->assign_classes)->delete(); // xóa những staff id không nằm trong bộ chọn
                    foreach($request->assign_classes as $class_id => $staffId){
                    TargetPoint::where('main_point', $request->point_id)->where("staff_id",$staffId)
                    ->where("class_id","<>",NULL)->where("subject_id","<>",NULL)
                    ->delete();
                    TargetPoint::where('main_point', $request->point_id)->where("staff_id",$staffId)
                    ->where("class_id",NULL)->where("subject_id",NULL)
                    ->delete(); 
                    $pointExist = TargetPoint::where('main_point', $request->point_id)->where("staff_id",$staffId)->where("class_id",$class_id)->where("subject_id",NULL)->first(); // check xem tồn tại subpoint
                    
                    if($pointExist) {
                            $pointExist->index_point = $request->index_point_class[$staffId];
                            $pointExist->final_point = $request->index_point_class[$staffId] * ($pointExist->result ?: 0) / $totalIndexPoint;
                            $pointExist->save();
                            continue;
                        }
                        $classPoint = $point->replicate();
                        $classPoint->main_point = $point->id;
                        $classPoint->staff_id = $staffId;
                        $classPoint->result = 0;
                        $classPoint->final_point = 0;
                        $classPoint->index_point = $request->index_point_class[$staffId];
                        $classPoint->class_id = $class_id;
                        $classPoint->push();
                    }
                }
            }

            if($request->optradio == 1) {
                $totalIndexPoint = collect($request->index_point)->sum();
                if($request->assigns) {
                    TargetPoint::where('main_point', $request->point_id)->whereNotIn("staff_id",$request->assigns)->delete(); // xóa những staff id không nằm trong bộ chọn
                    foreach($request->assigns as $staffId) {
                        if($request->has('assignWithClass')){
                            $classSubjectTaken = $this->staffService->findClassId($staffId);
                            $totalIndexPoint = $this->schoolService->getTotalIndexWithClass($request->index_point, $request->assigns);
                            TargetPoint::where('main_point', $request->point_id)->where("staff_id",$staffId)
                            ->where(function ($query) {
                                $query->where("class_id",NULL)
                                      ->orWhere("subject_id",NULL);
                            })->delete(); // xóa đi trường hợp class_id hoặc subject_id null xóa luôn
                            $pointExist = TargetPoint::where('main_point', $request->point_id)->where("staff_id",$staffId)->where("class_id","<>",NULL)->where("subject_id","<>",NULL)->first(); // check xem tồn tại subpoint
                            if($pointExist) {
                                $subPoints = $point->getSubPointsByStaff($staffId);
                                foreach ($subPoints as $subPoint) {
                                    $subPoint->index_point = $request->index_point[$staffId];
                                    $subPoint->final_point = $request->index_point[$staffId] * ($subPoint->result ?: 0) / $totalIndexPoint;
                                    $subPoint->save();
                                }
                                continue;
                            }
                            if(empty( $classSubjectTaken)) continue;
                            foreach($classSubjectTaken as $classSubject){
                                $staffPoint = $point->replicate();
                                $staffPoint->main_point = $point->id;
                                $staffPoint->staff_id = $staffId;
                                $staffPoint->result = 0;
                                $staffPoint->final_point = 0;
                                $staffPoint->index_point = $request->index_point[$staffId];
                                $staffPoint->class_id = $classSubject->class_id;
                                $staffPoint->subject_id = $classSubject->subject_id;
                                $staffPoint->push();
                            }
                        }else{
                            TargetPoint::where('main_point', $request->point_id)->where("staff_id",$staffId)
                            ->where(function ($query) {
                                $query->where("class_id","<>",NULL)
                                      ->orWhere("subject_id","<>",NULL);
                            })->delete();// xóa đi trường hợp class_id hoặc subject_id có giá trị xóa luôn
                            $pointExist = TargetPoint::where('main_point', $request->point_id)->where("staff_id",$staffId)->where("class_id",NULL)->where("subject_id",NULL)->first();
                            if($pointExist) {
                                $pointExist->index_point = $request->index_point[$staffId];
                                $pointExist->final_point = $request->index_point[$staffId] * ($pointExist->result ?: 0) / $totalIndexPoint;
                                $pointExist->save();
                                continue;
                            }
                            $staffPoint = $point->replicate();
                            $staffPoint->main_point = $point->id;
                            $staffPoint->result = 0;
                            $staffPoint->final_point = 0;
                            $staffPoint->staff_id = $staffId;
                            $staffPoint->index_point = $request->index_point[$staffId];
                            $staffPoint->push();
                        }
                    }
                }
            }
            result:
            $point->updateResultMainPoint($request->has('assignWithClass') ?: false,$totalIndexMainPoint, null);
            $target->updateResultTarget($point);
            return response()->json(['error' => 0, 'msg' => 'Cập nhật dữ liệu thành công']);
        }
        
        array_push($this->breadcrumbsRoot,
            ['name' => 'Danh sách chỉ tiêu của trường', 'link'=>route('school.target.index',['id'=>$schoolId])],
            ['name' => 'Giao tiêu chí']);

        $data['breadcrumbs'] = $this->breadcrumbsRoot;
        $view = 'admin.school.target.assign_staff';

        return view($view, $data);
    }
 

    public function delete($schoolId, $targetId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                
                if(Admin::user()->inRoles([ROLE_GIAO_VIEN])){
                    Target::where('main_target', $targetId)
                    ->delete();
                }else{
                    $target = Target::where('id',$targetId)->first();
                    $target->points()->delete();
                    $target->delete();
                }     
                
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
    public function deletePointById($choolId, $pointId) {
        try {
            $targetPoint = TargetPoint::find($pointId);
            $target = $targetPoint->target;
            $targetPoint->subPoints()->delete();
            $targetPoint->delete();
            $target->updateResultTarget();
            return Response::json(['status' => 200, "data"=>[]]);
        } catch (Exception $e) {
            return Response::json(['status' => 400, "data"=>[]]);
        }
    }
    public function getAssignPoint($choolId, $pointId) {
        $data["staffPoints"] =TargetPoint::where('main_point', $pointId)->groupBy("staff_id")->get();  
        return $data;
    }
    public function getSubPoints($choolId, $pointId) {
        try {
            $mainPoint = TargetPoint::find($pointId);
            $subPoints = $mainPoint->subPoints()->with("staff:id,fullname")->with("teacherClass:id,class_name")->with("teacherSubject:id,name")->get();
            return Response::json(['status' => 200, "data"=>$subPoints]);
        } catch (\Throwable $th) {
            return Response::json(['status' => 400, "data"=>[]]);
        }
    }
    
}