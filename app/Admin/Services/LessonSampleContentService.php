<?php

namespace App\Admin\Services;

use App\Admin\Repositories\LessonSampleContentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LessonSampleContent;
use Exception;

class LessonSampleContentService
{
    protected $lessoncontentRepo;

    public function __construct(LessonSampleContentRepository $repo)
    {
        $this->lessoncontentRepo = $repo;
    }   

    public function allByLesson($lessonsampleId) {
        return $this->lessoncontentRepo->allWithLessonSample($lessonsampleId);
    }
    
    public function create($params, $lessonsampleId = null) {
        DB::beginTransaction();
        try{            
            if($lessonsampleId) $params['lesson_sample_id'] = $lessonsampleId;
            $lessoncontent = $this->lessoncontentRepo->create($params);            
            DB::commit();
            return ['success' => true, 'message' => 'Thêm nội dung thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
        
    }

    public function update($id, $params) {      
      
        DB::beginTransaction();
        try{     
            $model = $this->lessoncontentRepo->findById($id); 
            unset($params['_token']);
            LessonSampleContent::where('id', $id)->update($params);  
            DB::commit();  
            return ['success' => true, 'message' => 'Sửa nội dung thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
       
    }

}