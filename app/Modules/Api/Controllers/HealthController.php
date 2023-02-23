<?php
#app/Modules/Api/Controllers/HealthController.php
namespace App\Modules\Api\Controllers;

use App\Models\HealthCategory;
use App\Models\HealthPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HealthController extends ApiController
{
    /**
     * Get All category health
     * @return JsonResponse
     */
    public function getCategories()
    {
        return $this->respSuccess(HealthCategory::all());
    }

    /**
     * Get All post health
     * @param Request $request
     * @return JsonResponse
     */
    public function getPosts(Request $request)
    {
        $keyword = $request->query('keyword');
        $category = $request->query('category');
        $postsQuery = HealthPost::with('categories', 'author');
        if (!empty($keyword)) {
            $postsQuery = $postsQuery->where('title', 'LIKE', '%' . Str::slug($keyword) . '%');
        }
        if (!empty($category)) {
            $postsQuery = $postsQuery->whereHas('categories', function ($query) use ($category) {
                $query->where('health_category.id', $category);
            });
        }
        $posts = $postsQuery->orderBy('created_at', 'desc')->paginate(6);
        return $this->respSuccess($posts);
    }

    /**
     * Get post detail
     *
     * @param HealthPost $post
     * @return JsonResponse
     */
    public function getPostDetail(HealthPost $post)
    {
        $post = $post->load('categories', 'author');
        return $this->respSuccess($post);
    }
}