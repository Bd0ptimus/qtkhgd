<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Permission;
use App\Admin\Services\EbookService;
use App\Http\Controllers\Controller;
use App\Admin\Services\FileUploadService;
use App\Admin\Services\S3Service;
use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    protected $ebookService;
    protected $fileUploadService;
    protected $s3Service;
    
    public function __construct(
        EbookService $ebookService,
        FileUploadService $fileUploadService,
        S3Service $s3Service
    )
    {
        $this->ebookService = $ebookService;
        $this->fileUploadService = $fileUploadService;
        $this->s3Service = $s3Service;
    }
    
    public function index() {
        $params = request()->query();
        $data = $this->ebookService->index($params);

        return view('admin.ebook.index', $data);
    }
    
    public function create(Request $request)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->ebookService->validateRequest($request);
            // end
            $result = $this->ebookService->create($request);

            return redirect()->route('ebooks.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    
        $subjects = Subject::whereNull('school_id')->get();
        $ebookCategories = EbookCategory::all();
        return view('admin.ebook.form', [
            'title_description' => 'Thêm sách điện tử',
            'url_action' => route('ebooks.create'),
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects,
            'ebookCategories' => $ebookCategories,
            'availableEbookCategoryIds' => collect([]),
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->ebookService->validateRequest($request);
            // end

            $result = $this->ebookService->update($id,$request);
            return redirect()->route('ebooks.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    
        $subjects = Subject::whereNull('school_id')->get();
        $ebook = Ebook::find($id);
        $schoolType = $this->ebookService->getSchoolTypeByGrade($ebook->grade);
        $ebookCategories = EbookCategory::all();
        $availableEbookCategoryIds = $ebook->ebookCategories->map(function(EbookCategory $ebookCategory) {
            return $ebookCategory->id;
        });
        return view('admin.ebook.form', [
            'title_description' => "Chỉnh sửa sách điện tử",
            'url_action' => route('ebooks.edit', ['id' => $id]),
            'ebook' => $ebook,
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects,
            'schoolType' => $schoolType,
            'ebookCategories' => $ebookCategories,
            'availableEbookCategoryIds' => $availableEbookCategoryIds,
        ]);
    }

    public function delete(Request $request, $id) {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                Ebook::destroy($id);
            } catch (\Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
    
    public function download($id) {
        $ebook = Ebook::with('attachments')->findOrFail($id);
        return $this->s3Service->download($ebook->attachments[count($ebook->attachments) - 1]->path);
    }
    
    // Hàm lấy data sau khi chọn select khối học, cấp học
    public function changeSelectByParam(Request $request)
    {
        if (!request()->ajax()) return false;
        $param = $request->all();
        if (empty($param['value'])) return false;
        if (empty($param['key'])) return false;
        
        $data = $this->ebookService->getDataChangeSelectByParam($param);
        
        return json_encode($data, true);
    }
}