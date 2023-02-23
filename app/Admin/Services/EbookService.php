<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Admin\Repositories\EbookRepository;
use App\Admin\Repositories\ExerciseQuestionRepository;
use App\Admin\Repositories\SchoolClassRepository;
use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\ExerciseQuestion;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;

class EbookService
{
    protected $ebookRepo;
    protected $schoolClassRepo;
    protected $fileUploadService;
    protected $s3Service;

    public function __construct(
        EbookRepository $ebookRepo, SchoolClassRepository $schoolClassRepo,
        FileUploadService $fileUploadService, S3Service $s3Service)
    {
        $this->ebookRepo = $ebookRepo;
        $this->schoolClassRepo = $schoolClassRepo;
        $this->fileUploadService = $fileUploadService;
        $this->s3Service = $s3Service;
    }
    
    public function validateRequest($request)
    {
        return $request->validate([
            'name' => 'required',
            'grade' => 'required',
            'subject_id' => 'required',
            'description' => 'required',
        ], [
            'name.required' => __('validation.required', ['attribute' => 'Tên sách']),
            'grade.required' => __('validation.required', ['attribute' => 'khối']),
            'subject_id.required' => __('validation.required', ['attribute' => 'môn học']),
            'description.required' => __('validation.required', ['attribute' => 'Mô tả sách']),
        ]);
    }

    public function index($params = null) {
        $checkIsCollaborator =  Admin::user()->isRole('cong-tac-vien');
        $ebooks = $this->ebookRepo->list($params,$checkIsCollaborator);
        $subjects = Subject::all();
        $level = $params['level'] ?? null;
        $subjectId = $params['subjectId'] ?? null;
        $keyGrade = $params['grade'] ?? null;
        $search = $params['search'] ?? null;
        $assemblage = $params['assemblage'] ?? null;
        $selectedEbookCategory = $params['ebookCategory'] ?? null;
        $selectedCollaborator = $params['selectedCollaborator'] ?? null;
        $ebookCategories = EbookCategory::all();
        $collaborators = AdminUser::whereHas('roles', function($query) { 
            $query->whereIn("role_id", [ROLE_ADMIN_ID,ROLE_CONG_TAC_VIEN_ID]);
            })->get();
        return [
            'ebooks' => $ebooks,
            'subjects' => $subjects,
            'keyGrade' => $keyGrade,
            'subjectId' => $subjectId,
            'level' => $level,
            'search' => $search,
            'assemblage' => $assemblage,
            'ebookCategories' => $ebookCategories,
            'selectedEbookCategory' => $selectedEbookCategory,
            'selectedCollaborator' => $selectedCollaborator,
            "collaborators"=>$collaborators,
            'permission' => Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN]),
        ];
    }
    
    public function create($request) {
        DB::beginTransaction();
        try{
            
            $newEbook = Ebook::create($request->all());
            $newEbook->ebookCategories()->attach($request->input('ebook_categories', []));
            
            if($request->file) {
                $fileUpload = $this->s3Service->upload($request, 'ebooks', ['pdf']);
                $this->ebookRepo->saveAttactment($newEbook, $fileUpload->getOriginalContent(), true);
            }

            DB::commit();
            $message = 'Thêm ebook thành công';
            return [
                'message' => $message,
                'success' => true,
                'data' => $newEbook
            ];
        } catch(\Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create exercise question]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
        
    }
    
    public function update($id, $request) {
        DB::beginTransaction();
        try{
            $data = $request->all();
            unset($data['_token']);
            $model = $this->ebookRepo->findById($id);
            $this->ebookRepo->update($id, $data);
            $model->ebookCategories()->sync($request->input('ebook_categories', []));
            
            if($request->file) {
                $fileUpload = $this->s3Service->upload($request, 'ebooks', ['pdf']);
                $this->ebookRepo->saveAttactment($model, $fileUpload->getOriginalContent(), true);
            }
            
            DB::commit();
            return [
                'message' => 'Cập nhật bài kiểm tra thành công',
                'success' => true
            ];
        } catch(\Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[update group plan]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
    }

    public function download($id, $fileNameEx) {
        Settings::setCompatibility(false);
        $exerciseQuestion = $this->ebookRepo->findById($id);
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultFontName('Times New Roman');
        $section = $phpWord->addSection();

        Html::addHtml($section, $exerciseQuestion->content);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        //ob_clean();
        $objWriter->save($fileNameEx);
        return response()->download($fileNameEx)->deleteFileAfterSend(true);
    }
    
    public function getDataChangeSelectByParam($param)
    {
        $key = $param['key'];
        $value = $param['value'];
        if ($key == ExerciseQuestion::PARAM_LEVEL) {
           $grades = $this->schoolClassRepo->getGradeBySchoolType($value);
            foreach ($grades as $grade) {
                if (!empty(SchoolClass::GRADES[$grade])) {
                    $gradeFormat[] = [
                        'id' => $grade,
                        'name' => SchoolClass::GRADES[$grade]
                    ];
                }
            }
           if (!empty($grades[0])) {
               $subjects = $this->schoolClassRepo->getSubjectByGrade($grades[0]);
           }
        }
        
        if ($key == ExerciseQuestion::PARAM_GRADE) {
            $subjects = $this->schoolClassRepo->getSubjectByGrade($value);
            $schoolType = $this->schoolClassRepo->getSchoolTypeByGrade($value);
        }
    
        foreach (SCHOOL_TYPES as $key => $val) {
            $schoolTypes[] = [
                'id' => $key,
                'name' => $val
            ];
        }
        
        return [
            'grades' => $gradeFormat ?? null,
            'subjects' => $subjects ?? null,
            'schoolType' => $schoolType ?? null,
            'schoolTypes' => $schoolTypes ?? null
        ];
    }
    
    public function getSchoolTypeByGrade($grade)
    {
        if (!$grade) return null;
        
        $schoolType = $this->schoolClassRepo->getSchoolTypeByGrade($grade);
        
        return $schoolType['id'] ?? null;
    }
}