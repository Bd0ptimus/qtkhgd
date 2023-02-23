<?php

namespace App\Admin\Services;

use App\Admin\Repositories\SchoolClassRepository;
use App\Admin\Repositories\StaffRepository;
use App\Admin\Repositories\SubjectRepository;
use App\Models\ClassSubject;
use App\Models\StaffLinkingSchool;
use App\Models\Subject;
use Exception;
use Illuminate\Support\Facades\DB;

class SchoolClassService
{
    protected $schoolClassRepo;
    protected $subjectRepo;
    private $staffRepo;

    public function __construct(
        SchoolClassRepository $repo,
        SubjectRepository $subjectRepo,
        StaffRepository $staffRepo
    )
    {
        $this->schoolClassRepo = $repo;
        $this->subjectRepo = $subjectRepo;
        $this->staffRepo = $staffRepo;
    }

    public function allBySchool($schoolId) {
        return $this->schoolClassRepo->findBySchoolId($schoolId);
    }

    public function bulkUpdate($data) {
        DB::beginTransaction();
        try{
            foreach($data as $class) {
                $this->schoolClassRepo->update($class['id'], $class);
            }
            DB::commit();
            return ['success' => true, 'message' => 'Cập nhật thông tin thành công'];
        } catch (Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
            return ['success' => false, 'message' => 'Cập nhật thông tin thất bại'];
        }
        
    }

    public function findById($classId, $schoolId = null) {
        return $this->schoolClassRepo->findById($classId, ['*'], ['classSubjects', 'subjects', 'classSubjects.subject', 'classSubjects.staff', 'classSubjects.staffSubjects', 'classSubjects.staffSubjects.staff', 'classSubjects.staffSubjects.staff.linkingSchools' => function($query) use ($schoolId) {
            if($schoolId) $query->where('primary_school_id', $schoolId)->orWhere('additional_school_id', $schoolId);
        }, 'classSubjects.staffSubjects.staff.staffGrades' ]);
    }

    public function findWithAvailableTeacher($classId, $schoolId) {
        return $this->schoolClassRepo->findById($classId, ['*'], ['classSubjects', 'subjects', 'subjects.teachers' => function($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        }]);
    }

    public function autoGenerateSubject($class, $schoolId) {
        $subjectForClass = $this->subjectRepo->findBySchoolAndGrade($schoolId, $class->grade);
        
        ClassSubject::where('class_id', $class->id)->whereNotIn('subject_id', $subjectForClass->pluck('id')->toArray())->delete();
        
        foreach($subjectForClass as $subject) {
            
            $classSubject = [
                'subject_id' => $subject->id,
                'class_id' => $class->id,
            ];
            $check = ClassSubject::where($classSubject)->get();

            if(count($check) == 0) {
                if (
                    in_array($subject->id, Subject::PRIMARY_SCHOOL_SUBJECT_DEFAULT) &&
                    in_array($subject->id, isset($class->homeroomTeacher->staffSubjects) ? $class->homeroomTeacher->staffSubjects->pluck('subject_id')->toArray() : [])
                ) {
                    $classSubject['staff_id'] = $class->homeroom_teacher;
                }
    
                ClassSubject::create($classSubject);
            }
        }

        return $this->findWithAvailableTeacher($class->id, $schoolId);
    }

    public function checkClassLesson($classSubjects, $maximumLessons) {
        $totalLesson = 0;
        foreach($classSubjects as $classSubject){
            if (empty($classSubject['staff_id'])) {
                continue;
            }
            $totalLesson += $classSubject['lesson_per_week'];
        }
        if($totalLesson > $maximumLessons) return ['success' => false, 'message' => 'Tổng số tiết học không hợp lệ'];
        return ['success' => true, 'message' => 'Dữ liệu hợp lệ'];
    }
}