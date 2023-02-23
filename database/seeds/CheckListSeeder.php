<?php

use Illuminate\Database\Seeder;
use App\Admin\Models\CheckList;
use Illuminate\Support\Facades\Log;

class CheckListSeeder extends Seeder
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
                'title' => 'Check list 01',
                'description' => 'description 1',
            ],
            [
                'title' => 'Check list 02',
                'description' => 'description 2',
            ],
            [
                'title' => 'Check list 03',
                'description' => 'description 3',
            ],
            [
                'title' => 'Check list 04',
                'description' => 'description 4',
            ],
            [
                'title' => 'Check list 05',
                'description' => 'description 5',
            ],
            [
                'title' => 'Check list 06',
                'description' => 'description 6',
            ]
        ];

        try {
            foreach ($datas as $data) {
                CheckList::create($data);
            }
        } catch (\Throwable $th) {
            Log::info($th);
        }
    }
}
