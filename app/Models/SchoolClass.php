<?php

namespace App\Models;

use App\Models\Base\BaseModel as Model;
use App\Admin\Models\AdminUser;
use App\Admin\Admin;

class SchoolClass extends Model
{
    const GRADES = [
        13 => '3-12 tháng', 14 => '13-24 tháng', 15 => '25-36 tháng',
        16 => '3-4 tuổi', 17 => '4-5 tuổi', 18 => '5-6 tuổi',
        1 => 'Khối 1', 2 => 'Khối 2', 3 => 'Khối 3', 4 => 'Khối 4', 5 => 'Khối 5',
        6 => 'Khối 6', 7 => 'Khối 7', 8 => 'Khối 8', 9 => 'Khối 9',
        10 => 'Khối 10', 11 => 'Khối 11', 12 => 'Khối 12', 0 => 'Chưa xác định'
    ];
    const LAST_GRADE_SCHOOL_TYPE = [
        1 => 5, // "Tiểu học"
        2 => 9, // "Trung học cơ sở"
        3 => 12, // "Trung học phổ thông"
        4 => 9, // "Liên cấp 1-2",
        5 => 12, // "Liên cấp 2-3",
        6 => 18, // "Mầm non",
        7 => 12 // "TTGD Thường Xuyên"
    ];

    public $table = 'class';
    protected $guarded = [];

    const DYNAMIC_REPORT_CRITERIA = [
        'tonghocsinh' =>['Tổng số học sinh',  ['students','id', '>', 0]],
        'hocsinhnu' => ['Số học sinh nữ', ['students','gender', '=', 'Nữ']],
        'dantocthieuso' => ['Dân tộc thiểu số',['students','ethnic', '!=', 'Kinh']],
        'tongiao' => ['Tôn giáo',['students','religion', '!=', 'Không']],
        'khiemthinh' => ['Khiếm thính', ['students','disabilities', '=', 'Khiếm thính']],
        'khiemthi' => ['Khiếm thị', ['students','disabilities', '=', 'Khiếm thị']],
        'tuky' => ['Tự kỷ', [ 'students','disabilities', '=', 'Tự kỷ']],
        'khuyettatkhac' => ['Khuyết tật khác', ['students', 'disabilities', '=', 'Khuyết tật khác về sức khỏe']],
        'hongheo' => ['Hộ nghèo', [ 'students','fptp', '=', 'Hộ nghèo']],
        'hocanngheo' => ['Hộ cận nghèo', [ 'students','fptp', '=', 'Hộ cận nghèo']],
        'khongnoinuongtua' => ['Không nơi nương tựa', [ 'students','fptp', '=', 'Không nơi nương tựa']],
        'tremocoi' => [ 'Trẻ mồ côi', ['students','fptp', '=', 'Trẻ mồ côi']],
        'chinhsachkhac' => [ 'Loại khác', ['students','fptp', '=','Loại khác']],
        'sktot' => [ 'Tiền sử sức khoẻ: Tốt', ['students','health_history', '=', 'Tốt']],
        'skkhongtot' => [ 'Tiền sử sức khoẻ: Không Tốt', ['students','health_history', '=', 'Không tốt']],
        'sankhoabinhthuong' => [ 'Sản khoa: Bình thường', ['students','born_history', '=', 'Bình thường']],
        'sankhoadengat' => [ 'Sản khoa: Đẻ ngạt', ['students','born_history', '=', 'Đẻ ngạt']],
        'sankhoathieuthang' => [ 'Sản khoa: Đẻ thiếu tháng', ['students','born_history', '=', 'Đẻ thiếu tháng']],
        'sangkhoamebibenh' => [ 'Sản khoa: Mẹ bị bệnh trong kỳ mang thai', ['students','born_history', '=', 'Mẹ bị bệnh trong kỳ mang thai']],
        'sankhoadethuathang' => [ 'Sản khoa: Đẻ thừa tháng, đẻ có can thiệp', ['students','born_history', '=', 'Đẻ thừa tháng,Đẻ có can thiệp']],
        'benhhen' => [ 'Tiền sử bệnh tật: Hen', ['students','disease_history', '=', 'Hen']],
        'benhdongkinh' => [ 'Tiền sử bệnh tật: Động kinh', ['students','disease_history', '=', 'Động Kinh']],
        'benhdiung' => [ 'Tiền sử bệnh tật: Dị ứng', ['students','disease_history', '=', 'Dị ứng']],
        'benhtimbamsinh' => [ 'Tiền sử bệnh tật: Tim bẩm sinh', ['students','disease_history', '=', 'Tim bẩm sinh']],
        'datiembcg' => [ 'Tiêm chủng: đã tiêm mũi BCG', ['students','tc_bcg', '=', '1']],
        'chuatiembcg' => [ 'Tiêm chủng: chưa tiêm mũi BCG', ['students','tc_bcg', '=', '0']],
        'datiembhhguvm1' => [ 'Tiêm chủng: đã tiêm mũi 1 Ho gà bạch hầu, uấn ván', ['students','tc_bhhguv_m1', '=', '1']],
        'chuatiembhhguvm1' => [ 'Tiêm chủng: chưa tiêm mũi 1 Ho gà bạch hầu, uấn ván', ['students','tc_bhhguv_m1', '=', '0']],
        'datiembhhguvm2' => [ 'Tiêm chủng: đã tiêm mũi 2 Ho gà bạch hầu, uấn ván', ['students','tc_bhhguv_m2', '=', '1']],
        'chuatiembhhguvm2' => [ 'Tiêm chủng: chưa tiêm mũi 2 Ho gà bạch hầu, uấn ván', ['students','tc_bhhguv_m2', '=', '0']],
        'datiembhhguvm3' => [ 'Tiêm chủng: đã tiêm mũi 3 Ho gà bạch hầu, uấn ván', ['students','tc_bhhguv_m3', '=', '1']],
        'chuatiembhhguvm3' => [ 'Tiêm chủng: chưa tiêm mũi 3 Ho gà bạch hầu, uấn ván', ['students','tc_bhhguv_m3', '=', '0']],
        'datiembailietm1' => [ 'Tiêm chủng: đã tiêm mũi 1 Bại liệt', ['students','tc_bailiet_m1', '=', '1']],
        'chuatiembailietm1' => [ 'Tiêm chủng: chưa tiêm mũi 1 Bại liệt', ['students','tc_bailiet_m1', '=', '0']],
        'datiembailietm2' => [ 'Tiêm chủng: đã tiêm mũi 2 Bại liệt', ['students','tc_bailiet_m2', '=', '1']],
        'chuatiembailietm2' => [ 'Tiêm chủng: chưa tiêm mũi 2 Bại liệt', ['students','tc_bailiet_m2', '=', '0']],
        'datiembailietm3' => [ 'Tiêm chủng: đã tiêm mũi 3 Bại liệt', ['students','tc_bailiet_m3', '=', '1']],
        'chuatiembailietm3' => [ 'Tiêm chủng: chưa tiêm mũi 3 Bại liệt', ['students','tc_bailiet_m3', '=', '0']],
        'datiemviemganbm1' => [ 'Tiêm chủng: đã tiêm mũi 1 Viêm gan B', ['students','tc_viemganb_m1', '=', '1']],
        'chuatiemviemganbm1' => [ 'Tiêm chủng: chưa tiêm mũi 1 Viêm gan B', ['students','tc_viemganb_m1', '=', '0']],
        'datiemviemganbm2' => [ 'Tiêm chủng: đã tiêm mũi 2 Viêm gan B', ['students','tc_viemganb_m2', '=', '1']],
        'chuatiemviemganbm2' => [ 'Tiêm chủng: chưa tiêm mũi 2 Viêm gan B', ['students','tc_viemganb_m2', '=', '0']],
        'datiemviemganbm3' => [ 'Tiêm chủng: đã tiêm mũi 3 Viêm gan B', ['students','tc_viemganb_m3', '=', '1']],
        'chuatiemviemganbm3' => [ 'Tiêm chủng: chưa tiêm mũi 3 Viêm gan B', ['students','tc_viemganb_m3', '=', '0']],
        'datiemsoi' => [ 'Tiêm chủng: đã tiêm mũi Sởi', ['students','tc_soi', '=', '1']],
        'chuatiemsoi' => [ 'Tiêm chủng: chưa tiêm mũi Sởi', ['students','tc_soi', '=', '0']],
        'datiemviemnaonb' => [ 'Tiêm chủng: đã tiêm mũi Viêm não Nhật Bản', ['students','tc_viemnaonb', '=', '1']],
        'chuatiemviemnaonb' => [ 'Tiêm chủng: chưa tiêm mũi Viêm não Nhật Bản', ['students','tc_viemnaonb', '=', '0']],
        'cknhi_socamac' => [ 'Chuyên khoa Nhi: số lượng mắc', ['students.specialistTests', 'nhi_macbenh', '=', '1']],
        'cknhi_dangdieutri' => [ 'Chuyên khoa Nhi: số lượng đang điều trị', ['students.specialistTests', 'nhi_duocdieutri', '=', '1']],
        'ckmat_socamac' => [ 'Chuyên khoa Mắt: số lượng mắc', ['students.specialistTests', 'mat_macbenh', '=', '1']],
        'ckmat_dangdieutri' => [ 'Chuyên khoa Mắt: số lượng đang điều trị', ['students.specialistTests', 'mat_duocdieutri', '=', '1']],
        'cktmh_socamac' => [ 'Tai mũi họng: số lượng mắc', ['students.specialistTests', 'tmh_macbenh', '=', '1']],
        'cktmh_dangdieutri' => [ 'Tai mũi họng: số lượng đang điều trị', ['students.specialistTests', 'tmh_duocdieutri', '=', '1']],
        'ckrhm_socamac' => [ 'Răng hàm mặt: số lượng mắc', ['students.specialistTests', 'rhm_macbenh', '=', '1']],
        'ckrhm_dangdieutri' => [ 'Răng hàm mặt: số lượng đang điều trị', ['students.specialistTests', 'rhm_duocdieutri', '=', '1']],
        'ckcxk_socamac' => [ 'Cơ xương khớp: số lượng mắc', ['students.specialistTests', 'cxk_macbenh', '=', '1']],
        'ckcxk_dangdieutri' => [ 'Cơ xương khớp: số lượng đang điều trị', ['students.specialistTests', 'cxk_duocdieutri', '=', '1']],
        'baohiem_tunguyen' => [ 'Bảo hiểm: số lượng tham gia hình thức Tự nguyện', ['students.insurance', 'form_type', '=', 'Tự nguyện']],//'1']],
        'baohiem_chinhsach' => [ 'Bảo hiểm: số lượng tham gia hình thức Diện chính sách', ['students.insurance', 'form_type', '=', 'Chính Sách']],//'2']],
        'baohiem_dong3thang' => [ 'Bảo hiểm: số lượng đóng theo thời hạn 3 tháng', ['students.insurance', 'term_type', '=', '3 tháng']],//'1']],
        'baohiem_dong6thang' => [ 'Bảo hiểm: số lượng đóng theo thời hạn 6 tháng', ['students.insurance', 'term_type', '=', '6 tháng']],//'2']],
        'baohiem_dong9thang' => [ 'Bảo hiểm: số lượng đóng theo thời hạn 9 tháng', ['students.insurance', 'term_type', '=', '9 tháng']],//'3']],
        'baohiem_dong12thang' => [ 'Bảo hiểm: số lượng đóng theo thời hạn 12 tháng', ['students.insurance', 'term_type', '=', '12 tháng']],//'4']]
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            
        });
        self::updated(function ($model) {

        });

        self::deleted(function ($model) {
            
        });
    }
    
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function schoolBranch()
    {
        return $this->belongsTo(SchoolBranch::class, 'school_branch_id', 'id');
    }

    public function getGrade()
    {
        return $this->grade > 0 ? self::GRADES[$this->grade] : "";
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id', 'id');
    }

    public function teachers()
    {
        return $this->morphToMany(AdminUser::class, 'agency', 'user_agency', 'agency_id','user_id','id', 'id');
    }

    public function getReportByCriteria($name) {
        $condition = self::DYNAMIC_REPORT_CRITERIA[$name][1];
        if($condition[0] == 'students') {
            return collect($this->students)->where($condition[1], $condition[2], $condition[3])->count();
        } 
        if ($condition[0] == 'students.insurance') {
            return $this->students->reduce(function ($count, $student) use ($condition) {
                $match_condition = collect($student)->where($condition[1], $condition[2], $condition[3])->count() ? 1 : 0;
                return $count + $match_condition;
            }, 0);
        }
        else {
            return $this->students->reduce(function ($count, $student) use ($condition) {
                $relation = explode('.', $condition[0]);
                $match_condition = collect($student->{$relation[1]})->where($condition[1], $condition[2], $condition[3])->count() ? 1 : 0;
                return $count + $match_condition;
            }, 0);
        }
    }

    public static function getClasses($class_id){
        if(Admin::user()->inRoles(['giao-vien'])) { 
            $classes = Admin::user()->classes;
            if(!$class_id){
                $class_id = $classes[0]->id; 
            } else{
                $selectedClass = SchoolClass::find($class_id);
                foreach ($classes as $class){
                    if($selectedClass->id == $class->id){
                        return [$classes, $class_id];
                    }
                }
            }
            return [ $classes, $classes[0]->id ?? null];
        }
        return [ [], $class_id];
        
    }

    public function updateStudentsBranch(){
        $students = $this->students;
        foreach($students as $student) {
            if($student->school_branch_id != $this->school_branch_id){
                $student->update(['school_branch_id' => $this->school_branch_id]);
            }
        }
    }

    public static function checkingClasses($classes) {
        foreach($classes as $class) {
            $class->updateStudentsBranch();
        }
    }

    public function homeroomTeacher() {
        return $this->belongsTo(SchoolStaff::class, 'homeroom_teacher', 'id');
    }

    public function classSubjects() {
        return $this->hasMany(ClassSubject::class, 'class_id', 'id');
    }

    public function subjects() {
        return $this->hasManyThrough(Subject::class, ClassSubject::class, 'class_id', 'id', 'id', 'subject_id');
    }
}
