<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Repositories\EbookCategoryRepository;
use App\Models\EbookCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EbookCategoryService
{
    private EbookCategoryRepository $ebookCategoryRepository;

    public function __construct(EbookCategoryRepository $ebookCategoryRepository)
    {
        $this->ebookCategoryRepository = $ebookCategoryRepository;
    }
    
    public function getAll(array $params = [])
    {
        return $this->ebookCategoryRepository->getAll($params);
    }

    public function create(Request $request)
    {
        $ebookCategory = EbookCategory::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name'), '-'),
        ]);
        $ebookCategory->save();
        $message = 'Thêm ebook thành công';

        return [
            'message' => $message,
            'success' => true,
            'data' => $ebookCategory,
        ];
    }

    public function update($id, Request $request)
    {
        $ebookCategory = EbookCategory::findOrFail($id);
        $ebookCategory->name = $request->input('name');
        $ebookCategory->slug = Str::slug($request->input('name'), '-');
        $ebookCategory->save();

        return [
            'message' => 'Cập nhật loại sách thành công',
            'success' => true,
        ];
    }

    public function destroy($id)
    {
        $ebookCategory = EbookCategory::findOrFail($id);

        if ($ebookCategory->ebooks()->count() > 0) {
            throw new Exception('Loại sách này đang được tham chiếu đến sách, hãy xóa các sách liên quan trước!');
        }
        $ebookCategory->delete();

        return [
            'message' => 'Xóa loại sách thành công',
            'success' => true,
        ];
    }

    public function validateRequest($request)
    {
        return $request->validate([
            'name' => 'required',
        ], [
            'name.required' => __('validation.required', ['attribute' => 'Loại sách']),
        ]);
    }
}