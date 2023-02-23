<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Admin\Repositories\ExerciseQuestionRepository;
use App\Admin\Repositories\HomeworkSheetRepository;
use App\Admin\Repositories\LessonSampleRepository;
use App\Admin\Repositories\SchoolClassRepository;
use App\Admin\Repositories\StaffRepository;
use App\Admin\Repositories\SubjectRepository;
use App\Models\ExerciseQuestion;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;

class LessonSampleService
{
    protected $lessonSampleRepo;
    protected $schoolClassRepo;
    protected $fileUploadService;
    protected $homeworkSheetRepo;
    protected $exerciseQuestionRepo;
    protected $staffRepo;
    protected $subjectRepo;
    protected $s3Service;

    public function __construct(
        LessonSampleRepository $lessonSampleRepo, 
        SchoolClassRepository $schoolClassRepo,
        FileUploadService $fileUploadService,
        HomeworkSheetRepository $homeworkSheetRepo,
        ExerciseQuestionRepository $exerciseQuestionRepo,
        StaffRepository $staffRepo,
        SubjectRepository $subjectRepo,
        S3Service $s3Service
    )
    {
        $this->lessonSampleRepo = $lessonSampleRepo;
        $this->schoolClassRepo = $schoolClassRepo;
        $this->fileUploadService = $fileUploadService;
        $this->homeworkSheetRepo = $homeworkSheetRepo;
        $this->exerciseQuestionRepo = $exerciseQuestionRepo;
        $this->staffRepo = $staffRepo;
        $this->subjectRepo = $subjectRepo;
        $this->s3Service = $s3Service;

    }
    
    public function validateRequest($request)
    {
        return $request->validate([
            'file' => 'max:100000',
            'title' => 'required',
            'grade' => 'required',
            'subject_id' => 'required',
            'content' => 'required',
        ], [
            'file.max' => __('validation.max', ['attribute' => 'bài giảng']),
            'title.required' => __('validation.required', ['attribute' => 'tên bài giảng']),
            'grade.required' => __('validation.required', ['attribute' => 'khối']),
            'subject_id.required' => __('validation.required', ['attribute' => 'môn học']),
            'content.required' => __('validation.required', ['attribute' => 'Nội dung bài giảng']),
        ]);
    }

    public function index($params = null) {
        $gradeSubjectDataForTeachers=[];
        $gradeSubjectDataForAdmin=[];
        if (Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_TO_TRUONG, ROLE_HIEU_TRUONG])){
            $gradeSubjectDataForTeachers = $this->staffRepo->takeSubjectAndGradeOfStaff(Admin::user()->username);
            $level = $params['level'] ?? $gradeSubjectDataForTeachers['school_level'];
        }else{
            $level = $params['level'] ?? array_keys(SCHOOL_TYPES);
            $gradeSubjectDataForAdmin['subjects']=[];
            if(isset($params['grade'])){
                $gradeSubjectDataForAdmin['subjects']= $this->subjectRepo->findByGrades($params['grade'])->toArray();
            }
        }
        $lessonSamples = $this->lessonSampleRepo->list($params, $gradeSubjectDataForTeachers,Admin::user()->isRole('cong-tac-vien') );
        $subjects = Subject::all();
        $subjectIdChoose = $params['subjectId'] ?? null;
        $keyGradeChoose = $params['grade']??null;
        $search = $params['search'] ?? null;

        $filterPowerpoint = $params['filter_powerpoint'] ?? null;
        $filterDigitalDevice = $params['filter_digital_device']??null;
        $filterHomesheet = $params['filter_homesheet'] ?? null;
        $filterExercise = $params['filter_exercise'] ?? null;
        $filterDiagramSimulator = $params['filter_diagram_simulator'] ?? null;
        $filterGame = $params['filter_game'] ?? null;
        $selectedCollaborator = $params['selectedCollaborator'] ?? null;

        $collaborators = AdminUser::whereHas('roles', function($query) { 
            $query->whereIn("role_id", [ROLE_ADMIN_ID,ROLE_CONG_TAC_VIEN_ID]);
            })->get();
        return [
            'lessonSamples' => $lessonSamples,
            'subjects' => $subjects,
            'keyGrade' => $gradeSubjectDataForTeachers['grades']??array_keys(GRADES),
            'keyGradeChoose'=>$keyGradeChoose,
            'subjectId' => $gradeSubjectDataForTeachers['subjects']??$gradeSubjectDataForAdmin['subjects'],
            'subjectIdChoose'=>$subjectIdChoose,
            'level' => $level,
            'filterPowerpoint'=>$filterPowerpoint,
            'filterDigitalDevice' => $filterDigitalDevice,
            'filterHomesheet'=>$filterHomesheet,
            'filterExercise' => $filterExercise,
            'filterDiagramSimulator'=>$filterDiagramSimulator,
            'filterGame' => $filterGame,
            'search' => $search,
            'selectedCollaborator' => $selectedCollaborator,
            "collaborators"=>$collaborators,
            "assemblages" => BOOK_ASSEMBLAGES,
            "selectedAssemblage"=>$params['selectedAssemblage']??null,
            'permission' => Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_CONG_TAC_VIEN])
        ];
    }
    
    public function create($request) {
        DB::beginTransaction();
        try{
            $newLesson = $this->lessonSampleRepo->create($request->all());
            if($request->has("files")) {
                $fileUploads = $this->s3Service->uploadMulti($request, 'lesson_samples/'.$newLesson->id);
                foreach($fileUploads->getOriginalContent() as $fileUpload) {
                    $this->lessonSampleRepo->saveAttactment($newLesson, $fileUpload, true);
                }
            }
            DB::commit();
            $message = 'Thêm bài giảng mẫu thành công';
            return [
                'message' => $message,
                'success' => true,
                'data' => $newLesson
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
            $model = $this->lessonSampleRepo->findById($id);
            unset($data['_token']);
            $this->lessonSampleRepo->update($id, $data);
            if($request->has("video")){
                $videoUpload = $this->s3Service->upload($request, 'lesson_samples/'.$model->id)->getOriginalContent();
                if($videoUpload['success']){
                    $this->lessonSampleRepo->updateVideo($id,$videoUpload);
                }
            }   

            if($request->has("files")) {
                //$model->attachments()->where('attachable_id', '=', $id)->delete();
                $fileUploads = $this->s3Service->uploadMulti($request, 'lesson_samples/'.$model->id);
                foreach($fileUploads->getOriginalContent() as $fileUpload) {
                    $this->lessonSampleRepo->saveAttactment($model, $fileUpload);//, true
                }
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
        $exerciseQuestion = $this->lessonSampleRepo->findById($id);
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultFontName('Times New Roman');
        $section = $phpWord->addSection();
        $host = 'http:\/\/' . request()->getHttpHost() . '\/';
        $pattern = '/'.$host . '/m';
        $replaceHtml = preg_replace($pattern, '', $exerciseQuestion->content);
        Html::addHtml($section, $replaceHtml);
        
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
    
    // func processing up lesson
    public function upLesson()
    {
        $lessonSamples = $this->lessonSampleRepo->list();
        $subjects = Subject::all();
        $level = $params['level'] ?? null;
        $subjectId = $params['subjectId'] ?? null;
        $keyGrade = $params['grade'] ?? null;
        $search = $params['search'] ?? null;
        return [
            'lessonSamples' => $lessonSamples,
            'subjects' => $subjects,
            'keyGrade' => $keyGrade,
            'subjectId' => $subjectId,
            'level' => $level,
            'search' => $search,
            'permission' => Admin::user()->inRoles([ROLE_ADMIN])
        ];
    }
    
    // get data homework sheet
    public function getDataHomeworkSheet($lessonId)
    {
        // get subject_id and grade
        $arrId = $this->lessonSampleRepo->all(['grade', 'subject_id']);
        if (empty($arrId)) return [];
        
        // get list homework sheet buy grade and subject_id
        return $this->homeworkSheetRepo->getDataByGradeAndSubject($arrId['grade'] ?? null, $arrId['subject_id'] ?? null);
    }
    
    // get data homework sheet
    public function getDataExerciseQuestion($lessonId)
    {
        // get subject_id and grade
        $arrId = $this->lessonSampleRepo->all(['grade', 'subject_id']);
        if (empty($arrId)) return [];
        
        // get list homework sheet buy grade and subject_id
        return $this->exerciseQuestionRepo->getDataByGradeAndSubject($arrId['grade'] ?? null, $arrId['subject_id'] ?? null);
    }
}