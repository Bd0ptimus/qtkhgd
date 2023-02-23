<?php

namespace App\Admin\Models\Imports;

use App\Admin\Helpers\Utils;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;

class ImportStudent extends BaseImport
{
    protected static $heading = [
        0 => "ho_va_ten",
        1 => "ngay_sinh",
        2 => "gioi_tinh",
        3 => "dantoc",
        4 => "ton_giao",
        5 => "quoc_tich",
        6 => "dia_chi",
        7 => "ho_ten_bo",
        8 => "so_dien_thoai_bo",
        9 => "email_bo",
        10 => "ho_ten_me",
        11 => "so_dien_thoai_me",
        12 => "email_me",
        13 => "khoi",
        14 => "lop_hoc",
        15 => "dang_khuyet_tat",
        16 => "dien_chinh_sach",
        17 => "con_thu",
        18 => "tong_so_con",
        19 => "tien_su_suc_khoe",
        20 => "san_khoa",
        21 => "tien_su_benh_tat",
        22 => "benh_dang_dieu_tri",
    ];

    protected static $arrFields = [
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
    ];

    protected static $mappingHeader = [
        "ho_va_ten" => "fullname",
        "ngay_sinh" => "dob",
        "gioi_tinh" => "gender",
        "dantoc" => "ethnic",
        "ton_giao" => "religion",
        "quoc_tich" => "nationality",
        "dia_chi" => "address",
        "ho_ten_bo" => "father_name",
        "so_dien_thoai_bo" => "father_phone",
        "email_bo" => "father_email",
        "ho_ten_me" => "mother_name",
        "so_dien_thoai_me" => "mother_phone",
        "email_me" => "mother_email",
        "khoi" => "grade",
        "lop_hoc" => "class_name",
        "dang_khuyet_tat" => "disabilities",
        "dien_chinh_sach" => "fptp",
        "con_thu" => "child_no",
        "tong_so_con" => "total_childs",
        "tien_su_suc_khoe" => "health_history",
        "san_khoa" => "born_history",
        "tien_su_benh_tat" => "disease_history",
        "benh_dang_dieu_tri" => "treating_disease",
    ];

    protected static $fieldHasDefaults = [
        'ethnic' => 1, 'religion' => 0, 'nationality' => 1, 'treating_disease' => 0,
    ];

    protected static $validator = [
        'rules' => [
            '*.fullname' => "required",
            '*.dob' => "required",
            '*.gender' => "required",
            '*.grade' => "required",
            '*.class_name' => 'required'
        ],
        'messages' => [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng',
            'required_without' => 'Giá trị tại dòng :attribute không được bỏ trống',
        ]
    ];

    public static function buildFullData($rows, $school)
    {
        $newRows = [];
        $defaultBranch = $school->getDefaultBranch();
        $lastedStudentCode = $school->getLastestStudentCode();
        $now = Carbon::now();
        foreach ($rows as $index => $row) {
            $class_name = mb_strtoupper($row['class_name']);
            $class = SchoolClass::where('school_id', $school->id)->where('class_name', $class_name)->first();
            if (!$class) $class = SchoolClass::create([
                'school_id' => $school->id,
                'grade' => $row['grade'],
                'class_name' => $class_name,
                'school_branch_id' => $defaultBranch ? $defaultBranch->id : null
            ]);
            $row['class_id'] = $class->id;
            $row['school_id'] = $school->id;
            $row['school_branch_id'] = $class->school_branch_id;

            $no = $lastedStudentCode + $index + 1;
            $row['student_code'] = $school->generateStudentCode($no);

            //Save all date format
            $dob = $row['dob'];
            $row['dob'] = Utils::formatDate($dob);
            $row['created_at'] = $now;
            $row['updated_at'] = $now;

            unset($row['class_name']);
            /* foreach(static::$fieldHasDefaults as $field) {
                if(!isset($row[$field])) $row[$field] = null;
            } */
            $newRows[] = $row;
        }
        return $newRows;
    }

    public static function buildFullDataSmas($rows, $school) {
        $newRows = [];
        $defaultBranch = $school->getDefaultBranch();
        $lastedStudentCode = $school->getLastestStudentCode();
        $now = Carbon::now();
       
        foreach ($rows as $index => $row) {
            if(!empty($row)) {
                if(!isset($row['ho_va_ten_hoc_sinh']) && !isset($row['ho_va_ten']) && !isset($row['ho_va_ten_tre'])) { dd($row); return false;} 
                $class_name = mb_strtoupper($row['lop']);
                $class = SchoolClass::where('school_id', $school->id)->where('class_name', $class_name)->first();
                if (!$class) $class = SchoolClass::create([
                    'school_id' => $school->id,
                    'grade' => 0,
                    'class_name' => $class_name,
                    'school_branch_id' => $defaultBranch ? $defaultBranch->id : null
                ]);
    
    
                $student['class_id'] = $class->id;
                $student['school_id'] = $school->id;
                $student['school_branch_id'] = $class->school_branch_id;
    
                $no = $lastedStudentCode + $index + 1;
                $student['student_code'] = $school->generateStudentCode($no);
    
                //Save all date format
                $dob = $row['ngay_sinh'];
                $student['dob'] = Utils::formatDate($dob);
                
                $student['created_at'] = $now;
                $student['updated_at'] = $now;
                $student['fullname'] = $row['ho_va_ten_hoc_sinh'] ?? $row['ho_va_ten'] ?? $row['ho_va_ten_tre'];
                
                $student['gender'] = $row['gioi_tinh']  == "Nam" ?  1 : 2;
                $student['address'] = $row['dia_chi_thuong_chu'] ?? $row['que_quan'] ?? ($row['thon_xom'] ?? '' )." - ". ($row['cho_o_hien_tai'] ?? '');
                $student['father_name'] = $row['ho_va_ten_cha'] ?? $row['ho_ten_cha'] ?? "";
                $student['father_phone'] = $row['so_dien_thoai_cua_cha'] ?? $row['so_dien_thoai_di_dong_cua_cha'] ?? '';
                $student['father_email'] = $row['email_cha'] ?? '';
                $student['mother_name'] = $row['ho_va_ten_me'] ?? $row['ho_ten_me'] ?? "";
                $student['mother_phone'] = $row['so_dien_thoai_cua_me'] ?? $row['so_dien_thoai_di_dong_cua_me'] ?? '';
                $student['mother_email'] = $row['email_me'] ?? '';
                $student['disabilities'] = self::getSmasDisability($row);
                $student['religion'] = self::getSmasReligion($row);
                $student['ethnic'] = self::getSmasEthnic($row);
                $newRows[] = $student;
            }
        }

        return $newRows;
    }

    public static function getSmasDisability($row) {
        switch($row['loai_khuyet_tat']) {
            case 'Khuyết tật khác':
                return 4; break;
            case 'Khuyết tật vận động':
                return 4; break;
            case 'Khuyết tật nghe nói':
                return 1; break;
            case 'Khuyết tật nhìn':
                return 2; break;
            case 'Khuyết tật trí tuệ':
                return 3; break;
            case 'Khuyết tật thần kinh tâm thần':
                return 4; break;
            default:
            return 0; break;
        }
    }

    public static function getSmasReligion($row) { //ton_giao
        if(!isset($row['ton_giao'])) return 0;
        switch($row['ton_giao']) {
            case 'Không': 
                return 0; break;
            case "Ba Ha'i":  
                return 13; break;
            case 'Bửu sơn Kỳ Hương': 
                return 11; break;
            case 'Cao Đài':  
                return 5; break;
            case 'Công giáo':  
                return 2; break;
            case 'Hồi giáo':  
                return 6; break;
            case 'Minh Lý đạo': 
                return 16; break;
            case 'Minh sư đạo': 
                return 15; break;
            case 'Phật giáo':  
                return 1; break;
            case 'Phật giáo Hòa Hảo': 
                return 3; break;
            case 'Tin Lành': 
                return 4; break;
            case 'Tôn giáo khác':  
                return 18; break;
            case 'Tịnh độ cư sĩ Phật hồi Việt Nam': 
                return 12; break;
            case 'Đạo tứ ấn hiếu nghĩa': 
                return 17; break;
        }
    }

    public static function getSmasEthnic($row) {  //dan_toc
        foreach(Student::ETHNICS as $key => $value) {
            if($value == $row['dan_toc'] ) return $key;
        }
        return 1;
    }
 }