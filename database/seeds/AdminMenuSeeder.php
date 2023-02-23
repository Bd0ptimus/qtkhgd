<?php

use App\Admin\Models\AdminMenu;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            //start-record
            $records = [
                "5" => [
                    "parent_id" => 0,
                    "sort" => 14,
                    "title" => "lang::admin.menu_titles.config_manager",
                    "icon" => "fa-cogs",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "34" => [
                    "parent_id" => 5,
                    "sort" => 2,
                    "title" => "lang::backup.admin.title",
                    "icon" => "fa-save",
                    "uri" => "admin::backup",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "38" => [
                    "parent_id" => 0,
                    "sort" => 13,
                    "title" => "lang::admin.menu_titles.admin",
                    "icon" => "fa-sitemap",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "47" => [
                    "parent_id" => 38,
                    "sort" => 2,
                    "title" => "lang::admin.menu_titles.roles",
                    "icon" => "fa-user",
                    "uri" => "admin::role",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "48" => [
                    "parent_id" => 38,
                    "sort" => 3,
                    "title" => "lang::admin.menu_titles.permission",
                    "icon" => "fa-ban",
                    "uri" => "admin::permission",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "49" => [
                    "parent_id" => 38,
                    "sort" => 4,
                    "title" => "lang::admin.menu_titles.menu",
                    "icon" => "fa-bars",
                    "uri" => "admin::menu",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "50" => [
                    "parent_id" => 38,
                    "sort" => 5,
                    "title" => "lang::admin.menu_titles.operation_log",
                    "icon" => "fa-history",
                    "uri" => "admin::log",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "53" => [
                    "parent_id" => 5,
                    "sort" => 1,
                    "title" => "lang::env.title",
                    "icon" => "fa-cog",
                    "uri" => "admin::env",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "58" => [
                    "parent_id" => 0,
                    "sort" => 12,
                    "title" => "Thông báo",
                    "icon" => "fa-bell",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["so-gd", "phong-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong"]
                ],
                "65" => [
                    "parent_id" => 58,
                    "sort" => 1,
                    "title" => "Thông báo chung",
                    "icon" => "fa-bars",
                    "uri" => "admin::notification",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "67" => [
                    "parent_id" => 0,
                    "sort" => 10,
                    "title" => "Cấu hình thông tin",
                    "icon" => "fa-upload",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "69" => [
                    "parent_id" => 0,
                    "sort" => 7,
                    "title" => "Dữ liệu địa chính",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "70" => [
                    "parent_id" => 67,
                    "sort" => 1,
                    "title" => "Thông tin giáo viên",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "admin::routing/admin.school.view_staff_list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "71" => [
                    "parent_id" => 67,
                    "sort" => 2,
                    "title" => "Thông tin học sinh",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "admin::routing/admin.school.view_student_list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "72" => [
                    "parent_id" => 0,
                    "sort" => 1,
                    "title" => "Trang chủ",
                    "icon" => "fa-home",
                    "uri" => "/",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "83" => [
                    "parent_id" => 67,
                    "sort" => 2,
                    "title" => "Tài khoản người dùng",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "admin::routing/admin.school.users",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "90" => [
                    "parent_id" => 69,
                    "sort" => 3,
                    "title" => "Sở GD",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency/provinces",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "91" => [
                    "parent_id" => 69,
                    "sort" => 2,
                    "title" => "Tổng quan",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "92" => [
                    "parent_id" => 69,
                    "sort" => 4,
                    "title" => "Phòng GD",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency/districts",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "93" => [
                    "parent_id" => 69,
                    "sort" => 5,
                    "title" => "Phường xã",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency/wards",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "110" => [
                    "parent_id" => 0,
                    "sort" => 2,
                    "title" => "Bàn làm việc",
                    "icon" => "fa-bars",
                    "uri" => "admin::school",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "giao-vien", "school-manager"]
                ],
                "112" => [
                    "parent_id" => 69,
                    "sort" => 1,
                    "title" => "Import Dữ Liệu Địa Chính",
                    "icon" => "fa-bars",
                    "uri" => "admin::sysconf/import-locations",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "137" => [
                    "parent_id" => 0,
                    "sort" => 11,
                    "title" => "Lịch sử hoạt động",
                    "icon" => "fa-history",
                    "uri" => "admin::routing/admin.school.user_activity",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "hieu-truong", "school-manager"]
                ],
                "146" => [
                    "parent_id" => 0,
                    "sort" => 9,
                    "title" => "Quản lý trường",
                    "icon" => "fa-building",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "147" => [
                    "parent_id" => 146,
                    "sort" => 1,
                    "title" => "Mầm non - Tiểu học - THCS - GDTX",
                    "icon" => "fa-bars",
                    "uri" => "admin::school/maugiao-tieuhoc-thcs",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "phong-gd"]
                ],
                "148" => [
                    "parent_id" => 146,
                    "sort" => 2,
                    "title" => "Trung học phổ thông",
                    "icon" => "fa-bars",
                    "uri" => "admin::school/thpt",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd"]
                ],
                "160" => [
                    "parent_id" => 0,
                    "sort" => 8,
                    "title" => "Quản lý các phòng",
                    "icon" => "fa-address-book",
                    "uri" => "admin::district",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "177" => [
                    "parent_id" => 0,
                    "sort" => 4,
                    "title" => "Việc cần làm",
                    "icon" => "fa-check",
                    "uri" => "admin::common/tasks",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "178" => [
                    "parent_id" => 0,
                    "sort" => 6,
                    "title" => "Cấu hình hệ thống",
                    "icon" => "fa-gear",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "179" => [
                    "parent_id" => 178,
                    "sort" => 1,
                    "title" => "Môn học",
                    "icon" => "fa-book",
                    "uri" => "admin::sysconf/subject",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "180" => [
                    "parent_id" => 178,
                    "sort" => 3,
                    "title" => "Tổ chuyên môn",
                    "icon" => "fa-bookmark",
                    "uri" => "admin::sysconf/regular-group",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "181" => [
                    "parent_id" => 38,
                    "sort" => 1,
                    "title" => "Users",
                    "icon" => "fa-bars",
                    "uri" => "admin::user",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "182" => [
                    "parent_id" => 178,
                    "sort" => 2,
                    "title" => "Môn học theo khối",
                    "icon" => "fa-bars",
                    "uri" => "admin::sysconf/subject/subject-by-grade",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "183" => [
                    "parent_id" => 0,
                    "sort" => 3,
                    "title" => "Bàn làm việc (PGD)",
                    "icon" => "fa-bars",
                    "uri" => "admin::district",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["phong-gd", "chuyen-vien-phong"]
                ],
                "184" => [
                    "parent_id" => 192,
                    "sort" => 3,
                    "title" => "Phiếu bài tập về nhà",
                    "icon" => "fa-book",
                    "uri" => "admin::common/homework-sheet/list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "185" => [
                    "parent_id" => 192,
                    "sort" => 4,
                    "title" => "Đề kiểm tra",
                    "icon" => "fa-bookmark",
                    "uri" => "admin::common/exercise-question/list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "186" => [
                    "parent_id" => 192,
                    "sort" => 1,
                    "title" => "Sách điện tử",
                    "icon" => "fa-book",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "187" => [
                    "parent_id" => 192,
                    "sort" => 2,
                    "title" => "Bài giảng mẫu",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/lesson-sample",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "188" => [
                    "parent_id" => 186,
                    "sort" => 2,
                    "title" => "Danh mục sách",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/ebook-categories",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "189" => [
                    "parent_id" => 186,
                    "sort" => 1,
                    "title" => "Thư viện sách",
                    "icon" => "fa-book",
                    "uri" => "admin::common/ebooks",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "190" => [
                    "parent_id" => 0,
                    "sort" => 15,
                    "title" => "Lịch bài giảng của giáo viên",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/teacher-weekly-lesson",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "giao-vien", "to-truong"]
                ],
                "191" => [
                    "parent_id" => 192,
                    "sort" => 6,
                    "title" => "Kho dữ liệu mô phỏng",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/simulator",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "school-manager", "cong-tac-vien"]
                ],
                "192" => [
                    "parent_id" => 0,
                    "sort" => 5,
                    "title" => "Dữ liệu mẫu",
                    "icon" => "fa-bars",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "giao-vien", "to-truong", "cong-tac-vien"]
                ],
                "193" => [
                    "parent_id" => 192,
                    "sort" => 5,
                    "title" => "Chỉ tiêu mẫu",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/target",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "school-manager", "cong-tac-vien"]
                ],
            ];
            //end-record
            AdminMenu::query()->truncate();
            foreach ($records as $id => $record) {
                /** @var $adminMenu AdminMenu */
                $adminMenu = AdminMenu::updateOrCreate(
                    ["id" => $id],
                    [
                        "parent_id" => $record["parent_id"],
                        "sort" => $record["sort"],
                        "title" => $record["title"],
                        "icon" => $record["icon"],
                        "uri" => $record["uri"],
                        "type" => $record["type"],
                    ]
                );
                $adminPermissionIds = AdminPermission::whereIn("slug", $record['permissions'])->pluck("id")->toArray();
                $adminRoleIds = AdminRole::whereIn("slug", $record['roles'])->pluck("id")->toArray();
                $adminMenu->permissions()->sync($adminPermissionIds);
                $adminMenu->roles()->sync($adminRoleIds);
            }
        });
    }
}
