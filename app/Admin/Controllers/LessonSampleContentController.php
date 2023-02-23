<?php

namespace App\Admin\Controllers;
use App\Admin\Admin;
use App\Admin\Permission;
use App\Http\Controllers\Controller;
use App\Admin\Services\LessonSampleContentService;
use App\Admin\Services\S3Service;
use App\Models\Attachment;
use App\Models\LessonSampleContent;
use App\Models\LessonSample;
use App\Models\SchoolClass;
use App\Models\HomeworkSheet;
use App\Models\ExerciseQuestion;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonSampleContentController extends Controller
{

    protected $lessonService;
    

    public function __construct(LessonSampleContentService $service)
    {
        $this->lessonService = $service;
        
    }

    public function index($id)
    {       
        return view('admin.lesson_sample.lesson_content.index',['lessoncontents'=>$this->lessonService->allByLesson($id),'lessonsampleId'=>$id] );
    }

    public function create(Request $request, $id)
    {
        $lessonsample = LessonSample::find($id);
        if (is_null($lessonsample)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        
        if ($request->isMethod('post')) {
            $result = $this->lessonService->create($request->all(), $id);
            return redirect()->route('lesson_sample.lesson_content.index', ['id' => $id])
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }

        return view('admin.lesson_sample.lesson_content.form', [
            'url_action' => route('lesson_sample.lesson_content.create', ['id' => $id]),
           
        ]);
    }

    public function edit(Request $request,  $id, $lessonsampleId)
    {
        $lessoncontent = LessonSampleContent::where(['id' => $lessonsampleId, 'lesson_sample_id' => $id])->with('lessonsample')->first();
        
        if ($request->isMethod('post')) {    
            if($lessoncontent) {
                $result = $this->lessonService->update($lessonsampleId, $request->all());
                return redirect()->route('lesson_sample.lesson_content.index', ['id' => $id])
                    ->with($result['success'] ? 'success' : 'error', $result['message']);
            }
        }

        return view('admin.lesson_sample.lesson_content.form', [
            'url_action' => route('lesson_sample.lesson_content.edit', ['id' => $id, 'lesson_sample_id' => $lessonsampleId]),
            'lessoncontent' => $lessoncontent,
           
        ]);
    }

    public function delete(Request $request, $id, $lessonsampleId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                LessonSampleContent::destroy($lessonsampleId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}
