<?php

namespace App\Admin\Controllers\School;

use App\Admin\Models\Exports\Students\ExportStudents;
use App\Admin\Models\Imports\ImportStudent;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolStaff;
use App\Models\Student;
use App\Admin\Helpers\Utils;
use App\Models\StudentHealthIndex;
use Exception;
use App\Admin\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\HeadingRowImport;

class StudentController extends Controller
{
    public function viewStudentList($id)
    {
        $request_class = request()->query('class', null);

        $school = School::with(['classes', 'students' => function ($query) use ($request_class) {
            if (!empty($request_class) && $request_class != 'all') {
                $query->where('class_id', $request_class);
            } elseif($request_class == 'all') {
                $query->where('class_id', '!=', 0);
            }
        }, 'students.class'])->find($id);

        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        if (!$request_class && count($school->classes) > 0) return redirect()->route('admin.school.view_student_list', ['id' => $id, 'class' => $school->classes[0]->id]);
        //$conditions = [];

        $title = 'Danh sách học sinh';
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $title],
        ];

        return $this->renderView('admin.school.student.index', [
            'school' => $school,
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'request_class' => $request_class
        ]);
    }

    public function editStudent($id)
    {
        $staff = SchoolStaff::with('school.branches')->find($id);
        if (is_null($staff)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $data = [
            'gender' => SchoolStaff::GENDER,
            'ethnic' => SchoolStaff::ETHNICS,
            'religion' => SchoolStaff::RELIGIONS,
            'nationality' => SchoolStaff::NATIONALITIES,
            'qualification' => SchoolStaff::QUALIFICATIONS,
            'position' => SchoolStaff::POSITIONS,
            'status' => SchoolStaff::STATUS,
        ];

        return $this->renderView('admin.school.staff.form_staff', [
            'title' => 'Chỉnh sửa nhân viên cho trường',
            'routing' => route('admin.school.post_edit_staff', ['id' => $staff->id]),
            'school' => $staff->school,
            'staff' => $staff,
            'data' => $data
        ]);
    }

    public function postEditStudent($id)
    {
        $data = request()->only([
            'fullname',
            'dob',
            'gender',
            'ethnic',
            'religion',
            'nationality',
            'address',
            'identity_card',
            'phone_number',
            'email',
            'qualification',
            'position',
            'school_branch_id',
            'status',
            'responsible',
            'concurrently',
            'professional_certificate'
        ]);

        $validator = ImportStudent::validator($data);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /** @var $staff SchoolStaff */
        $staff = SchoolStaff::with('school')->find($id);
        if (is_null($staff)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        $staff->update($data);

        return redirect()->route('admin.school.view_staff_list', [
            'id' => $staff->school->id
        ])->with('success', 'Chỉnh sửa nhân viên thành công!');
    }

    /**
     * Delete staff
     *
     * @return JsonResponse
     */
    public function deleteStudent()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            SchoolStaff::destroy($arrID);

            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function importStudent($id)
    {
        $school = School::where('id', $id)->with('district', 'district.province')->first();
        $district = $school->district;
        $province = $district->province;
        $data['title'] = "Import học sinh";
        $data['breadcrumbs'] = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $data['title']],
        ];

        $data['school'] = $school;

        return $this->renderView('admin.school.student.import', $data);
    }

    public function importStudentSmas($id, Request $request) {
        $school = School::where('id', $id)->with('district', 'district.province')->first();
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        
        if ($request->isMethod('post')) {

            $validator = Validator::make(request()->all(), [
                'file_upload' => 'required|file',
            ], [
                'file_upload.required' => trans('validation.file_required'),
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $importData = (new ImportStudent)->toArray(request()->file('file_upload'))[0];
            $importData = ImportStudent::buildFullDataSmas($importData, $school);

            if(!$importData) return redirect()->back()->with('error', 'Định dạng file không hợp lệ');
            
            //dd($importData[51]);
            DB::beginTransaction();
            try {

                $chunk_data = array_chunk($importData, 100);
                if (isset($chunk_data) && !empty($chunk_data)) {
                    foreach ($chunk_data as $chunk_data_val) {
                        Student::insert($chunk_data_val);
                    }
                }

                DB::commit();
                return redirect()
                    ->route('admin.school.view_student_list', ['id' => $school->id, 'class' => $importData[0]['class_id']])
                    ->with('success', 'Nhập dữ liệu học sinh thành công!');
            } catch (Exception $ex) {
                DB::rollback();
                if(env('APP_ENV') !== 'production') dd($ex);
            }
        }

        
        $district = $school->district;
        $province = $district->province;
        $data['title'] = "Import học sinh";
        $data['breadcrumbs'] = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $data['title']],
        ];

        $data['school'] = $school;

        return $this->renderView('admin.school.student.import_smas', $data);
    }

    public function postImportStudent($id)
    {
        $school = School::where('id', $id)->with('students', 'district.province')->first();
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
        if (!ImportStudent::validateFileHeader($heading)) {
            return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
        }

        $branches = $school->branches;
        $branchId = null;

        if (count($branches) === 1) {
            $branchId = $branches->first()->id;
        }

        $importData = (new ImportStudent)->toArray(request()->file('file_upload'))[0];
        $importData = ImportStudent::mappingKey($importData);
        
        $specialCharacters = [' ', '!', '@', '$', '%', '^', '&', '*', '(', ')', '_', '+', '=', 
        '"', "'", ';', ':', '?', '>', '.', '<', ',', '~', '`', '|'];
        foreach($importData as $index => $row) {
            //$importData[$index]['dob'] = str_replace(' ','', $row['dob']);
            $rowNo = $index + 2;
            foreach (str_split($importData[$index]['dob']) as $char) {
                if(!Utils::formatDate($row['dob']) || in_array($char, $specialCharacters)) {
                    $rowFullname = $row['fullname'];
                    $rowBob = $row['dob'];
                    $message = "Ngày sinh của $rowFullname ($rowBob) có chứa dấu cách hoặc ký tự đặc biệt <br>";
                    $validator->getMessageBag()->add('file_upload', $message);
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
        }
        
        $importData = ImportStudent::filterData($importData);
        
        $validator = ImportStudent::validator($importData);
        if ($validator->fails()) {
            $message = ImportStudent::getErrorMessage($validator->errors());
            $validator->getMessageBag()->add('file_upload', $message);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $importData = ImportStudent::buildFullData($importData, $school);
        
        DB::beginTransaction();
        try {

            $chunk_data = array_chunk($importData, 100);
            if (isset($chunk_data) && !empty($chunk_data)) {
                foreach ($chunk_data as $chunk_data_val) {
                    Student::insert($chunk_data_val);
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.school.view_student_list', ['id' => $school->id, 'class' => $importData[0]['class_id']])
                ->with('success', 'Nhập dữ liệu học sinh thành công!');
        } catch (Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
    }

    public function exportStudent($id, Request $request)
    {
        $class = $request->query('class', null);

        /** @var $school School */
        $school = School::with([
            'students' => function ($query) use ($class) {
                if (!empty($class) && $class != 'all') {
                    $query->where('class_id', $class);
                }
            },
            'students.class'
        ])->find($id);

        if (empty($school)) {
            return redirect()->with('error', 'Dữ liệu không đúng.');
        }

        return (new ExportStudents($school->students))->download('school_students.xls');
    }

    public function view($id)
    {
        $student = Student::where('id', $id)->with('school', 'class')->first();
        $school = $student->school;
        $title = 'Thông tin học sinh';
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $title],
        ];
        return $this->renderView('admin.school.student.view', [
            'student' => Student::where('id', $id)->first(),
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'class' => $student->class,
        ]);
    }

    public function delete()
    {
        if (!Auth::guard('admin')->user()->canDeleteStudent()) {
            return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền xóa học sinh!']);
        }
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $ids = request('ids');
            Student::destroy($ids);
            return response()->json(array('success' => true));
        }

    }

    public function edit($id)
    {
        //Todo: check psermission
        $student = Student::where('id', $id)->with('school', 'class')->first();
        $school = $student->school;
        $schools = [$school];
        $classes = SchoolClass::where('school_id', $student->school_id)->get();

        $title = "Chỉnh sửa học sinh";
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $student->school_id])],
            ['name' => 'Danh sách học sinh', 'link' => route('admin.school.view_student_list', ['id' => $student->school_id])],
            ['name' => $title],
        ];

        return $this->renderView('admin.school.student.edit', ['student' => $student, 'title' => $title, 'breadcrumbs' => $breadcrumbs, 'schools' => $schools, 'classes' => $classes]);
    }

    public function postEdit($id)
    {
        //Todo: check psermission
        $student = Student::find($id);
        $data = request()->all();
        DB::beginTransaction();
        try {
            $student->update($data);
            DB::commit();
            return redirect()->route('admin.school.view_student_list',  ['id' => $student->school_id, 'class' => $student->class_id])->with('success', 'Cập nhập học sinh thành công!');
        } catch (Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
    }

    public function createBySchool($school_id)
    {
        //Todo: check psermission
        $student = new Student;
        $school = School::find($school_id);
        $classes = SchoolClass::where('school_id', $school->id)->get();
        $title = "Thêm học sinh";
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => 'Danh sách học sinh', 'link' => route('admin.school.view_student_list', ['id' => $school->id])],
            ['name' => $title],
        ];
        $schools = [$school];
        return $this->renderView('admin.school.student.create_by_school', ['student' => $student, 'title' => $title, 'breadcrumbs' => $breadcrumbs, 'schools' => $schools, 'classes' => $classes]);
    }

    public function storeCreateBySchool($school_id)
    {
        $data = request()->all();
    
        $school = School::find($school_id);
        DB::beginTransaction();
        try {
            $currentExist = $school->getLastestStudentCode();
            $no = $currentExist + 2;
         
            $studentCode = $school->generateStudentCode($no);
            $data['student_code'] = $studentCode;
            $class = SchoolClass::where('id', $data['class_id'])->first();
            $data['grade'] = $class->grade;
            $data['school_branch_id'] = $class->school_branch_id;
            $data['school_id'] = $school_id;  
         
            Student::create($data);
            DB::commit();
            return redirect()->route('admin.school.view_student_list', ['id' => $school_id, 'class' => $data['class_id']])->with('success', 'Tạo học sinh thành công!');
        } catch (Exception $e) {
            report($e);
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($e);
        }
    }

    public function assignClass(Request $request)
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $classId = request('classId');
            $selectedStudentIds = request('selectedStudentIds');
            $class = SchoolClass::find($classId);

            if (is_null($class)) {
                return response()->json(['error' => 1, 'msg' => 'Class not found']);
            }

            Student::whereIn('id', $selectedStudentIds)->update(['class_id' => $classId]);

            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function deleteAllStudentsByClass($id, $class_id, Request $request) {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            if(Admin::user()->isAdministrator()) {
                $class = SchoolClass::where(['id' => $class_id, 'school_id' => $id])->with('students')->first();

                if (is_null($class)) {
                    return response()->json(['error' => 1, 'msg' => 'Class not found']);
                }
    
                $students = $class->students->pluck('id')->toArray();
                Student::destroy($students);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}