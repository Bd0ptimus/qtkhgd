<?php

use Illuminate\Database\Seeder;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\Log;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'id' => 1,
                'title' => 'Mới',
                'color' => 'default',
                'position' => 1,
                'creator_id' => 1,
            ],
            [
                'id' => 2,
                'title' => 'Đang thực hiện',
                'color' => 'info',
                'position' => 2,
                'creator_id' => 1,
            ],
            [
                'id' => 3,
                'title' => 'Chờ phản hồi',
                'color' => 'warning',
                'position' => 3,
                'creator_id' => 1,
            ],
            [
                'id' => 4,
                'title' => 'Hoàn thành',
                'color' => 'success',
                'position' => 4,
                'creator_id' => 1,
            ]
        ];

        // delete data old
        TaskStatus::truncate();

        try {
            foreach ($datas as $data) {
                TaskStatus::create($data);
            }
        } catch (\Throwable $th) {
            Log::info($th);
        }
    }
}
