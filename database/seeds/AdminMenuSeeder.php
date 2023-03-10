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
                    "title" => "Th??ng b??o",
                    "icon" => "fa-bell",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["so-gd", "phong-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong"]
                ],
                "65" => [
                    "parent_id" => 58,
                    "sort" => 1,
                    "title" => "Th??ng b??o chung",
                    "icon" => "fa-bars",
                    "uri" => "admin::notification",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "67" => [
                    "parent_id" => 0,
                    "sort" => 10,
                    "title" => "C???u h??nh th??ng tin",
                    "icon" => "fa-upload",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "69" => [
                    "parent_id" => 0,
                    "sort" => 7,
                    "title" => "D??? li???u ?????a ch??nh",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "70" => [
                    "parent_id" => 67,
                    "sort" => 1,
                    "title" => "Th??ng tin gi??o vi??n",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "admin::routing/admin.school.view_staff_list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "71" => [
                    "parent_id" => 67,
                    "sort" => 2,
                    "title" => "Th??ng tin h???c sinh",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "admin::routing/admin.school.view_student_list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "72" => [
                    "parent_id" => 0,
                    "sort" => 1,
                    "title" => "Trang ch???",
                    "icon" => "fa-home",
                    "uri" => "/",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "83" => [
                    "parent_id" => 67,
                    "sort" => 2,
                    "title" => "T??i kho???n ng?????i d??ng",
                    "icon" => "fa-arrow-circle-right",
                    "uri" => "admin::routing/admin.school.users",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "school-manager"]
                ],
                "90" => [
                    "parent_id" => 69,
                    "sort" => 3,
                    "title" => "S??? GD",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency/provinces",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "91" => [
                    "parent_id" => 69,
                    "sort" => 2,
                    "title" => "T???ng quan",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "92" => [
                    "parent_id" => 69,
                    "sort" => 4,
                    "title" => "Ph??ng GD",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency/districts",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "93" => [
                    "parent_id" => 69,
                    "sort" => 5,
                    "title" => "Ph?????ng x??",
                    "icon" => "fa-bars",
                    "uri" => "admin::agency/wards",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "110" => [
                    "parent_id" => 0,
                    "sort" => 2,
                    "title" => "B??n l??m vi???c",
                    "icon" => "fa-bars",
                    "uri" => "admin::school",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "giao-vien", "school-manager"]
                ],
                "112" => [
                    "parent_id" => 69,
                    "sort" => 1,
                    "title" => "Import D??? Li???u ?????a Ch??nh",
                    "icon" => "fa-bars",
                    "uri" => "admin::sysconf/import-locations",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "137" => [
                    "parent_id" => 0,
                    "sort" => 11,
                    "title" => "L???ch s??? ho???t ?????ng",
                    "icon" => "fa-history",
                    "uri" => "admin::routing/admin.school.user_activity",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "hieu-truong", "school-manager"]
                ],
                "146" => [
                    "parent_id" => 0,
                    "sort" => 9,
                    "title" => "Qu???n l?? tr?????ng",
                    "icon" => "fa-building",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "147" => [
                    "parent_id" => 146,
                    "sort" => 1,
                    "title" => "M???m non - Ti???u h???c - THCS - GDTX",
                    "icon" => "fa-bars",
                    "uri" => "admin::school/maugiao-tieuhoc-thcs",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "phong-gd"]
                ],
                "148" => [
                    "parent_id" => 146,
                    "sort" => 2,
                    "title" => "Trung h???c ph??? th??ng",
                    "icon" => "fa-bars",
                    "uri" => "admin::school/thpt",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd"]
                ],
                "160" => [
                    "parent_id" => 0,
                    "sort" => 8,
                    "title" => "Qu???n l?? c??c ph??ng",
                    "icon" => "fa-address-book",
                    "uri" => "admin::district",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "177" => [
                    "parent_id" => 0,
                    "sort" => 4,
                    "title" => "Vi???c c???n l??m",
                    "icon" => "fa-check",
                    "uri" => "admin::common/tasks",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => []
                ],
                "178" => [
                    "parent_id" => 0,
                    "sort" => 6,
                    "title" => "C???u h??nh h??? th???ng",
                    "icon" => "fa-gear",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator"]
                ],
                "179" => [
                    "parent_id" => 178,
                    "sort" => 1,
                    "title" => "M??n h???c",
                    "icon" => "fa-book",
                    "uri" => "admin::sysconf/subject",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "180" => [
                    "parent_id" => 178,
                    "sort" => 3,
                    "title" => "T??? chuy??n m??n",
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
                    "title" => "M??n h???c theo kh???i",
                    "icon" => "fa-bars",
                    "uri" => "admin::sysconf/subject/subject-by-grade",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "183" => [
                    "parent_id" => 0,
                    "sort" => 3,
                    "title" => "B??n l??m vi???c (PGD)",
                    "icon" => "fa-bars",
                    "uri" => "admin::district",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["phong-gd", "chuyen-vien-phong"]
                ],
                "184" => [
                    "parent_id" => 192,
                    "sort" => 3,
                    "title" => "Phi???u b??i t???p v??? nh??",
                    "icon" => "fa-book",
                    "uri" => "admin::common/homework-sheet/list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "185" => [
                    "parent_id" => 192,
                    "sort" => 4,
                    "title" => "????? ki???m tra",
                    "icon" => "fa-bookmark",
                    "uri" => "admin::common/exercise-question/list",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "186" => [
                    "parent_id" => 192,
                    "sort" => 1,
                    "title" => "S??ch ??i???n t???",
                    "icon" => "fa-book",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "187" => [
                    "parent_id" => 192,
                    "sort" => 2,
                    "title" => "B??i gi???ng m???u",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/lesson-sample",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "188" => [
                    "parent_id" => 186,
                    "sort" => 2,
                    "title" => "Danh m???c s??ch",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/ebook-categories",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support"]
                ],
                "189" => [
                    "parent_id" => 186,
                    "sort" => 1,
                    "title" => "Th?? vi???n s??ch",
                    "icon" => "fa-book",
                    "uri" => "admin::common/ebooks",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "giao-vien", "to-truong", "school-manager", "cong-tac-vien"]
                ],
                "190" => [
                    "parent_id" => 0,
                    "sort" => 15,
                    "title" => "L???ch b??i gi???ng c???a gi??o vi??n",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/teacher-weekly-lesson",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["hieu-truong", "giao-vien", "to-truong"]
                ],
                "191" => [
                    "parent_id" => 192,
                    "sort" => 6,
                    "title" => "Kho d??? li???u m?? ph???ng",
                    "icon" => "fa-bars",
                    "uri" => "admin::common/simulator",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "hieu-truong", "school-manager", "cong-tac-vien"]
                ],
                "192" => [
                    "parent_id" => 0,
                    "sort" => 5,
                    "title" => "D??? li???u m???u",
                    "icon" => "fa-bars",
                    "uri" => "",
                    "type" => 0,
                    "permissions" => [],
                    "roles" => ["administrator", "customer-support", "so-gd", "chuyen-vien-phong", "giao-vien", "to-truong", "cong-tac-vien"]
                ],
                "193" => [
                    "parent_id" => 192,
                    "sort" => 5,
                    "title" => "Ch??? ti??u m???u",
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
