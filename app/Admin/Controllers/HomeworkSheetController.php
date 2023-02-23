<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Permission;
use App\Http\Controllers\Controller;
use App\Admin\Services\HomeworkSheetService;
use App\Models\HomeworkSheet;
use App\Models\Attachment;
use App\Models\SchoolClass;
use App\Admin\Services\S3Service;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeworkSheetController extends Controller
{
    protected $homeworkSheetService;
    protected $s3Service;
    
    public function __construct(HomeworkSheetService $homeworkSheetService, S3Service $s3Service)
    {
        $this->homeworkSheetService = $homeworkSheetService;
        $this->s3Service = $s3Service;
    }
    
    public function index() {
        $params = request()->query();
        $data = $this->homeworkSheetService->index($params);
        
        return view('admin.homework_sheet.index', $data);
    }
    
    public function create(Request $request)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->homeworkSheetService->validateRequest($request);
            // end

            $result = $this->homeworkSheetService->create($request);
            return redirect()->route('homework_sheet.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    
        $subjects = Subject::whereNull('school_id')->get();
        return view('admin.homework_sheet.form', [
            'title_description' => HomeworkSheet::TITLE_DESCRIPTION_ADD,
            'url_action' => route('homework_sheet.create'),
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
            $this->homeworkSheetService->validateRequest($request);
            // end

            $result = $this->homeworkSheetService->update($id,$request);
            return redirect()->route('homework_sheet.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    
        $subjects = Subject::whereNull('school_id')->get();
        $homeworkSheet = HomeworkSheet::find($id);
        $schoolType = $this->homeworkSheetService->getSchoolTypeByGrade($homeworkSheet->grade);
        return view('admin.homework_sheet.form', [
            'title_description' => HomeworkSheet::TITLE_DESCRIPTION_EDIT,
            'url_action' => route('homework_sheet.edit', ['id' => $id]),
            'homeworkSheet' => $homeworkSheet,
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
                $homeworkSheet = HomeworkSheet::find($id);
                $paths = $homeworkSheet->attachments->pluck('path')->toArray();
                $homeworkSheet->attachments()->delete();
                $homeworkSheet->delete();

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
        $fileNameEx = storage_path("/app/public/homework_sheet_" . date('Y-m-d H:i:s') . ".docx");
        return $this->homeworkSheetService->download($id, $fileNameEx);
    }
    
    
    // Hàm lấy data sau khi chọn select khối học, cấp học
    public function changeSelectByParam(Request $request)
    {
        if (!request()->ajax()) return false;
        $param = $request->all();
        if (empty($param['value'])) return false;
        if (empty($param['key'])) return false;
        
        $data = $this->homeworkSheetService->getDataChangeSelectByParam($param);
        
        return json_encode($data, true);
    }
}