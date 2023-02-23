<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Admin\Permission;
use App\Admin\Services\EbookCategoryService;
use App\Http\Controllers\Controller;
use App\Models\EbookCategory;
use Exception;
use Illuminate\Http\Request;

class EbookCategoryController extends Controller
{
    private EbookCategoryService $ebookCategoryService;

    public function __construct(EbookCategoryService $ebookCategoryService)
    {
        $this->ebookCategoryService = $ebookCategoryService;
    }

    public function index(Request $request)
    {
        $params = request()->query();
        $ebookCategories = $this->ebookCategoryService->getAll($params);
        $selectedCollaborator = $request->input('selectedCollaborator', null);
        $collaborators = AdminUser::whereHas('roles', function($query) { 
              $query->whereIn("role_id", [ROLE_ADMIN_ID,ROLE_CONG_TAC_VIEN_ID]);
              })->get();
        return view('admin.ebook_category.index', [
            'ebookCategories' => $ebookCategories,
            'search' => $request->input('search', ''),
            'selectedCollaborator' => $selectedCollaborator,
            "collaborators"=>$collaborators,
            'permission' => Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN]),
        ]);
    }

    public function create(Request $request)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM,ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->ebookCategoryService->validateRequest($request);
            // end
            $result = $this->ebookCategoryService->create($request);

            return redirect()->route('ebook-categories.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }
    

        return view('admin.ebook_category.form', [
            'title_description' => 'Thêm loại sách',
            'url_action' => route('ebook-categories.create'),
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if ($request->isMethod('post')) {
            // validate request
            $this->ebookCategoryService->validateRequest($request);
            // end

            $result = $this->ebookCategoryService->update($id,$request);
            return redirect()->route('ebook-categories.index')
                ->with($result['success'] ? 'success' : 'error', $result['message']);
        }

        $ebookCategory = EbookCategory::findOrFail($id);

        return view('admin.ebook_category.form', [
            'title_description' => "Chỉnh sửa loại sách",
            'url_action' => route('ebook-categories.edit', ['id' => $id]),
            'ebook' => $ebookCategory,
        ]);
    }

    public function destroy(Request $request, $id) {
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CONG_TAC_VIEN])) {
            return Permission::error();
        }
        
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                $this->ebookCategoryService->destroy($id);
            } catch (Exception $e) {
                return response()->json(['error' => 1, 'msg' => 'Đã có lỗi'], 400);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}
