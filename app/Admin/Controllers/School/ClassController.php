<?php

namespace App\Admin\Controllers\School;

use App\Admin\Models\Imports\ImportClass;
use App\Admin\Services\CommonService;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\HeadingRowImport;

class ClassController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function viewClassList($id)
    {
        $positions = request()->query('position', []);
        $school = School::with('classes.schoolBranch')->find($id);

        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        $activity = "Xem trang danh sách lớp học";
        $this->saveActivityLog($activity, $school->id);
        return $this->renderView('admin.school.class.class_list', [
            'school' => $school,
            'positions' => $positions
        ]);
    }

    public function addClass($id)
    {
        $school = School::with('branches')->find($id);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $data = [
            'grades' => $this->commonService->getGradeBySchoolType($school->school_type)
        ];

        return $this->renderView('admin.school.class.form_class', [
            'title' => 'Thêm lớp cho trường',
            'routing' => route('admin.school.post_add_class', ['id' => $school->id]),
            'school' => $school,
            'data' => $data
        ]);
    }

    public function postAddClass($id)
    {
        /** @var $school School */
        $school = School::with('branches')->find($id);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $data = request()->only([
            'grade',
            'class_name',
            'school_branch_id',
        ]);
        $data['school_id'] = $school->id;

        $validator = ImportClass::validator($data);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $class = SchoolClass::create($data);
        $activity = "Thêm mới lớp học "
        . ' -  Khối "' . SchoolClass::GRADES[$class->grade] . '"'
        . ' -  Lớp "' . $class->class_name . '"'
        . ' -  Điểm trường "' . collect($class->school->branches)->where('id', intval($class->school_branch_id))->first()['branch_name'] . '"';
        $this->saveActivityLog($activity, $class->school_id, $class->school_branch_id);

        return redirect()->route('admin.school.view_class_list', [
            'id' => $school->id
        ])->with('success', 'Thêm lớp thành công!');
    }

    public function editClass($id)
    {
        $class = SchoolClass::with('school.branches')->find($id);
        if (is_null($class)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $data = [
            'grades' => $this->commonService->getGradeBySchoolType($class->school->school_type)
        ];

        return $this->renderView('admin.school.class.form_class', [
            'title' => 'Chỉnh sửa lớp cho trường',
            'routing' => route('admin.school.post_edit_class', ['id' => $class->id]),
            'school' => $class->school,
            'class' => $class,
            'data' => $data
        ]);
    }

    public function postEditClass($id)
    {
        /** @var $class SchoolClass */
        $class = SchoolClass::with('school.branches')->find($id);
        if (is_null($class)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $data = request()->only([
            'grade',
            'class_name',
            'school_branch_id',
        ]);
        $data['school_id'] = $class->school->id;

        $validator = ImportClass::validator($data, $class->id);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $old_class = $class->replicate();
        $class->update($data);
        $changes = $class->getChanges();
        unset($changes['updated_at']);
        $activity = "Chỉnh sửa thông tin lớp học";
        if(isset($changes['grade'])) $activity .= ' -  Khối: "' . SchoolClass::GRADES[$old_class->grade] . '" -> "' . SchoolClass::GRADES[$changes['grade']] . '"';
        if(isset($changes['class_name'])) $activity .= ' -  Lớp: "' . $old_class->class_name . '" -> "' . $changes['class_name']. '"';
        if(isset($changes['school_branch_id'])) $activity .= ' -  Điểm trường: "' . collect($class->school->branches)->where('id', intval($old_class->school_branch_id))->first()['branch_name']  . '" -> "' . collect($class->school->branches)->where('id', intval($class->school_branch_id))->first()['branch_name']. '"';
        $this->saveActivityLog($activity, $class->school_id, $class->school_branch_id);
        return redirect()->route('admin.school.view_class_list', [
            'id' => $class->school->id
        ])->with('success', 'Chỉnh sửa lớp thành công!');
    }

    /**
     * Delete class
     *
     * @return JsonResponse
     */
    public function deleteClass()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $class = SchoolClass::whereIn('id', request('ids'))->first();
            if($class) $this->saveActivityLog('Xóa lớp học: "' . $class->class_name . '"', $class->school_id, $class->school_branch_id);
            SchoolClass::destroy(request('ids'));

            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Delete class
     *
     * @return JsonResponse
     */
    public function deleteAllClass($id)
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $allClassIds = SchoolClass::where('school_id', $id)->pluck('id')->toArray();
            SchoolClass::destroy($allClassIds);
            $activity = "Xóa tất cả các lớp học";
            $this->saveActivityLog($activity, $id);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function importClass($id)
    {
        return $this->renderView('admin.school.class.import_class', [
            'school' => School::where('id', $id)->with('district', 'district.province')->first()
        ]);
    }

    public function postImportClass($id)
    {
        $school = School::where('id', $id)->with('district.province')->first();
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $validator = Validator::make(request()->all(), [
            'file_upload' => 'required|file',
        ], [
            'file_upload.required' => trans('validation.file_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        /* Validate Heading */
        $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];
        if (!ImportClass::validateFileHeader($heading)) {
            return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
        }

        $branches = $school->branches;
        $branchId = null;

        if (count($branches) === 1) {
            $branchId = $branches->first()->id;
        }

        $results = (new ImportClass)->toArray(request()->file('file_upload'))[0];
        $results = ImportClass::mappingKey($results);
        $results = ImportClass::filterData($results);

        DB::beginTransaction();
        try {
            //Import Class
            $data_activity = "Import lớp học:<br>";
            foreach ($results as $index => $result) {
                //Render Data and create
                $result['school_id'] = $school->id;
                $result['school_branch_id'] = $branchId;
                $validator = ImportClass::validator($result);
                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->getMessageBag()->first() . ' Tại dòng ' . ($index + 1));
                }
                SchoolClass::create($result);
                $data_activity .= $result['class_name']. '<br>'; 
            }
            $activity = "Import lớp học tại trường";
            $this->saveActivityLog($activity, $school->id, null, $data_activity);
            DB::commit();
            return redirect()
                ->route('admin.school.view_class_list', ['id' => $school->id])
                ->with('success', 'Nhập dữ liệu lớp thành công!');
        } catch (Exception $e) {
            DB::rollback();
            dd($e);
        }
    }
}