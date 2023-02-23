<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Admin\Repositories\HomeworkSheetRepository;
use App\Admin\Repositories\SchoolClassRepository;
use App\Admin\Repositories\StaffRepository;
use App\Models\HomeworkSheet;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;

class HomeworkSheetService
{
    protected $homeworkSheetRepo;
    protected $schoolClassRepo;
    protected $staffRepo;
    protected $s3Service;
    protected $fileUploadService;

    public function __construct(HomeworkSheetRepository $homeworkSheetRepo, 
    SchoolClassRepository $schoolClassRepo,
    StaffRepository $staffRepo,
    S3Service $s3Service,
    FileUploadService $fileUploadService)
    {
        $this->homeworkSheetRepo = $homeworkSheetRepo;
        $this->schoolClassRepo = $schoolClassRepo;
        $this->staffRepo = $staffRepo;
        $this->s3Service = $s3Service;
        $this->fileUploadService = $fileUploadService;
    }
    
    public function validateRequest($request)
    {
        return $request->validate([
            'name' => 'required',
            'grade' => 'required',
            'subject_id' => 'required',
            'content' => 'required',
        ], [
            'name.required' => __('validation.required', ['attribute' => 'tên bài giảng']),
            'grade.required' => __('validation.required', ['attribute' => 'khối']),
            'subject_id.required' => __('validation.required', ['attribute' => 'môn học']),
            'content.required' => __('validation.required', ['attribute' => 'Nội dung bài giảng']),
        ]);
    }

    public function index($params = null) {
        $gradeSubjectData=[];
        if (Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_TO_TRUONG, ROLE_HIEU_TRUONG])){
            $gradeSubjectData = $this->staffRepo->takeSubjectAndGradeOfStaff(Admin::user()->username);
            $level = $params['level'] ?? $gradeSubjectData['school_level'];
        }else{
            $level = $params['level'] ?? array_keys(SCHOOL_TYPES);
        }

        $homeworkSheets = $this->homeworkSheetRepo->list($params, $gradeSubjectData, Admin::user()->isRole('cong-tac-vien'));
        $subjects = Subject::all();
        $subjectIdChoose = $params['subjectId'] ?? null;
        $keyGradeChoose = $params['grade'] ?? null;
        $search = $params['search'] ?? null;
        $selectedCollaborator = $params['selectedCollaborator'] ?? null;
        $collaborators = AdminUser::whereHas('roles', function($query) { 
            $query->whereIn("role_id", [ROLE_ADMIN_ID,ROLE_CONG_TAC_VIEN_ID]);
            })->get();
        if (!Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM,  ROLE_CONG_TAC_VIEN])) {
            $permission = false;
        }
        
        return [
            'homeworkSheets' => $homeworkSheets,
            'subjects' => $subjects,
            'keyGrade' => $gradeSubjectData['grades']??array_keys(GRADES),
            'keyGradeChoose' => $keyGradeChoose,
            'subjectId' => $gradeSubjectData['subjects']??[],
            'subjectIdChoose' => $subjectIdChoose,
            'level' => $level,
            'search' => $search,
            'selectedCollaborator' => $selectedCollaborator,
            "collaborators"=>$collaborators,
            "assemblages" => BOOK_ASSEMBLAGES,
            "selectedAssemblage"=>$params['selectedAssemblage']??null,
            'permission' => $permission ?? true
        ];
    }
    
    public function create($request) {
        DB::beginTransaction();
        try{
            $homeworkSheet = $this->homeworkSheetRepo->create($request->all());
            if($request->has("files")) {
                $fileUploads = $this->s3Service->uploadMulti($request, 'home_sheets/'.$homeworkSheet->id);
                foreach($fileUploads->getOriginalContent() as $fileUpload) {
                    $this->homeworkSheetRepo->saveAttactment($homeworkSheet, $fileUpload, true);
                }
            }
            DB::commit();
            $message = 'Thêm bài tập về nhà thành công';
            return [
                'message' => $message,
                'success' => true,
                'data' => $homeworkSheet
            ];
        } catch(\Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create homework sheet]',
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
            $model=$this->homeworkSheetRepo->findById($id);   
            unset($data['_token']);
            $this->homeworkSheetRepo->update($id, $data);   
            if($request->has("files")) {
                $model->attachments()->where('attachable_id', '=', $id)->delete();
                $fileUploads = $this->s3Service->uploadMulti($request, 'home_sheets/'.$model->id);
                foreach($fileUploads->getOriginalContent() as $fileUpload) {
                    $this->homeworkSheetRepo->saveAttactment($model, $fileUpload, true);
                }
            }
            DB::commit();
            $message = 'Cập nhât bài tập về nhà thành công';
            return [
                'message' => $message,
                'success' => true,                
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
        $homeworkSheet = $this->homeworkSheetRepo->findById($id);
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultFontName('Times New Roman');
        $section = $phpWord->addSection();

        Html::addHtml($section, $homeworkSheet->content);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        //ob_clean();
        $objWriter->save($fileNameEx);
        return response()->download($fileNameEx)->deleteFileAfterSend(true);
    }
    
    public function getDataChangeSelectByParam($param)
    {
        $key = $param['key'];
        $value = $param['value'];
        if ($key == HomeworkSheet::PARAM_LEVEL) {
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
    
        if ($key == HomeworkSheet::PARAM_GRADE) {
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