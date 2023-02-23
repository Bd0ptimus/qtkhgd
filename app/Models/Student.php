<?php

namespace App\Models;

use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Models\Base\People;
use Staudenmeir\LaravelUpsert\Eloquent\HasUpsertQueries;
use App\Scopes\YearScope;
class Student extends People
{
    use HasUpsertQueries;

    const DISABILITIES = [
        '' => '', 1 => 'Khiếm thính', 2 => 'Khiếm thị', 3 => 'Tự kỷ', 4 => 'Khuyết tật khác về sức khỏe'
    ];
    const FPTP = [
        '' => '',
        1 => 'Hộ nghèo', 2 => 'Hộ cận nghèo', 3 => 'Không nơi nương tựa',
        4 => 'Trẻ mồ côi', 5 => 'Loại khác'
    ];
    const HEALTH_HISTORY = [1 => 'Tốt', 2 => 'Không tốt'];
    const BORN_HISTORY = [
        1 => 'Bình thường', 2 => 'Đẻ ngạt', 3 => 'Đẻ thiếu tháng',
        4 => 'Mẹ bị bệnh trong kỳ mang thai', 5 => 'Đẻ thừa tháng,Đẻ có can thiệp'
    ];
    const DISEASE_HISTORY = [
        0 => "Không có", 1 => 'Hen', 2 => 'Động Kinh', 3 => 'Dị ứng', 4 => 'Tim bẩm sinh'
    ];
    const TREATING_DISEASE = [2 => 'Không', 1 => 'Có'];
    const TC_STATUS = [1 => "Có", 0 => "Không", 2 => "Không nhớ rõ"];
    protected static $listValues = [
        'gender' => Student::GENDER,
        'ethnic' => Student::ETHNICS,
        'religion' => Student::RELIGIONS,
        'nationality' => Student::NATIONALITIES,
        "health_history" => Student::HEALTH_HISTORY,
        "born_history" => Student::BORN_HISTORY,
        "disabilities" => Student::DISABILITIES,
        "fptp" => Student::FPTP,
        "disease_history" => Student::DISEASE_HISTORY,
        "treating_disease" => Student::TREATING_DISEASE,
        "tc_bcg" => Student::TC_STATUS,
        "tc_bhhguv_m1" => Student::TC_STATUS,
        "tc_bhhguv_m2" => Student::TC_STATUS,
        "tc_bhhguv_m3" => Student::TC_STATUS,
        "tc_bailiet_m1" => Student::TC_STATUS,
        "tc_bailiet_m2" => Student::TC_STATUS,
        "tc_bailiet_m3" => Student::TC_STATUS,
        "tc_viemganb_m1" => Student::TC_STATUS,
        "tc_viemganb_m2" => Student::TC_STATUS,
        "tc_viemganb_m3" => Student::TC_STATUS,
        "tc_soi" => Student::TC_STATUS,
        "tc_viemnaonb" => Student::TC_STATUS,
        "grade" => SchoolClass::GRADES,
    ];
    public $table = 'student';
    protected $guarded = [];
    protected $hidden = ['pivot'];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->createOrAssignParentAccount();
        });
        self::updated(function ($model) {

        });

        self::deleted(function ($model) {
            // $model->insurance()->delete();
            // $model->healthIndexes()->delete();
            // $model->healthAbnormals()->delete();
            // $model->specialistTests()->delete();
        });
        static::addGlobalScope(new YearScope);
    }

    public function createOrAssignParentAccount()
    {
        $model = $this;
        $parentPhone = $model->father_phone;
        if (empty($parentPhone)) $parentPhone = $model->mother_phone;
        $parentName = $model->father_name;
        if (empty($parentName)) $parentName = $model->mother_name;

        if (!empty($parentPhone)) {
            $parent = AdminUser::where('username', $parentPhone)->first();
            if (!$parent) {
                $parent = AdminUser::createAcount([
                    'username' => $parentPhone,
                    'user_detail' => null,
                    'password' => bcrypt(\Config::get('constants.password_reset')),
                    'name' => $parentName,
                    'avatar' => null,
                    'created_by' => Admin::user()->id,
                    'phone_number' => $parentPhone,
                    'force_change_pass' => 1
                ], 'parents', $model->id);
            } else {
                UserAgency::updateOrCreate([
                    "user_id" => $parent->id,
                    "agency_id" => $model->id,
                    "agency_type" => "App\Models\Student"
                ]);
            }
        }
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function schoolBranch()
    {
        return $this->belongsTo(SchoolBranch::class, 'school_branch_id', 'id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'id');
    }

    public function parent_accounts()
    {
        return $this->morphToMany(AdminUser::class, 'agency', 'user_agency', 'agency_id','user_id','id', 'id');
    }

    public function insurance()
    {
        return $this->hasOne(SchoolHealthInsurance::class, 'student_id', 'id');
    }

    public function healthIndexes()
    {
        return $this->hasMany(StudentHealthIndex::class, 'student_id', 'id');
    }

    public function healthAbnormals()
    {
        return $this->morphMany(HealthAbnormal::class, 'object');
    }

    public function medicalDeclarations()
    {
        return $this->morphMany(MedicalDeclaration::class, 'object');
    }

    public function dailyHealthChecks()
    {
        return $this->morphMany(DailyHealthCheck::class, 'object');
    }

    public function vaccineHistory() {
        return $this->morphMany(VaccineHistory::class, 'object');
    }

    public function specialistTests()
    {
        return $this->hasMany(StudentSpecialistTest::class, 'student_id', 'id');
    }

    public function ageInMonth($endMonth = null)
    {
        $birthday = new \DateTime($this->getOriginal('dob'));
        $diff = $birthday->diff(new \DateTime($endMonth));
        $months = $diff->format('%m') + 12 * $diff->format('%y');
        return $months;
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

    public function getGradeAttribute($value)
    {
        return !empty($value) ? SchoolClass::GRADES[$value] : null;
    }

    public function getDisabilitiesAttribute($value)
    {
        return !empty($value) ? Student::DISABILITIES[$value] : 'Không có';
    }

    public function getHealthHistoryAttribute($value)
    {
        return !empty($value) ? Student::HEALTH_HISTORY[$value] : 'Không có';
    }

    public function getBornHistoryAttribute($value)
    {
        return !empty($value) ? Student::BORN_HISTORY[$value] : null;
    }

    public function getDiseaseHistoryAttribute($value)
    {
        return !empty($value) ? Student::DISEASE_HISTORY[$value] : 'Không có';
    }

    public function getTreatingDiseaseAttribute($value)
    {
        return !empty($value) ? Student::TREATING_DISEASE[$value] : 'Không có';
    }

    public function getDobAttribute($value)
    {
        return !empty($value) ? date('d/m/Y', strtotime($value)) : '';
    }

    public function getFptpAttribute($value)
    {
        return !empty($value) ? Student::FPTP[$value] : '';
    }

    public static function getBieudophattrien($students){
        foreach($students as $key => $student){
            $weight_chart = []; //[month_age, weight]
            $height_chart = []; //[month_age, height]
            $weight_per_height_chart = []; //[height, weight, month_age]
            foreach ($student->healthIndexes as $student_health_index){
                $month_age = $student_health_index->month_age;
                if($student_health_index->weight){
                    array_push($weight_chart, [$month_age, $student_health_index->weight]);
                    if($student_health_index->height){
                        array_push($weight_per_height_chart, [$student_health_index->height, $student_health_index->weight, $month_age]);
                    }
                }
                if($student_health_index->height){
                    array_push($height_chart, [$month_age, $student_health_index->height]);
                }
            }
            $students[$key]->weight_chart = count($height_chart) > 0 ? json_encode($weight_chart) : json_encode([[]]);
            $students[$key]->height_chart = count($height_chart) > 0 ? json_encode($height_chart) : json_encode([[]]);
            $students[$key]->weight_per_height_chart = count($weight_per_height_chart) > 0 ? json_encode($weight_per_height_chart) : json_encode([[]]);
        }
        return $students;
    }
}