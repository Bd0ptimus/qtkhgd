<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Permission;
use App\Http\Controllers\Controller;
use App\Admin\Services\LessonSampleService;
use App\Admin\Services\S3Service;
use App\Models\Attachment;
use App\Models\Lesson_sample;
use App\Models\LessonSample;
use App\Models\SchoolClass;
use App\Models\HomeworkSheet;
use App\Models\ExerciseQuestion;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonSampleController extends Controller
{
    protected $lessonSampleService;
    protected $s3Service;
    
    public function __construct(
        LessonSampleService $lessonSampleService,
        S3Service $s3Service
    )
    {
        $this->lessonSampleService = $lessonSampleService;
        $this->s3Service = $s3Service;
    }
    
    public function index() {
        $params = request()->query();
        $data = $this->lessonSampleService->index($params);
        //dd($data);
        return view('admin.lesson_sample.index', $data);
    }
    
    public function create(Request $request)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->lessonSampleService->validateRequest($request);
            // end

            $result = $this->lessonSampleService->create($request);
            return redirect()->route('lesson_sample.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    
        $subjects = Subject::whereNull('school_id')->get();      
        $homesheets = HomeworkSheet::all();
        $exercises = ExerciseQuestion::all();

        return view('admin.lesson_sample.form', [
            'title_description' => 'Thêm bài giảng mẫu',
            'url_action' => route('lesson_sample.create'),
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects,
            'homesheets' => $homesheets,
            'exercises' => $exercises,
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->lessonSampleService->validateRequest($request);
            // end

            $result = $this->lessonSampleService->update($id,$request);
            return redirect()->route('lesson_sample.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
           }
    
        $subjects = Subject::whereNull('school_id')->get();
        $lessonSample = LessonSample::with('attachments')->find($id);
        $schoolType = $this->lessonSampleService->getSchoolTypeByGrade($lessonSample->grade);
        $homesheets = HomeworkSheet::all();
        $exercises = ExerciseQuestion::all();
        return view('admin.lesson_sample.form', [
            'title_description' => "Chỉnh sửa Bài giảng mẫu",
            'url_action' => route('lesson_sample.edit', ['id' => $id]),
            'lessonSample' => $lessonSample,
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects,
            'schoolType' => $schoolType,
            'homesheets' => $homesheets,
            'exercises' => $exercises,
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
                LessonSample::destroy($id);
            } catch (\Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function download($id) {
        $fileNameEx = storage_path("/app/public/lesson_sample_" . date('Y-m-d H:i:s') . ".docx");
        return $this->lessonSampleService->download($id, $fileNameEx);
    }

        
    public function downloadAttachFile($attachmentId) {
            $attachment = Attachment::find($attachmentId);
            return $this->s3Service->download($attachment->path);
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
    
    // Hàm lấy data sau khi chọn select khối học, cấp học
    public function changeSelectByParam(Request $request)
    {
        if (!request()->ajax()) return false;
        $param = $request->all();
        if (empty($param['value'])) return false;
        if (empty($param['key'])) return false;
        
        $data = $this->lessonSampleService->getDataChangeSelectByParam($param);
        
        return json_encode($data, true);
    }
    
    // Ham lấy data lên bài giảng
    public function upLesson()
    {
        $data = $this->lessonSampleService->upLesson();
    
        return view('admin.lesson_sample.up_lesson', $data);
    }
    
    // get data homework sheet
    public function getDataHomeworkSheet(Request $request)
    {
        if (!request()->ajax()) return [];
    
        $param = $request->all();
        if (empty($param['id'])) return [];
    
        $data = $this->lessonSampleService->getDataHomeworkSheet($param['id']);
    
        return response()->json($data);
    }
    
    // get data exercise question
    public function getDataExerciseQuestion(Request $request)
    {
        if (!request()->ajax()) return [];
        
        $param = $request->all();
        if (empty($param['id'])) return [];
        
        $data = $this->lessonSampleService->getDataExerciseQuestion($param['id']);
        
        return response()->json($data);
    }
       
    // them video
    public function addFile(Request $request)
    {
        if (!Admin::user()) {
            return Permission::error();
        }
        $validator = $request->validate([
            'file_upload' => 'required|mimes:mp4,docx,pdf|max:100000',
        ], [
            'file_upload.required' => trans('validation.required'),
            'file_upload.mimes' => trans('validation.mimes'),
        ]);
    
        if($request->file()) {
            $request->validate([
                'files.*' => 'file|max:25600', // 25MB Max
            ]);
        }
    }
    
    // get data trò chơi
    public function getDataGame()
    {
        return view('admin.lesson_sample.game');
    }
    
    // get data mô phỏng
    public function getDataSimulation() {     
        return view('admin.lesson_sample.simulation');
    }
}