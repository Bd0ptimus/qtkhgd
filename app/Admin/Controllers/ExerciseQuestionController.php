<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Permission;
use App\Http\Controllers\Controller;
use App\Admin\Services\ExerciseQuestionService;
use App\Models\Attachment;
use App\Models\ExerciseQuestion;
use App\Admin\Services\S3Service;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExerciseQuestionController extends Controller
{
    protected $exerciseQuestionService;
    protected $s3Service;
    
    public function __construct(ExerciseQuestionService $exerciseQuestionService,S3Service $s3Service)
    {
        $this->exerciseQuestionService = $exerciseQuestionService;
        $this->s3Service = $s3Service;
    }
    
    public function index() {
        $params = request()->query();
        $data = $this->exerciseQuestionService->index($params);
        
        return view('admin.exercise_question.index', $data);
    }
    
    public function create(Request $request)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->exerciseQuestionService->validateRequest($request);
            // end

            $result = $this->exerciseQuestionService->create($request);
            return redirect()->route('exercise_question.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    
        $subjects = Subject::whereNull('school_id')->get();
        return view('admin.exercise_question.form', [
            'title_description' => ExerciseQuestion::TITLE_DESCRIPTION_ADD,
            'url_action' => route('exercise_question.create'),
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->exerciseQuestionService->validateRequest($request);
            // end

            $result = $this->exerciseQuestionService->update($id,$request);
            return redirect()->route('exercise_question.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    
        $subjects = Subject::whereNull('school_id')->get();
        $exerciseQuestion = ExerciseQuestion::find($id);
        $schoolType = $this->exerciseQuestionService->getSchoolTypeByGrade($exerciseQuestion->grade);
        return view('admin.exercise_question.form', [
            'title_description' => ExerciseQuestion::TITLE_DESCRIPTION_EDIT,
            'url_action' => route('exercise_question.edit', ['id' => $id]),
            'exerciseQuestion' => $exerciseQuestion,
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects,
            'schoolType' => $schoolType
        ]);
    }

    public function delete(Request $request, $id) {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                $exerciseQuestion = ExerciseQuestion::find($id);
                $paths = $exerciseQuestion->attachments->pluck('path')->toArray();
                $exerciseQuestion->attachments()->delete();
                $exerciseQuestion->delete();

                if ($paths) {
                    $this->s3Service->deleteFile($paths);
                }
            } catch (\Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function deleteFile(Request $request, $attachmentId){
        if (!request()->ajax()) {   
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {        
            try {  
                $attachments = Attachment::find($attachmentId);                      
                $this->s3Service->deleteFile($attachments->path);
                Attachment::destroy($attachmentId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }

    }
    
    public function download($id) {
        $fileNameEx = storage_path("/app/public/exercise_question_" . date('Y-m-d H:i:s') . ".docx");
        return $this->exerciseQuestionService->download($id, $fileNameEx);
    }
    
    
    // Hàm lấy data sau khi chọn select khối học, cấp học
    public function changeSelectByParam(Request $request)
    {
        if (!request()->ajax()) return false;
        $param = $request->all();
        if (empty($param['value'])) return false;
        if (empty($param['key'])) return false;
        
        $data = $this->exerciseQuestionService->getDataChangeSelectByParam($param);
        
        return json_encode($data, true);
    }
}