<?php

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
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
                "administrator" => [
                    "id" => "1",
                    "name" => "Administrator",
                    "permissions" => []
                ],
                "customer-support" => [
                    "id" => "2",
                    "name" => "Hỗ trợ khách hàng",
                    "permissions" => ["user-management", "basic-permissions", "school-management", "view-school-activities", "district-manager", "province-manage", "ward-manager", "task-management", "school-plan-management", "data-sample"]
                ],
                "so-gd" => [
                    "id" => "3",
                    "name" => "Quản lý cấp Sở",
                    "permissions" => ["basic-permissions", "view-school-activities", "province-manage", "task-management"]
                ],
                "phong-gd" => [
                    "id" => "4",
                    "name" => "Quản lý cấp Phòng",
                    "permissions" => ["basic-permissions", "view-school-activities", "district-manager", "task-management"]
                ],
                "chuyen-vien-phong" => [
                    "id" => "5",
                    "name" => "Chuyên viên phòng giáo dục",
                    "permissions" => ["basic-permissions", "view-school-activities", "district-manager", "task-management"]
                ],
                "hieu-truong" => [
                    "id" => "6",
                    "name" => "Hiệu Trưởng",
                    "permissions" => ["basic-permissions", "school-management", "task-management", "data-sample"]
                ],
                "giao-vien" => [
                    "id" => "7",
                    "name" => "Giáo viên",
                    "permissions" => ["basic-permissions", "school-management", "task-management", "school-plan-management", "data-sample"]
                ],
                "to-truong" => [
                    "id" => "8",
                    "name" => "Tổ trưởng chuyên môn",
                    "permissions" => ["basic-permissions", "task-management", "data-sample"]
                ],
                "school-manager" => [
                    "id" => "9",
                    "name" => "Quản lý trường",
                    "permissions" => ["basic-permissions", "school-management", "task-management", "data-sample"]
                ],
                "cong-tac-vien" => [
                    "id" => "10",
                    "name" => "Cộng tác viên",
                    "permissions" => ["basic-permissions", "data-sample"]
                ],
                "parents" => [
                    "id" => "13",
                    "name" => "Phụ Huynh",
                    "permissions" => ["basic-permissions"]
                ],
            ];
            //end-record
            AdminRole::query()->truncate();
            foreach ($records as $slug => $record) {
                /** @var $adminRole AdminRole */
                $adminRole = AdminRole::updateOrCreate(
                    ["slug" => $slug],
                    [
                        "name" => $record["name"],
                        "id" => $record["id"]
                    ]
                );
                $adminPermissionIds = AdminPermission::whereIn("slug", $record['permissions'])->pluck("id")->toArray();
                $adminRole->permissions()->sync($adminPermissionIds);
            }
        });
    }
}
