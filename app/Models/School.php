<?php

namespace App\Models;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class School extends Model
{
    const SCHOOL_PREFIX = [1 => 'B', 2 => 'C', 3 => 'D', 4 => 'BC', 5 => 'CD', 6 => 'A', 7 => 'E'];
    //const ROLE_PREFIX = [5 => 'HT', 6 => 'GV', 7 => 'YT', 8 => 'QL', 12 => 'TTTYT'];
    const ROLE_PREFIX = [5 => 'H', 6 => 'G', 7 => 'Y', 9 => 'Q', 8 => 'Q', 12 => 'T'];
    const SCHOOL_TYPES = [
        1 => "Tiểu học",
        2 => "Trung học cơ sở",
        3 => "Trung học phổ thông",
        4 => "Liên cấp 1-2",
        5 => "Liên cấp 2-3",
        6 => "Mầm non",
        7 => "TTGD Thường Xuyên"
    ];

    const SCHOOL_DISTRICT_TYPES = [
        1 => "Tiểu học",
        2 => "Trung học cơ sở",
        4 => "Liên cấp 1-2",
        6 => "Mầm non",
        7 => "TTGD Thường Xuyên"
    ];

    const SCHOOL_THPT_TYPES = [
        3 => "Trung học phổ thông",
        5 => "Liên cấp 2-3",
    ];

    private static $getList = null;
    public $table = 'school';
    protected $guarded = [];

    public function getHighestGrade() {
        switch($this->school_type) {
            case 1:
                return 5; break;
            case 2:
            case 4:
                return 9; break;
            case 3:
            case 5:
            case 7: 
                return 12; break;
            case 6:
                return 18; break;
        }
    }

    public function getSchoolGrades() {
        switch($this->school_type) {
            case 1:
                return [1,2,3,4,5]; break;
            case 2:
                return [6,7,8,9]; break;
            case 4:
                return [1,2,3,4,5,6,7,8,9]; break;
            case 3:
            case 7: 
                return [10,11,12]; break;
            case 5: 
                return [6,7,8,9,10,11,12]; break;
            case 6:
                return [13,14,15,16,17,18]; break;
        }
    }

    public static function getAccountPrefix($district, $school_type)
    {
        $agencyPrefix = $district->province->gso_id;
        if (!in_array(intval($school_type), [3,5])) $agencyPrefix .= $district->gso_id;
        return $agencyPrefix . self::SCHOOL_PREFIX[$school_type];
    }

    public static function createSchoolWithDefaultUsers($school)
    {
        $newSchool = self::create($school);

        /* Create Account School Manager */
        $accountPrefix = strtoupper($newSchool->school_code . self::ROLE_PREFIX[ROLE_SCHOOL_MANAGER_ID]);
        $currentExist = AdminUser::where('username', 'like', $accountPrefix . '%')->count();
        $dataInsert = [
            'username' => $accountPrefix . ($currentExist + 1),
            'password' => bcrypt(\Config::get('constants.password_reset')),
            'name' => 'Quản lý - ' . $school['school_name'],
            'avatar' => null,
            'created_by' => Admin::user()->id,
            'phone_number' => null,
            'force_change_pass' => 1
        ];
        AdminUser::createAcount($dataInsert, 'school-manager', $newSchool->id);

        //Create School Branch
        $newSchool->createBranch([
            'branch_name' => $school['school_name'],
            'branch_email' => $school['school_email'],
            'branch_phone' => $school['school_phone'],
            'branch_address' => $school['school_address'],
        ], 1);

        return $newSchool;
    }

    public function branches()
    {
        return $this->hasMany(SchoolBranch::class, 'school_id', 'id');
    }

    public function getDefaultBranch()
    {
        return SchoolBranch::where([
            'school_id' => $this->id,
            'is_main_branch' => true
        ])->first();
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function staffs()
    {
        return $this->hasMany(SchoolStaff::class, 'school_id', 'id');
    }

    public function teachers()
    {
        return $this->hasMany(SchoolStaff::class, 'school_id', 'id')->where('position', 3);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'school_id', 'id');
    }

    public function regularGroups() {
        return $this->hasMany(RegularGroup::class, 'school_id', 'id');
    }

    public function schoolReport()
    {
        return $this->hasOne(SchoolReport::class, 'school_id', 'id');
    }

    /* public function createAccount($role, $detailName)
    {
        DB::beginTransaction();
        try {
            $user = AdminUser::createUser($dataInsert);
            $roles = [$role];
            $user->roles()->detach();
            //Insert roles
            if ($roles) {
                $user->roles()->attach($roles);
            }

            UserAgency::create([
                'user_id' => $user->id,
                'agency_id' => $this->id,
                'agency_model' => 'App\Models\School'
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            dd($e);
        }
    } */

    public function createBranch($data, $is_main_branch = 0)
    {
        $data['school_id'] = $this->id;
        $data['is_main_branch'] = $is_main_branch;
        SchoolBranch::create($data);
    }

    public function isTieuHoc()
    {
        return $this->school_type === 1;
    }

    public function isThcs()
    {
        return $this->school_type === 2;
    }

    public function isThpt()
    {
        return $this->school_type === 3;
    }

    public function isLC12()
    {
        return $this->school_type === 4;
    }

    public function isLC23()
    {
        return $this->school_type === 5;
    }

    public function isMamNon()
    {
        return $this->school_type === 6;
    }

    public function isTTGD()
    {
        return $this->school_type === 7;
    }

    public function getSchoolType()
    {
        return self::SCHOOL_TYPES[$this->school_type];
    }

    public function users()
    {
        return $this->morphToMany(AdminUser::class, 'agency', 'user_agency', 'agency_id', 'user_id', 'id', 'id');
    }

    public function accountHieuTruong(){
         return AdminUser::where('username', SchoolStaff::where(['school_id' => $this->id, 'position' => 1])->first()->staff_code)->first();
    }

    public function accountTruongPhong() {
        $districId = $this->district_id;
        $users = AdminUser::with(['agency' => function($query) use ($districId) {
            $query->where([
                'agency_type' => District::class,
                'agency_id' => $districId
            ]);
        }, 'roles' => function ($query) {
            $query->where([
              'role_id' => ROLE_PHONG_GD_ID
            ]);
        }])->get();
        foreach($users as $user) {
            if($user->agency && count($user->roles) > 0) return $user;
        }
        return null;
    }

    public function healthInsurances()
    {
        return $this->hasMany(SchoolHealthInsurance::class, 'school_id', 'id');
    }

    public function hasData()
    {
        if (count($this->classes) != 0 || count($this->staffs) != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getLastestStudentCode()
    {
        $students = Student::where('student_code', 'like', $this->school_code."%")->orderBy('id', 'ASC')->get();
        if (count($students) > 0) {
            $lastestStudent = $students[count($students) - 1];
            $lastedCode = intval(str_replace($this->school_code . 'S', '', $lastestStudent->student_code));
            return $lastedCode;
        }
        return 0;
    }

    public function getLastestStaffCode()
    {
        $staffs = $this->staffs;
        if (count($staffs) > 0) {
            $lastestStaff = $staffs[count($staffs) - 1];
            $lastedCode = intval(str_replace($this->school_code . 'N', '', $lastestStaff->staff_code));
            return $lastedCode;
        }
        return 0;
    }

    public function generateStaffCode($no)
    {
        $no = str_pad($no, 3, '0', STR_PAD_LEFT);
        return $this->school_code . "N{$no}";
    }

    public function generateStudentCode($no)
    {
        $no = str_pad($no, 9, '0', STR_PAD_LEFT);
        return $this->school_code . "S{$no}";
    }

    public function countingStudentsWithInfectiousDiseases($diagnosis)
    {
        $count_student = 0;
        $students = $this->students;
        $check_diagnosis = false;
        foreach ($students as $student) {
            if (count($student->healthAbnormals) > 0) {
                foreach ($student->healthAbnormals as $healthAbnormal) {
                    if ($healthAbnormal->getOriginal('diagnosis') == $diagnosis && $healthAbnormal->getOriginal('patient_status') != 3) {
                        $check_diagnosis = true;
                    }
                }
                $check_diagnosis ? $count_student++ : false;
                $check_diagnosis = false;
            }
        }
        return $count_student;
    }

    public function getTotalInsuranceAttribute()
    {
        return $this->healthInsurances->count();
    }

    public function getTotalBhTunguyenAttribute()
    {
        return $this->healthInsurances->filter(function ($item) {
            return $item->getOriginal('form_type') == 1;
        })->count();
    }

    public function getTotalBhChinhsachAttribute()
    {
        return $this->healthInsurances->filter(function ($item) {
            return $item->getOriginal('form_type') == 2;
        })->count();
    }

    public function getTotalBranchAttribute()
    {
        return $this->branches->count();
    }

    public function getTotalClassAttribute()
    {
        return $this->classes->count();
    }   

    public function getMaximumLesson(){
        if(in_array($this->school_type, [1,6])) return 19+20;
        else return 28+24;
    }

    public function targets() {
        return $this->hasMany(Target::class, 'school_id', 'id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}