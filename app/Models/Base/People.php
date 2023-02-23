<?php 
namespace App\Models\Base;

class People extends BaseModel
{
    const GENDER = [ 1 => 'Nam', 2 => 'Nữ', 0 => 'Chưa xác định'];
    const ETHNICS = [
        1 => 'Kinh', 2 => 'Tày', 3 => 'Thái', 4 => 'Mường', 5 => 'Khmer',
        6 => 'Hoa', 7 => 'Nùng', 8 => 'H\'Mông', 9 => 'Dao', 10 => 'Gia Rai',
        11 => 'Ê Đê', 12 => 'Ba Na', 13 => 'Sán Chay', 14 => 'Chăm', 15 => 'Cơ Ho',
        16 => 'Xơ Đăng', 17 => 'Sán Dìu', 18 => 'Hrê', 19 => 'Ra Glai', 20 => 'Mnông',
        21 => 'Thổ', 22 => 'Stiêng', 23 => 'Khơ mú', 24 => 'Bru - Vân Kiều', 25 => 'Cơ Tu',
        26 => 'Giáy', 27 => 'Tà Ôi', 28 => 'Mạ', 29 => 'Giẻ-Triêng', 30 => 'Co', 
        31 => 'Chơ Ro', 32 => 'Xinh Mun', 33 => 'Hà Nhì', 34 => 'Chu Ru', 35 => 'Lào',
        36 => 'La Chí', 37 => 'Kháng', 38 => 'Phù Lá', 39 => 'La Hủ', 40 => 'La Ha',
        41 => 'Pà Thẻn', 42 => 'Lự', 43 => 'Ngái', 44 => 'Chứt', 45 => 'Lô Lô',
        46 => 'Mảng', 47 => 'Cơ Lao', 48 => 'Bố Y', 49 => 'Cống', 50 => 'Si La',
        51 => 'Pu Péo', 52 => 'Rơ Măm', 53 => 'Brâu', 54 => 'Ơ Đu',
    ];
    const RELIGIONS = [
        0 => 'Không',
        1 => 'Phật giáo', 2 => 'Công giáo', 3 => 'Hòa Hảo', 4 => 'Tin Lành',
        5 => 'Cao Đài', 6 => 'Hồi Giáo', 7 => 'Bà La Môn', 8 => 'Đạo Tứ ấn hiếu nghĩa',
        9 => 'Cơ đốc Phục Lâm', 10 => 'Mormon', 11 => 'Bửu sơn Kỳ hương',
        12 => 'Tịnh độ cư sĩ Phật hội Việt Nam', 13 => 'Bahá\'í', 
        14 => 'Hiếu Nghĩa Tà Lơn', 15 => 'Minh Sư Đạo', 16 => 'Minh Lý Đạo', 17 => 'Đạo tứ ấn hiếu nghĩa', 18 => 'Tôn giáo khác'
    ];
    const NATIONALITIES = [
        0 => 'Khác', 1 => 'Việt Nam', 2 => 'Lào',
        3 => 'Campuchia', 4 => 'Thái Lan', 5 => 'Mỹ', 6 => 'Anh',
    ];
    
    public static function getGender($id) {
        return self::GENDER[$id];
    }

    public static function getEthnic($id) {
        return self::ETHNICS[$id];
    }

    public static function getReligion($id) {
        return self::RELIGIONS[$id];
    }

    public static function getNationality($id) {
        return self::RELIGIONS[$id];
    }

    public static function getQualification($id) {
        return self::QUALIFICATIONS[$id];
    }
}