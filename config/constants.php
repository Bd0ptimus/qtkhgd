<?php
    const ROLE_ADMIN = 'administrator';
    const ROLE_CM = 'customer-support';
    const ROLE_SO_GD = 'so-gd';
    const ROLE_PHONG_GD = 'phong-gd';
    const ROLE_CV_PHONG = 'chuyen-vien-phong';
    const ROLE_HIEU_TRUONG = 'hieu-truong';
    const ROLE_SCHOOL_MANAGER = 'school-manager';
    const ROLE_TO_TRUONG = 'to-truong';
    const ROLE_GIAO_VIEN = 'giao-vien';
    const ROLE_CONG_TAC_VIEN = 'cong-tac-vien';

    const ROLE_VALUE = [
        'administrator' => 'Admin',
        'customer-support' => 'Hỗ trợ viên',
        'so-gd' => 'Sở GD',
        'phong-gd' => 'Phòng GD',
        'chuyen-vien-phong' => 'Chuyên viên phòng',
        'hieu-truong' => 'Hiệu trưởng',
        'school-manager' => 'QL trường',
        'to-truong' => 'Tổ trưởng',
        'giao-vien' => 'Giáo viên',
        'cong-tac-vien' => 'Cộng tác viên',
    ];

    const USER_ACTIVE = [
        '<span class="text-primary">Đã kích hoạt</span>',
        '<span class="text-danger">Chưa kích hoạt</span>'
    ];

    const ROLE_ADMIN_ID = 1;
    const ROLE_CM_ID = 2;
    const ROLE_SO_GD_ID = 3;
    const ROLE_PHONG_GD_ID = 4;
    const ROLE_CV_PHONG_ID = 5;
    const ROLE_HIEU_TRUONG_ID = 6;
    const ROLE_GIAO_VIEN_ID = 7;
    const ROLE_TO_TRUONG_ID = 8;
    const ROLE_SCHOOL_MANAGER_ID = 9;
    const ROLE_CONG_TAC_VIEN_ID  = 10;

    const SCHOOL_TH = 1; // tiểu học
    const SCHOOL_THCS = 2; // trung học cơ sở
    const SCHOOL_THPT = 3; // trung học phổ thông
    const SCHOOL_LC12 = 4; //Cap liên cấp 1-2
    const SCHOOL_LC23 = 5; //Cap liên cấp 2-3
    const SCHOOL_MN = 6; // mầm non
    const SCHOOL_GDTX = 7; //GDTX - giáo dục thuờng xuyên

    const GROUP_LEADER = 1; //Tổ trưởng
    const GROUP_DEPUTY = 2; //Tổ phó
    const GROUP_MEMBER = 3; //Thành viên

    const PLAN_PENDING = 0;
    const PLAN_SUBMITTED = 1;
    const PLAN_INREVIEW = 2;
    const PLAN_APPROVED = 10;

    const GROUP_ROLES = [
        1 => 'Tổ trưởng', 
        2 => 'Tổ phó', 
        3 => 'Thành viên'
    ];

    const BOOK_ASSEMBLAGES = ['Cánh diều', 'Chân trời sáng tạo', 'Kết nối tri thức'];

    const PRIORITY = [
        'Thấp' => 'low',
        'Bình thuờng' => 'normal',
        'Cao' => 'high',
        'Khẩn cấp' => 'urgent',
    ];

    const PRIORITY_VALUE = [
        'low' => 'Thấp',
        'normal' => 'Bình thuờng',
        'high' => 'Cao',
        'urgent' => 'Khẩn cấp',
    ];

    const PRIORITY_BG = [
        'low' => 'default',
        'normal' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ];

    const GRADES = [
        13 => '3-12 tháng', 14 => '13-24 tháng', 15 => '25-36 tháng',
        16 => '3-4 tuổi', 17 => '4-5 tuổi', 18 => '5-6 tuổi',
        1 => 'Khối 1', 2 => 'Khối 2', 3 => 'Khối 3', 4 => 'Khối 4', 5 => 'Khối 5',
        6 => 'Khối 6', 7 => 'Khối 7', 8 => 'Khối 8', 9 => 'Khối 9',
        10 => 'Khối 10', 11 => 'Khối 11', 12 => 'Khối 12', 0 => 'Chưa xác định'
    ];

    const PLAN_STATUSES = [
        0 => 'Đang soạn thảo',
        1 => 'Đã gửi kế hoạch, đang chờ duyệt',
        2 => 'Đang kiểm duyệt',
        10 => 'Đã được kiểm duyệt'
    ];

    const SCHOOL_TYPES = [
        1 => "Tiểu học",
        2 => "Trung học cơ sở",
        3 => "Trung học phổ thông",
        6 => "Mầm non",
    ];

    const DATETIME_SHORT_FORMAT = 'd/m/Y';
    const DATETIME_LONG_FORMAT = 'd/m/Y H:i:s';

    const TARGET_TYPES = [
        1 => 'Chỉ tiêu đào tạo',
        2 => 'Chỉ tiêu xây dựng trường học & cơ sở vật chất',
        3 => 'Chỉ tiêu nguồn nhân lực',
        4 => 'Chỉ tiêu chuyển đổi số',
        5 => 'Chi tiêu y tế học đường'
    ];

    const SYSTEM_MODULES = [
        1 => [
            "name" => 'Quản lý nhà trường',
            "value" => 'school_management',
            "image" => 'https://mamnon.edusmart.net.vn/themes/images/logo.png',
            "isActive" => false,
            "functions"=> [
                "Đội ngũ nhân viên" => 'inactive', 
                "Cơ sở vật chất" => 'inactive', 
                "Dạy học" => 'inactive'
            ]
        ],
        2 => [
            "name" => 'Quản lý tổ chức Đoàn thể',
            "value" => 'organization_management',
            "image" => 'https://mamnon.edusmart.net.vn/themes/images/logo.png',
            "isActive" => false,
            "functions" => [
                "Công đoàn (đội)" => 'inactive', 
                "Đoàn thanh niên" => 'inactive', 
                "Khuyến học" => 'inactive',
                "Thanh tra nhân dân" => 'inactive',
                "Chữ thập đỏ" => 'inactive'
            ]
        ],
        3 => [
            "name" => 'Quản lý dạy học',
            "value" => 'teaching_management',
            "image" => 'https://mamnon.edusmart.net.vn/themes/images/logo.png',
            "isActive" => true,
            "functions"=> [
                "Thời khóa biểu" => 'active', 
                "Trang thiết bị" => 'active', 
                "Đội ngũ giáo viên" => 'active',
                "Kế hoạch giảng dạy" => 'active',
                "Phụ huynh" => 'active',
                "Học sinh" => 'active'
            ]
        ],
        4 => [
            "name" => 'Chuyển đổi số',
            "value" => 'digital_trans_management',
            "image" => 'https://mamnon.edusmart.net.vn/themes/images/logo.png',
            "isActive" => false,
            "functions" => [
                "Chỉ số hạ tầng" => 'inactive',
                "Nền tảng số và an toàn thông tin" => 'inactive',
                "Phát triển nhân lực số" => 'inactive',
                "Nguồn lực" => 'inactive',
                "Xây dựng thể chế" => 'inactive',
                "Hoạt động quản trị số" => 'inactive',
                "Hoạt động dạy học số" => 'inactive',
                "Hoạt động dịch vụ số học đường" => 'inactive',
            ]
        ],
        5 => [
            "name" => 'Hồ sơ trường học',
            "value" => 'school_profile_management',
            "image" => 'https://mamnon.edusmart.net.vn/themes/images/logo.png',
            "isActive" => false,
            "functions" => [
                "Sổ đăng bộ" => 'inactive',
                "Sổ phổ cập giáo dục tiểu học" => 'inactive',
                "Sổ theo dõi kết quả kiểm tra, đánh giá học sinh" => 'inactive',
                "Học bạ của học sinh" => 'inactive',
                "Sổ nghị quyết" => 'inactive',
                "Sổ kế hoạch công tác",
                "Sổ quản lí cán bộ, giáo viên, nhân viên" => 'inactive',
                "Sổ khen thưởng, kỉ luật" => 'inactive',
                "Sổ sinh hoạt chuyên môn" => 'inactive',
                "Sổ kế hoạch giảng dạy" => 'inactive',
                "Giáo án (bài soạn)" => 'inactive',
                "Bảng tổng hợp kết quả đánh giá giáo dục" => 'inactive',
                "Sổ ghi chép sinh hoạt chuyên môn và dự giờ" => 'inactive',
                "Sổ chủ nhiệm" => 'inactive'
            ]
        ]
    ];

    const ACCOUNT_TYPE = [
        0 => 'Account Default',
        1 => 'Account Demo',
    ];

    return array(
        'password_reset' => '123123123',
        'email_system' => env('MAIL_USERNAME', ''),
    );
?>
