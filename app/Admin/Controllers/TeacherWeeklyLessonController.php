<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Admin\Exports\TeacherWeeklyLessonExport;
use App\Admin\Helpers\ListHelper;
use App\Admin\Repositories\SchoolClassRepository;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherLesson;
use App\Models\TeacherWeeklyLesson;
use App\Models\TeacherWeeklyLessonProgress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TeacherWeeklyLessonController extends Controller
{
    private SchoolClassRepository $schoolClassRepository;

    public function __construct(SchoolClassRepository $schoolClassRepository)
    {
        $this->schoolClassRepository = $schoolClassRepository;
    }

    public function index(Request $request)
    {
        list($month, $year) = $request->input('month_year') ? explode('-', $request->input('month_year')) : [null,null];

        $teacherWeeklyLessons = TeacherWeeklyLesson::with(['teacherLesson', 'teacherWeeklyLessonProgresses', 'teacherWeeklyLessonProgresses.schoolClass'])
            ->select('teacher_weekly_lesson.*', 'teacher_lesson.month_year')
            ->join('teacher_lesson', 'teacher_lesson.id', '=', 'teacher_weekly_lesson.teacher_lesson_id')
            ->when(Admin::user()->inRoles([ROLE_GIAO_VIEN]), function($q) {
                $staffId = Admin::user()->staffDetail->id;
                $q->where('teacher_id', $staffId);
            })
            ->when($year, function($q) use ($year) {
                $q->whereYear('teacher_lesson.month_year', $year);
            })
            ->when($month, function($q) use ($month) {
                $q->whereMonth('teacher_lesson.month_year', $month);
            })
            ->orderBy('teacher_weekly_lesson.created_at', 'DESC')
            ->get();

        return view('admin.weekly_lesson.index', [
            'createRouting' => route('teacher_weekly_lesson.create'),
            'teacherWeeklyLessons' => $teacherWeeklyLessons,
            'monthYears' => ListHelper::listMonth(),
            'staffId' => Admin::user()->staffDetail->id,
            'schoolId' => Admin::user()->staffDetail->school_id,
        ]);
    }

    public function create(Request $request)
    {
        $subjects = Subject::select('id', 'name', 'grade', 'subject_id')
            ->join('grade_subject', 'subject.id', '=', 'grade_subject.subject_id')
            ->get();

          return view('admin.weekly_lesson.form', [
            'method' => 'post',
            'title' => 'Nhập bài giảng theo tuần của giáo viên',
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects->groupBy('grade')->toArray(),
            'routing' => route('teacher_weekly_lesson.store'),
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $teacherWeeklyLesson = TeacherWeeklyLesson::create([
                'teacher_id' => Admin::user()->staffDetail->id,
                'grade' => $request->input('grade'),
                'subject_id' => $request->input('subject'),
                'teacher_lesson_id' => $request->input('lesson'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);

            $classes = $this->schoolClassRepository->getClassByGradeAndSubject($teacherWeeklyLesson->grade, $teacherWeeklyLesson->subject_id);

            $teacherWeeklyLessonProgress = [];
            foreach ($classes as $class) {
                $teacherWeeklyLessonProgress[] = [
                    'teacher_weekly_lesson_id' => $teacherWeeklyLesson->id,
                    'class_id' => $class->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('teacher_weekly_lesson_progress')->insert($teacherWeeklyLessonProgress);            
            DB::commit();
            return redirect()->route('teacher_weekly_lesson.index')->with('success', 'Thêm thành công!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('teacher_weekly_lesson.index')->with('error', 'Thêm thất bại!');
        }
    }

    public function edit(Request $request, $weeklyLessonId)
    {
        $weeklyLesson = TeacherWeeklyLesson::when(Admin::user()->inRoles([ROLE_GIAO_VIEN]), function($q) {
                $q->where('teacher_id', Admin::user()->staffDetail->id);
            })->findOrFail($weeklyLessonId);

        $subjects = Subject::select('id', 'name', 'grade', 'subject_id')
            ->join('grade_subject', 'subject.id', '=', 'grade_subject.subject_id')
            ->get();

        return view('admin.weekly_lesson.form', [
            'method' => 'put',
            'title' => 'Sửa bài giảng theo tuần của giáo viên',
            'weeklyLesson' => $weeklyLesson,
            'grades' => SchoolClass::GRADES,
            'subjects' => $subjects->groupBy('grade')->toArray(),
            'routing' => route('teacher_weekly_lesson.update', [
                'weeklyLessonId' => $weeklyLessonId,
            ]),
        ]);
    }

    public function update(Request $request, $weeklyLessonId)
    {
        $weeklyLesson = TeacherWeeklyLesson::findOrFail($weeklyLessonId);

        try {
            $weeklyLesson->update([
                'grade' => $request->input('grade'),
                'subject_id' => $request->input('subject'),
                'teacher_lesson_id' => $request->input('lesson'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
            return redirect()->route('teacher_weekly_lesson.index')->with('success', 'Cập nhật thành công!');
        } catch (Exception $e) {
            return redirect()->route('teacher_weekly_lesson.index')->with('error', 'Cập nhật thất bại!');
        }
    }

    public function destroy(Request $request, $weeklyLessonId)
    {
        $weeklyLesson = TeacherWeeklyLesson::when(Admin::user()->inRoles([ROLE_GIAO_VIEN]), function($q) {
            $q->where('teacher_id', Admin::user()->staffDetail->id);
        })->findOrFail($weeklyLessonId);
        try {
            DB::beginTransaction();
            $weeklyLesson->delete();
            $weeklyLesson->teacherWeeklyLessonProgresses()->delete();
            DB::commit();
        } catch (Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
        }

        return response('success');
    }

    public function getLessonByGradeAndSubject(Request $request)
    {
        $lessons = TeacherLesson::select(
                'teacher_lesson.id',
                'teacher_lesson.bai_hoc',
                'teacher_lesson.ten_bai_hoc',
                'teacher_lesson.month_year',
                'teacher_lesson.start_date',
                'teacher_lesson.end_date'
            )
            ->join('teacher_plan', 'teacher_plan.id', '=', 'teacher_lesson.teacher_plan_id')
            ->where([
                'teacher_plan.staff_id' => Admin::user()->staffDetail->id,
                'teacher_plan.grade' => $request->input('grade'),
                'teacher_plan.subject_id' => $request->input('subject'),
            ])
            ->whereBetween('teacher_lesson.month_year', [session()->get('year').'-08-01', (session()->get('year')+1). '-06-01'])
            ->get();

        return $lessons->toJson();
    }

    public function export(Request $request)
    {
        $monthYear = $request->input('month_year');
        list($month, $year) = $monthYear ? explode('-', $monthYear) : [null,null];

        $teacherWeeklyLessons = TeacherWeeklyLesson::with('teacherLesson')
            ->select('teacher_weekly_lesson.*', 'teacher_lesson.month_year')
            ->join('teacher_lesson', 'teacher_lesson.id', '=', 'teacher_weekly_lesson.teacher_lesson_id')
            ->when(Admin::user()->inRoles([ROLE_GIAO_VIEN]), function($q) {
                $q->where('teacher_id', Admin::user()->staffDetail->id);
            })
            ->when($year, function($q) use ($year) {
                $q->whereYear('teacher_lesson.month_year', $year);
            })
            ->when($month, function($q) use ($month) {
                $q->whereMonth('teacher_lesson.month_year', $month);
            })
            ->orderBy('teacher_weekly_lesson.created_at', 'DESC')
            ->get();

        return Excel::download(new TeacherWeeklyLessonExport([
            'teacherWeeklyLessons' => $teacherWeeklyLessons,
            'monthYear' => $monthYear,
        ]), 'Danh sách bài giảng.xlsx');
    }

    public function updateProgress(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('teacher_weekly_lesson_progress')
                ->where('teacher_weekly_lesson_id', $request->get('teacher_weekly_lesson_id'))
                ->whereIn('id', array_keys($request->get('progresses', [])))
                ->update([
                    'is_taught' => true,
                ]);
            DB::table('teacher_weekly_lesson_progress')
                ->where('teacher_weekly_lesson_id', $request->get('teacher_weekly_lesson_id'))
                ->whereNotIn('id', array_keys($request->get('progresses', [])))
                ->update([
                    'is_taught' => false,
                ]);
            DB::commit();
            return redirect()->route('teacher_weekly_lesson.index')->with('success', 'Cập nhật thành công!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('teacher_weekly_lesson.index')->with('error', 'Cập nhật thất bại!');
        }
    }
}
