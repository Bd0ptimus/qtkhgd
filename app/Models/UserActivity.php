<?php 
namespace App\Models;

use App\Models\Base\BaseModel as Model;
use App\Admin\Models\AdminUser;
use App\Models\School;
use App\Models\SchoolBranch;
use App\Scopes\YearScope;

class UserActivity extends Model
{
    public $table           = 'user_activities';
    protected $guarded      = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $now = date('Y-m-d H:i:s', time());
            $endSchoolYear = date('Y-m-d H:i:s', strtotime(\App\Admin\Helpers\ListHelper::listYear()[1]."-06-30 23:59:59"));
            $model->created_at = $now > $endSchoolYear ? $endSchoolYear : $now;
        });
        static::addGlobalScope(new YearScope);
    }
    
    private static $getList = null;

    const ACTIVITIES = [
    /* School Medical */
        /* Sức khoẻ học sinh */
        'student_health_periodical_check' => 'Xem trang Theo dõi sức khỏe định kỳ học sinh',
        //'student_health_periodical_check_import' => 'Xem trang Import excel - Theo dõi sức khỏe định kỳ học sinh',
        'student_periodical_check_download_demo' => 'Tải File mẫu về máy - Theo dõi sức khỏe định kỳ học sinh',
        'student_health_periodical_check_post_import' => 'Import Theo dõi sức khỏe định kỳ học sinh',

        'student_health_profile' => 'Xem trang Hồ sơ sức khỏe học sinh',
        'student_health_abnormals' => 'Xem trang Theo dõi diễn biến bất thường sức khỏe học sinh',
        'student_health_abnormals_edit' => 'Sửa thông tin Theo dõi diễn biến bất thường sức khỏe học sinh',
        'student_health_abnormals_post_import' => 'Import Theo dõi diễn biến bất thường sức khỏe học sinh',
        'student_health_abnormals_delete' => 'Xóa Theo dõi diễn biến bất thường sức khỏe học sinh',
        'student_health_specialist_test' => 'Xem trang Khám chuyên khoa học sinh',
        'student_health_create_specialist_test' => 'Tạo mới Khám chuyên khoa học sinh',
        'student_health_edit_specialist_test' => 'Chỉnh sửa Khám chuyên khoa học sinh',
        'student_health_delete_specialist_test' => 'Xóa Khám chuyên khoa học sinh',
        'student_health_profile_export_student' => 'Xuất báo cáo Hồ sơ sức khỏe học sinh',
        'student_bieudophattrien' => 'Biểu đồ phát triển - Hồ sơ sức khỏe học sinh',
        'student_health_abnormals_post_edit' => 'Chỉnh sửa Theo dõi diễn biến bất thường sức khỏe học sinh',
        /* Bảo hiểm y Tế */
        'admin.school_medical.view_insurance_list' => 'Xem trang Danh sách bảo hiểm y tế học sinh',
        'admin.school_medical.delete_insurance' => 'Xóa Bảo hiểm y tế học sinh',
        'admin.school_medical.post_edit_insurance' => 'Chỉnh sửa Bảo hiểm y tế học sinh',
        
        /* Sức khoẻ giáo viên */
        'staff_health_specialist_test' => 'Xem trang Khám chuyên khoa nhân viên',
        'staff_health_specialist_test_export' => 'Export Khám chuyên khoa nhân viên',
        'staff_health_edit_specialist_test' => 'Chỉnh sửa Khám chuyên khoa nhân viên',
        'staff_health_delete_specialist_test' => 'Xóa Khám chuyên khoa nhân viên',
        'staff_health_abnormals' => 'Xem trang Theo dõi diễn biến bất thường sức khỏe nhân viên',
        'staff_health_abnormals_export' => 'Export Theo dõi diễn biến bất thường sức khỏe nhân viên',
        'staff_delete_health_abnormals' => 'Xóa Theo dõi diễn biến bất thường sức khỏe nhân viên',
        'staff_health_abnormals_post_edit' => 'Chỉnh sửa Theo dõi diễn biến bất thường sức khỏe nhân viên',
        /* Quản lý thuốc */
        'school_medical_local_medicines' => 'Xem trang Thuốc tại trường',
        'school_medical_config_medicines' => 'Xem trang Cấu hình danh mục thuốc',
        'school_medical_manage' => 'Xem trang Theo dõi thuốc',
        'school_medical_import' => 'Nhập thuốc',
        'school_medical_export' => 'Xuất thuốc',
        /* Quản lý thiết bị y tế */
        'school_config_medical_equipments' => 'Xem trang Cấu hình thiết bị y tế',
        'school_local_medical_equipments' => 'Xem trang Thiết bị y tế tại trường',
        'school_medical_equipments_manage' => 'Xem trang Theo dõi thiết bị y tế tại trường',
        'school_medical_equipments_import' => 'Nhập thiết bị y tế tại trường',
        'school_medical_equipments_export' => 'Xuất thiết bị y tế tại trường',
        /** Bảo hiểm y tế */
        'admin.school_medical.export_excel' => 'Export Danh sách bảo hiểm y tế  học sinh',
        'admin.school_medical.download_demo_insurance' => 'Tải file exel mẫu Danh sách bảo hiểm y tế học sinh',
        'admin.school_medical.post_import_insurance' => 'Import Danh sách bảo hiểm y tế học sinh',
        'admin.school_medical.post_mass_edit_insurance' => 'Chỉnh sửa Bảo hiểm y tế học sinh',
        /* Vệ sinh học đường */
        'school_sanitation_config' => 'Cấu hình thông tin vệ sinh học đường',
        'school_sanitation_check' => 'Đánh giá vệ sinh học đường',
        /* Report school medical */
        'admin.school.report.pl02' => 'Xem trang Báo cáo PL02',
        'admin.school.report.pl02.create' => 'Tạo mới Báo cáo PL02',
        'admin.school.report.pl02.export' => 'Export Báo cáo PL02',
        'admin.school.report.pl02.edit' => 'Chỉnh sửa Báo cáo PL02',
        'admin.school.report.pl03' => 'Xem trang Báo cáo PL03',
        'admin.school.report.pl03.create' => 'Tạo mới Báo cáo PL03',
        'admin.school.report.pl03.export' => 'Export Báo cáo PL03',
        'admin.school.report.pl03.edit' => 'Chỉnh sửa Báo cáo PL03',
        'admin.school.report.pl04' => 'Xem trang Báo cáo PL04',
        'admin.school.report.pl04.create' => 'Tạo mới Báo cáo PL04',
        'admin.school.report.pl04.export' => 'Export Báo cáo PL04',
        'admin.school.report.pl04.edit' => 'Chỉnh sửa Báo cáo PL04',
        /* Quản lý thực phẩm tại trường */
        'school.food_management.food.index' => 'Xem trang Quản lý thực phẩm tại trường',
        'school.food_management.food.create' => 'Tạo mới Thực phẩm tại trường',
        'school.food_management.food.edit' => 'Chỉnh sửa Thực phẩm tại trường',
        'school.food_management.food.delete' => 'Xóa Thực phẩm tại trường',
        /* Quản lý món ăn tại trường */
        'school.food_management.dish.index' => 'Xem trang Quản lý món ăn tại trường',
        'school.food_management.dish.create' => 'Tạo mới Món ăn tại trường',
        'school.food_management.dish.edit' => 'Chỉnh sửa Món ăn tại trường',
        'school.food_management.dish.delete' => 'Xóa Món ăn tại trường',
        /* Đơn vị cung cấp thực phẩm */
        'school.food_provider.index' => 'Xem trang Quản lý nhà cung cấp tại trường',
        'school.food_provider.store' => 'Tạo mới Nhà cung cấp tại trường',
        'school.food_provider.update' => 'Chỉnh sửa Nhà cung cấp tại trường',
        /* An toàn thực phẩm */
        'school.food_inspection.index' => 'Xem trang Quản lý An toàn thực phẩm',
        'school.food_inspection.export' => 'Export Quản lý An toàn thực phẩm',
        'school.food_inspection.create' => 'Tạo mới An toàn thực phẩm',
        'school.food_inspection.delete' => 'Xóa An toàn thực phẩm',
        'school.food_inspection.edit' => 'Chỉnh sửa An toàn thực phẩm',
        /* Theo dõi thực phẩm */
        'school.food_tracking.food_sample.index' => 'Xem trang Quản lý lưu và hủy mẫu thức ăn tại trường',
        'school.food_tracking.food_sample.create' => 'Tạo mới Mẫu thức ăn tại trường',
        'school.food_tracking.food_sample.edit' => 'Chỉnh sửa Mẫu thức ăn tại trường',
        'school.food_tracking.food_sample.delete' => 'Xóa Mẫu thức ăn tại trường',
        'school.food_tracking.food_entry.index' => 'Xem trang Theo dõi nhập thực phẩm tại trường',
        'school.food_tracking.food_entry.create' => 'Tạo mới Theo dõi nhập thực phẩm tại trường',
        'school.food_tracking.food_entry.edit' => 'Chỉnh sửa Theo dõi nhập thực phẩm tại trường',
        'school.food_tracking.food_entry.delete' => 'Xóa Theo dõi nhập thực phẩm tại trường',
        'school.food_tracking.copy_data' => 'Sao chép dữ liệu An toàn thực phẩm',
        /* Cơ sở vật chất */
        'csvc.check_room.index' => 'Xem trang Đánh giá phòng học và chiếu sáng',
        'csvc.check_room.create' => 'Tạo mới Đánh giá phòng học và chiếu sáng',
        'csvc.check_room.edit' => 'Chỉnh sửa Đánh giá phòng học và chiếu sáng',
        'csvc.check_room.delete' => 'Xóa Đánh giá phòng học và chiếu sáng',
        'csvc.check_furniture.index' => 'Xem trang Đánh giá bàn ghế',
        'csvc.check_furniture.create' => 'Tạo mới Đánh giá bàn ghế',
        'csvc.check_furniture.edit' => 'Chỉnh sửa Đánh giá bàn ghế',
        'csvc.check_furniture.delete' => 'Xóa Đánh giá bàn ghế',
        /* Báo cáo */
        'report_tonghopkiemtrasuckhoetheothang' => 'Xem trang Báo cáo Tổng hợp kiếm tra sức khoẻ theo tháng',
        'report_sotheodoitonghopskhocsinh' => 'Xem trang Báo cáo Số theo dõi tổng hợp sức khỏe học sinh',
        'report_tonghoptheodoisuckhoebatthuong' => 'Xem trang Báo cáo Tổng hợp theo dõi sức khỏe bất thường',
        'report_tonghoptheodoisuckhoebatthuong_export' => 'Export Tổng hợp theo dõi sức khỏe bất thường',
        'report_quanlydichbenh' => 'Xem trang Báo cáo Quản lý dịch bệnh',
        'report_baocaodong' => 'Xem trang Báo cáo động',
        /* Báo cáo cấp Phòng */ 
        'district.report.pl02' => 'Xem trang Báo cáo cấp phòng PL02',
        'district.report.pl03' => 'Xem trang Báo cáo cấp phòng PL03',
        'district.report.pl04' => 'Xem trang Báo cáo cấp phòng PL04',
    /* School */
        /* School Branch and User */
        'school.index' => 'Xem trang Danh sách các đơn vị trường học',
        'admin.school.delete_school' => 'Xóa trường',
        'admin.school.manage' => 'Xem trang Quản lý trường',
        'admin.school.users' => 'Xem trang danh sách người dùng', 
        'admin.school.users.assign_teacher' => 'Thêm Tài khoản giáo viên',
        'admin.school.view_branch_list' => 'Xem trang Danh sách các điểm trường',
        'admin.school.post_add_branch' => 'Them mới Điểm trường',
        'admin.school.post_edit_branch' => 'Chỉnh sửa Điểm trường',
        'admin.school.delete_branch' => 'Xóa Điểm trường',
        'admin.school.add_user' => 'Thêm mới Tài khoản trường',
        'school.create_parent_account' => 'Thêm mới tài khoản phụ huynh',
        /* Staff  */
        //'admin.school.view_staff_list' => 'Xem trang Danh sách nhân viên',
        //'admin.school.post_add_staff' => 'Thêm mới Nhân viên tại trường',
        //'admin.school.post_edit_staff' => 'Chỉnh sửa Nhân viên tại trường',
        //'admin.school.delete_staff' => 'Xóa Nhân viên tại trường',
        //'admin.school.import_staff' => 'Import Nhân viên tại trường',
        'admin.school.staff.assign_branch' => 'Chuyển nhân viên vào điểm trường',
        //'admin.school.staff.assign_class' => 'Chuyển giáo viên đứng lớp',
        /* Student */
        'admin.school.view_student_list' => 'Xem trang Danh sách học sinh',
        'admin.school.import_student' => 'Import Danh sách học sinh',
        'admin.school.export_student' => 'Export Danh sách học sinh',
        'admin.student.view' => 'Xem thông tin chi tiết của học sinh',
        'admin.student.edit' => 'Chỉnh sửa Thông tin học sinh',
        'admin.student.delete' => 'Xóa Thông tin học sinh',
        'admin.school.create_student' => 'Tạo mới Thông tin học sinh',
        'admin.school.assign_class_student' => 'Chỉ định lớp học cho học sinh',
        'admin_user.edit' => 'Chỉnh sửa thông tin tài khoản',
        'admin_user.reset_password' => 'Đặt lại mật khẩu tài khoản',

        'tasks.index' => 'Danh sách nhiệm vụ',
        'tasks.show' => 'Chi tiết nhiệm vụ',
        'tasks.store' => 'Tạo nhiệm vụ mới',
        'tasks.update' => 'Cập nhật nhiệm vụ',
        'tasks.destroy' => 'Xoá nhiệm vụ',
        'tasks.comment' => 'Bình luận nhiệm vụ',

    ];

    public function user(){
        return $this->belongsTo(AdminUser::class, 'user_id','id');
    }

    public function school(){
        return $this->belongsTo(School::class, 'school_id','id');
    }

    public function school_branch(){
        return $this->belongsTo(SchoolBranch::class, 'school_branch_id','id');
    }

    public function getCreatedAtAttribute($value) {
        if (!empty($value)) return date(DATETIME_LONG_FORMAT, strtotime($value)); 
    }

    public static function getSpecialActivity($routeName){
        switch($routeName){
            case 'school_config_medical_equipments':
                return 'Thêm mới Cấu hình thiết bị y tế';
            case 'school_local_medical_equipments':
                return 'Thêm mới Thiết bị y tế tại trường';
            case 'school_sanitation_config':
                return 'Thêm mới Cấu hình thông tin vệ sinh học đường';
            case 'school_sanitation_check':
                return 'Thêm mới Đánh giá vệ sinh học đường';
            case 'admin_user.edit':
                return 'Chỉnh sửa thông tin tài khoản';
            case 'admin_user.reset_password':
                return 'Đặt lại mật khẩu tài khoản';
            default:
                return null;
        }
    }
}
