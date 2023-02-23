<?php

namespace App\Admin\Controllers\School;

use App\Admin\Services\RegularGroupService;
use App\Admin\Services\SchoolClassService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\StaffService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TimetableService;
use App\Http\Controllers\Controller;
use App\Models\Timetable;
use Exception;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    protected $schoolService;
    protected $timetableService;
    
    public function __construct(
        SchoolService $schoolService,
        TimetableService $timetableService
    ) {
        $this->schoolService = $schoolService;
        $this->timetableService = $timetableService;
    }

    public function index($schoolId) {
        return view('admin.school.timetable.index', [
            'school' => $this->schoolService->findById($schoolId),
            'timetables' => $this->timetableService->findBySchool($schoolId) 
        ]);
    }

    public function view($schoolId, $timetableId) {
        $data = $this->timetableService->view($timetableId);
        $data['school'] = $this->schoolService->findById($schoolId);
        return view('admin.school.timetable.view', $data);
    }

    public function edit($schoolId, $timetableId) {
        $data = $this->timetableService->view($timetableId);
        $data['school'] = $this->schoolService->findById($schoolId);
        return view('admin.school.timetable.edit', $data);
    }
    
    public function autoGenerateTimetable($schoolId) {
        $result = $this->timetableService->autoGenerateTimetable($schoolId);

        return redirect()->back()->with($result['success'] == false ? 'error' : 'success',$result['message']);
    }

    public function delete($schoolId, $timetableId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                Timetable::destroy($timetableId);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function active($schoolId, $timetableId) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            try {
                Timetable::where(['school_id'=> $schoolId])->update(['is_actived' => 0]);
                Timetable::where(['id' => $timetableId])->update(['is_actived' => 1]);
            } catch ( Exception $e) {
                response()->json(['error' => 1, 'msg' => 'Đã có lỗi']);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function staffTimetable($schoolId, $staffId) {
        
    }
}
