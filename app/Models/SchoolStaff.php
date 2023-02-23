<?php

namespace App\Models;

use App\Admin\Admin;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use App\Models\Base\People;
use Illuminate\Support\Facades\DB;

class SchoolStaff extends People
{
    public $table = 'school_staff';
    protected $guarded = [];

    const QUALIFICATIONS = [
        0 => 'Khác', 1 => 'Đại học', 2 => 'Cao đẳng',
        3 => 'Thạc sỹ', 4 => 'Trung cấp', 5 => 'Sơ cấp',
    ];
    
    const POSITIONS = [
        1 => 'Hiệu trưởng', 2 => 'Phó hiệu trưởng', 3 => 'Giáo viên',
        4 => 'Nhân viên Kế toán', 5 => 'Nhân viên văn thư', 6 => 'Nhân viên Y tế',
        7 => 'Nhân viên thư viện', 8 => 'Nhân viên bảo vệ', 9 => 'Nhân viên nuôi dưỡng'
    ];

    const MAPPING_POSITION_ROLE = [ 1 => 5, 3 => 6, 6 => 7];
    const STATUS = [
        0 => 'Đang công tác tại trường',
        1 => 'Nghỉ chế độ',
        2 => 'Nghi hưu',
        3 => 'Khác',
    ];

    public static function boot()
    {
        parent::boot();

        self::updated(function ($model) {

        });

        self::deleted(function ($model) {
            $model->staffGrades()->delete();
            $model->staffSubjects()->delete();
            $model->memberGroups()->delete();
        });
    }

    protected static $listValues = [
        'gender' => SchoolStaff::GENDER,
        'ethnic' => SchoolStaff::ETHNICS,
        'religion' => SchoolStaff::RELIGIONS,
        'nationality' => SchoolStaff::NATIONALITIES,
        'qualification' => SchoolStaff::QUALIFICATIONS,
        'position' => SchoolStaff::POSITIONS,
        'status' => SchoolStaff::STATUS,
        'professional_certificate' => SchoolStaff::YES_NO,
        'responsible' => SchoolStaff::YES_NO,
        'concurrently' => SchoolStaff::YES_NO,
    ];


    public static function getPosition($id)
    {
        return self::POSITIONS[$id];
    }

    public function getPositionAttribute($value) {
        if(!empty($value)) return self::POSITIONS[$value];
    }

    public function getQualificationAttribute($value)
    {
        if(!is_null($value)) return self::QUALIFICATIONS[$value];
    }

    public static function getStatus($id)
    {
        return self::STATUS[$id];
    }

    public function getStatusAttribute($value) {
        if($value !== null) return self::STATUS[$value];
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function schoolBranch()
    {
        return $this->belongsTo(SchoolBranch::class, 'school_branch_id', 'id');
    }

    public function createAccount()
    {
        $school = School::where('id', $this->school_id)->with(['district','staffs'])->first();
        $role = self::MAPPING_POSITION_ROLE[$this->position];
        $accountPrefix = School::getAccountPrefix($school->district, $school->school_type). School::ROLE_PREFIX[$role];
        $currentExist = AdminUser::where('username', 'LIKE', $accountPrefix . '%')->count();
        $no = $currentExist + 1;
        $no = str_pad($no, 3, '0', STR_PAD_LEFT);
        $dataInsert = [
            'username' => $accountPrefix.$no,
            'user_detail' => $this->id,
            'password' => bcrypt(\Config::get('constants.password_reset')),
            'name' => $this->fullname,
            'avatar' => null,
            'created_by' => Admin::user()->id,
            'phone_number' => $this->phone_number,
            'force_change_pass' => 1
        ];

        DB::beginTransaction();
        try {
            $user = AdminUser::createUser($dataInsert);
            $roles = AdminRole::where('name', self::POSITIONS[$this->position])->pluck('id')->toArray();
            $user->roles()->sync($roles);
            UserAgency::create([
                'user_id' => $user->id,
                'agency_id' => $this->school_id,
                'agency_type' => 'App\Models\School'
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($e);
        }
    }

    /**
     * Can create Account
     *
     * @return bool
     */
    public function canCreateAccount()
    {
        // 1 'Hiệu trưởng', 3 'Giáo viên', 6 'Nhân viên Y tế'
        return in_array($this->position, [1,3,6]);
    }

    public function getDobAttribute($value) {
        return !empty($value) ? date('d/m/Y', strtotime($value)) : '';
    }

    public function getGenderAttribute($value)
    {
        return !empty($value) ? Student::GENDER[$value] : null;
    }

    public function getEthnicAttribute($value)
    {
        return !empty($value) ? Student::ETHNICS[$value] : null;
    }

    public function getReligionAttribute($value)
    {
        return $value !== null ? Student::RELIGIONS[$value] : "";
    }

    public function getNationalityAttribute($value)
    {
        return !empty($value) ? Student::NATIONALITIES[$value] : null;
    }

    public function assignToClass($class_id) {
        
        DB::beginTransaction();
        try {
            $user = AdminUser::where('username', $this->staff_code)->first();
            
            $class = SchoolClass::find($class_id);
            $class_name = $class->class_name ?? null;
            $route_name = 'admin.school.staff.assign_class';
            $activity = 'Phân công giáo viên đứng lớp: "'. $this->fullname . '"';
            if($class_name) $activity_assign_class = ' - Đứng lớp: "'. $class_name .'"';

            if(!$user) {
                $dataInsert = [
                    'username' => $this->staff_code,
                    'user_detail' => $this->id,
                    'password' => bcrypt(\Config::get('constants.password_reset')),
                    'name' => $this->fullname,
                    'avatar' => null,
                    'created_by' => Admin::user()->id,
                    'phone_number' => $this->phone_number,
                    'force_change_pass' => 1
                ];
    
                $user = AdminUser::createUser($dataInsert);
            } else $user->update(['status' => 1]);

            if($this->getOriginal('position') == 3) {
                $roles = AdminRole::where('name', $this->position)->pluck('id')->toArray();
                $user->roles()->sync($roles);
                
                $userAgency = UserAgency::where([
                    'user_id' => $user->id,
                    'agency_type' => 'App\Models\SchoolClass'
                ])->first();
                $old_class_id = $userAgency->agency_id ?? null;
                if(!$userAgency) {
                    $userAgency = UserAgency::create([
                        'user_id' => $user->id,
                        'agency_id' => $class_id,
                        'agency_type' => 'App\Models\SchoolClass'
                    ]);
                } else $userAgency->update(['agency_id' => $class_id]);
                
                $changes = $userAgency->getChanges();
                if(isset($changes['agency_id'])){
                    $old_class = SchoolClass::find($old_class_id);
                    if($old_class) $activity_assign_class = ' Đứng lớp: "' . $old_class->class_name . '" -> "'. $class->class_name . '"';
                }
                $activity .= $activity_assign_class;
                $controller = new \App\Http\Controllers\Controller;
                $controller->saveActivityLog($activity, $this->school_id, $this->school_branch_id, null, $route_name);
                UserAgency::where([
                    'user_id' => $user->id,
                    'agency_type' => 'App\Models\SchoolClass'
                ])->where('id', '!=', $userAgency->id)->delete();
            }
                 
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($e);
        }
    }

    public function staffSubjects() {
        return $this->hasMany(StaffSubject::class, 'staff_id', 'id');
    }
    
    public function subjects() {
        return $this->hasManyThrough(Subject::class, StaffSubject::class,'staff_id',  'id', 'id', 'subject_id');
    }

    public function staffGrades() {
        return $this->hasMany(StaffGrade::class, 'staff_id', 'id');
    }

    public function assignedClassSubject() {
        return $this->hasMany(ClassSubject::class, 'staff_id', 'id');
    }

    public function teacherPlans() {
        return $this->hasMany(TeacherPlan::class, 'staff_id', 'id');
    }

    public function manageGroup() {
        return $this->hasOne(RegularGroupStaff::class, 'staff_id', 'id')->where('member_role', GROUP_LEADER);
    }
    
    public function memberGroups()
    {
        return $this->hasMany(RegularGroupStaff::class, 'staff_id', 'id')->where('member_role', GROUP_MEMBER);
    }

    public function targets() {
        return $this->hasMany(Target::class, 'staff_id', 'id');
    }

    public function targetPoints() {
        return $this->hasMany(TargetPoint::class, 'staff_id', 'id');
    }


    public function linkingSchools() {
        return $this->hasMany(StaffLinkingSchool::class, 'staff_id', 'id');
    }
}