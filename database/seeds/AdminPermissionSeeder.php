<?php

use App\Admin\Models\AdminPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPermissionSeeder extends Seeder
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
                "admin.manager" => [
                    "name" => "Admin manager",
                    "http_uri" => "GET::portal/user,GET::portal/role,GET::portal/permission,ANY::portal/log/*,ANY::portal/menu/*"
                ],
                "setting.full" => [
                    "name" => "Quản lý cấu hình",
                    "http_uri" => "ANY::portal/backup/*,ANY::portal/currency/*,ANY::portal/email/*,ANY::portal/email_template/*,ANY::portal/env/*,ANY::portal/language/*,ANY::portal/store_info/*,ANY::portal/store_value/*"
                ],
                "cms.full" => [
                    "name" => "CMS manager",
                    "http_uri" => "ANY::portal/page/*,ANY::portal/banner/*,ANY::portal/cms_category/*,ANY::portal/cms_content/*,ANY::portal/news/*"
                ],
                "user-management" => [
                    "name" => "Quản lý tài khoản",
                    "http_uri" => "ANY::portal/user/*"
                ],
                "basic-permissions" => [
                    "name" => "Quyền cơ bản",
                    "http_uri" => "ANY::portal/auth/*,GET::portal/select-module,POST::portal/select-module,GET::portal,ANY::portal/agency/*,ANY::portal/ajax/*,ANY::portal/uploads/*,POST::portal/upload-tinymce-image,ANY::portal/notification/*,ANY::portal/routing/*"
                ],
                "school-management" => [
                    "name" => "Quản lý trường",
                    "http_uri" => "ANY::portal/ajax/*,ANY::portal/school/*"
                ],
                "medical-management" => [
                    "name" => "Quản lý YTHD",
                    "http_uri" => "ANY::portal/school_medical/*"
                ],
                "view-school-activities" => [
                    "name" => "Xem hoạt động trường",
                    "http_uri" => "ANY::portal/ajax/*,ANY::portal/school/*,ANY::portal/school_medical/*"
                ],
                "district-manager" => [
                    "name" => "Quản lý Phòng",
                    "http_uri" => "ANY::portal/district/*"
                ],
                "province-manage" => [
                    "name" => "Quản lý Sở",
                    "http_uri" => "ANY::portal/province/*"
                ],
                "ward-manager" => [
                    "name" => "Quản lý Xã",
                    "http_uri" => "ANY::portal/ward/*"
                ],
                "task-management" => [
                    "name" => "Quản lý công việc",
                    "http_uri" => "GET::portal/common/tasks,GET::portal/common/tasks/create,GET::portal/common/tasks/{id},POST::portal/common/tasks,PUT::portal/common/tasks/{id},POST::portal/common/tasks/{id},POST::portal/common/comment"
                ],
                "school-plan-management" => [
                    "name" => "Quản trị kế hoạch giáo dục",
                    "http_uri" => "ANY::portal/school/*"
                ],
                "data-sample" => [
                    "name" => "Dữ liệu mẫu",
                    "http_uri" => "ANY::portal/common/*"
                ],
            ];
            //end-record
            $insertData = [];
            AdminPermission::query()->truncate();
            foreach ($records as $slug => $record) {
                $insertData[] = [
                    "slug" => $slug,
                    'name' => $record['name'],
                    "http_uri" => $record["http_uri"],
                ];
            }
            AdminPermission::insert($insertData);
        });
    }
}
