<?php

use App\Models\GradeSubject;
use App\Models\RegularGroup;
use App\Models\RegularGroupSubject;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefaultSubjectAndGroup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataSubjects = [
            0 => [
                "id" => 1,
                "name" => "Tiếng Việt",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:49:32",
                "updated_at" => "2022-06-15 01:49:32",
            ],
            1 => [
                "id" => 2,
                "name" => "Toán",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:50:08",
                "updated_at" => "2022-06-15 01:50:08",
            ],
            2 => [
                "id" => 3,
                "name" => "Đạo Đức",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:50:40",
                "updated_at" => "2022-06-15 01:50:40",
            ],
            3 => [
                "id" => 4,
                "name" => "Tự nhiên và xã hội",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:51:03",
                "updated_at" => "2022-06-15 01:51:03",
            ],
            4 => [
                "id" => 5,
                "name" => "Khoa học",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:51:23",
                "updated_at" => "2022-06-15 01:58:15",
            ],
            5 => [
                "id" => 6,
                "name" => "Lịch sử và Địa lý",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:51:43",
                "updated_at" => "2022-06-15 01:51:43",
            ],
            6 => [
                "id" => 7,
                "name" => "Nghệ thuật",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:52:12",
                "updated_at" => "2022-06-15 01:52:12",
            ],
            7 => [
                "id" => 8,
                "name" => "Âm nhạc",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:52:35",
                "updated_at" => "2022-06-15 01:52:35",
            ],
            8 => [
                "id" => 9,
                "name" => "Mỹ thuật",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:52:55",
                "updated_at" => "2022-06-15 01:52:55",
            ],
            9 => [
                "id" => 10,
                "name" => "Kỹ thuật và Thể dục",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:53:15",
                "updated_at" => "2022-06-15 01:53:15",
            ],
            10 => [
                "id" => 11,
                "name" => "Tiếng Anh",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:54:05",
                "updated_at" => "2022-06-15 02:20:01",
            ],
            11 => [
                "id" => 12,
                "name" => "Tin học",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:54:46",
                "updated_at" => "2022-06-15 01:54:46",
            ],
            12 => [
                "id" => 13,
                "name" => "Vật lí",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:55:44",
                "updated_at" => "2022-06-15 01:55:44",
            ],
            13 => [
                "id" => 14,
                "name" => "Hóa học",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:56:04",
                "updated_at" => "2022-06-15 01:56:04",
            ],
            14 => [
                "id" => 15,
                "name" => "Sinh học",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:56:29",
                "updated_at" => "2022-06-15 01:56:29",
            ],
            15 => [
                "id" => 16,
                "name" => "Lịch sử",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:56:55",
                "updated_at" => "2022-06-15 01:56:55",
            ],
            16 => [
                "id" => 17,
                "name" => "Địa lí",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:57:21",
                "updated_at" => "2022-06-15 01:57:21",
            ],
            17 => [
                "id" => 18,
                "name" => "Giáo dục công dân",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:57:49",
                "updated_at" => "2022-06-15 01:57:49",
            ],
            18 => [
                "id" => 19,
                "name" => "Mỹ thuật",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:59:12",
                "updated_at" => "2022-06-15 01:59:12",
            ],
            19 => [
                "id" => 20,
                "name" => "Âm nhạc",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 01:59:29",
                "updated_at" => "2022-06-15 01:59:29",
            ],
            20 => [
                "id" => 21,
                "name" => "Công nghệ",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 02:00:00",
                "updated_at" => "2022-06-15 02:00:00",
            ],
            21 => [
                "id" => 22,
                "name" => "Thể dục",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 02:00:36",
                "updated_at" => "2022-06-15 02:00:36",
            ],
            22 => [
                "id" => 23,
                "name" => "Giáo dục quốc phòng",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 02:01:26",
                "updated_at" => "2022-06-15 02:01:26",
            ],
            23 => [
                "id" => 24,
                "name" => "Ngữ văn",
                "description" => null,
                "school_id" => null,
                "deleted_at" => null,
                "created_at" => "2022-06-15 02:03:54",
                "updated_at" => "2022-06-15 02:03:54",
            ],
        ];

        $dataGradeSubjects = [
            [
                "grade" => 1,
                "subject_id" => 1,
                ],
                [
                "grade" => 2,
                "subject_id" => 1,
                ],
                [
                "grade" => 3,
                "subject_id" => 1,
                ],
                [
                "grade" => 4,
                "subject_id" => 1,
                ],
                [
                "grade" => 5,
                "subject_id" => 1,
                ],
                [
                "grade" => 1,
                "subject_id" => 2,
                ],
                [
                "grade" => 2,
                "subject_id" => 2,
                ],
                [
                "grade" => 3,
                "subject_id" => 2,
                ],
                [
                "grade" => 4,
                "subject_id" => 2,
                ],
                [
                "grade" => 5,
                "subject_id" => 2,
                ],
                [
                "grade" => 6,
                "subject_id" => 2,
                ],
                [
                "grade" => 7,
                "subject_id" => 2,
                ],
                [
                "grade" => 8,
                "subject_id" => 2,
                ],
                [
                "grade" => 9,
                "subject_id" => 2,
                ],
                [
                "grade" => 10,
                "subject_id" => 2,
                ],
                [
                "grade" => 11,
                "subject_id" => 2,
                ],
                [
                "grade" => 12,
                "subject_id" => 2,
                ],
                [
                "grade" => 1,
                "subject_id" => 3,
                ],
                [
                "grade" => 2,
                "subject_id" => 3,
                ],
                [
                "grade" => 3,
                "subject_id" => 3,
                ],
                [
                "grade" => 4,
                "subject_id" => 3,
                ],
                [
                "grade" => 5,
                "subject_id" => 3,
                ],
                [
                "grade" => 1,
                "subject_id" => 4,
                ],
                [
                "grade" => 2,
                "subject_id" => 4,
                ],
                [
                "grade" => 3,
                "subject_id" => 4,
                ],
                [
                "grade" => 4,
                "subject_id" => 4,
                ],
                [
                "grade" => 5,
                "subject_id" => 4,
                ],
                [
                "grade" => 1,
                "subject_id" => 6,
                ],
                [
                "grade" => 2,
                "subject_id" => 6,
                ],
                [
                "grade" => 3,
                "subject_id" => 6,
                ],
                [
                "grade" => 4,
                "subject_id" => 6,
                ],
                [
                "grade" => 5,
                "subject_id" => 6,
                ],
                [
                "grade" => 1,
                "subject_id" => 7,
                ],
                [
                "grade" => 2,
                "subject_id" => 7,
                ],
                [
                "grade" => 3,
                "subject_id" => 7,
                ],
                [
                "grade" => 4,
                "subject_id" => 7,
                ],
                [
                "grade" => 5,
                "subject_id" => 7,
                ],
                [
                "grade" => 1,
                "subject_id" => 8,
                ],
                [
                "grade" => 2,
                "subject_id" => 8,
                ],
                [
                "grade" => 3,
                "subject_id" => 8,
                ],
                [
                "grade" => 4,
                "subject_id" => 8,
                ],
                [
                "grade" => 5,
                "subject_id" => 8,
                ],
                [
                "grade" => 1,
                "subject_id" => 9,
                ],
                [
                "grade" => 2,
                "subject_id" => 9,
                ],
                [
                "grade" => 3,
                "subject_id" => 9,
                ],
                [
                "grade" => 4,
                "subject_id" => 9,
                ],
                [
                "grade" => 5,
                "subject_id" => 9,
                ],
                [
                "grade" => 1,
                "subject_id" => 10,
                ],
                [
                "grade" => 2,
                "subject_id" => 10,
                ],
                [
                "grade" => 3,
                "subject_id" => 10,
                ],
                [
                "grade" => 4,
                "subject_id" => 10,
                ],
                [
                "grade" => 5,
                "subject_id" => 10,
                ],
                [
                "grade" => 1,
                "subject_id" => 12,
                ],
                [
                "grade" => 2,
                "subject_id" => 12,
                ],
                [
                "grade" => 3,
                "subject_id" => 12,
                ],
                [
                "grade" => 4,
                "subject_id" => 12,
                ],
                [
                "grade" => 5,
                "subject_id" => 12,
                ],
                [
                "grade" => 6,
                "subject_id" => 12,
                ],
                [
                "grade" => 7,
                "subject_id" => 12,
                ],
                [
                "grade" => 8,
                "subject_id" => 12,
                ],
                [
                "grade" => 9,
                "subject_id" => 12,
                ],
                [
                "grade" => 10,
                "subject_id" => 12,
                ],
                [
                "grade" => 11,
                "subject_id" => 12,
                ],
                [
                "grade" => 12,
                "subject_id" => 12,
                ],
                [
                "grade" => 8,
                "subject_id" => 14,
                ],
                [
                "grade" => 9,
                "subject_id" => 14,
                ],
                [
                "grade" => 10,
                "subject_id" => 14,
                ],
                [
                "grade" => 11,
                "subject_id" => 14,
                ],
                [
                "grade" => 12,
                "subject_id" => 14,
                ],
                [
                "grade" => 6,
                "subject_id" => 15,
                ],
                [
                "grade" => 7,
                "subject_id" => 15,
                ],
                [
                "grade" => 8,
                "subject_id" => 15,
                ],
                [
                "grade" => 9,
                "subject_id" => 15,
                ],
                [
                "grade" => 10,
                "subject_id" => 15,
                ],
                [
                "grade" => 11,
                "subject_id" => 15,
                ],
                [
                "grade" => 12,
                "subject_id" => 15,
                ],
                [
                "grade" => 6,
                "subject_id" => 16,
                ],
                [
                "grade" => 7,
                "subject_id" => 16,
                ],
                [
                "grade" => 8,
                "subject_id" => 16,
                ],
                [
                "grade" => 9,
                "subject_id" => 16,
                ],
                [
                "grade" => 10,
                "subject_id" => 16,
                ],
                [
                "grade" => 11,
                "subject_id" => 16,
                ],
                [
                "grade" => 12,
                "subject_id" => 16,
                ],
                [
                "grade" => 6,
                "subject_id" => 17,
                ],
                [
                "grade" => 7,
                "subject_id" => 17,
                ],
                [
                "grade" => 8,
                "subject_id" => 17,
                ],
                [
                "grade" => 9,
                "subject_id" => 17,
                ],
                [
                "grade" => 10,
                "subject_id" => 17,
                ],
                [
                "grade" => 11,
                "subject_id" => 17,
                ],
                [
                "grade" => 12,
                "subject_id" => 17,
                ],
                [
                "grade" => 6,
                "subject_id" => 18,
                ],
                [
                "grade" => 7,
                "subject_id" => 18,
                ],
                [
                "grade" => 8,
                "subject_id" => 18,
                ],
                [
                "grade" => 9,
                "subject_id" => 18,
                ],
                [
                "grade" => 10,
                "subject_id" => 18,
                ],
                [
                "grade" => 11,
                "subject_id" => 18,
                ],
                [
                "grade" => 12,
                "subject_id" => 18,
                ],
                [
                "grade" => 1,
                "subject_id" => 5,
                ],
                [
                "grade" => 2,
                "subject_id" => 5,
                ],
                [
                "grade" => 3,
                "subject_id" => 5,
                ],
                [
                "grade" => 4,
                "subject_id" => 5,
                ],
                [
                "grade" => 5,
                "subject_id" => 5,
                ],
                [
                "grade" => 6,
                "subject_id" => 19,
                ],
                [
                "grade" => 7,
                "subject_id" => 19,
                ],
                [
                "grade" => 8,
                "subject_id" => 19,
                ],
                [
                "grade" => 9,
                "subject_id" => 19,
                ],
                [
                "grade" => 6,
                "subject_id" => 20,
                ],
                [
                "grade" => 7,
                "subject_id" => 20,
                ],
                [
                "grade" => 8,
                "subject_id" => 20,
                ],
                [
                "grade" => 9,
                "subject_id" => 20,
                ],
                [
                "grade" => 6,
                "subject_id" => 21,
                ],
                [
                "grade" => 7,
                "subject_id" => 21,
                ],
                [
                "grade" => 8,
                "subject_id" => 21,
                ],
                [
                "grade" => 9,
                "subject_id" => 21,
                ],
                [
                "grade" => 6,
                "subject_id" => 22,
                ],
                [
                "grade" => 7,
                "subject_id" => 22,
                ],
                [
                "grade" => 8,
                "subject_id" => 22,
                ],
                [
                "grade" => 9,
                "subject_id" => 22,
                ],
                [
                "grade" => 10,
                "subject_id" => 22,
                ],
                [
                "grade" => 11,
                "subject_id" => 22,
                ],
                [
                "grade" => 12,
                "subject_id" => 22,
                ],
                [
                "grade" => 10,
                "subject_id" => 23,
                ],
                [
                "grade" => 11,
                "subject_id" => 23,
                ],
                [
                "grade" => 12,
                "subject_id" => 23,
                ],
                [
                "grade" => 6,
                "subject_id" => 13,
                ],
                [
                "grade" => 7,
                "subject_id" => 13,
                ],
                [
                "grade" => 8,
                "subject_id" => 13,
                ],
                [
                "grade" => 9,
                "subject_id" => 13,
                ],
                [
                "grade" => 10,
                "subject_id" => 13,
                ],
                [
                "grade" => 11,
                "subject_id" => 13,
                ],
                [
                "grade" => 12,
                "subject_id" => 13,
                ],
                [
                "grade" => 6,
                "subject_id" => 24,
                ],
                [
                "grade" => 7,
                "subject_id" => 24,
                ],
                [
                "grade" => 8,
                "subject_id" => 24,
                ],
                [
                "grade" => 9,
                "subject_id" => 24,
                ],
                [
                "grade" => 10,
                "subject_id" => 24,
                ],
                [
                "grade" => 11,
                "subject_id" => 24,
                ],
                [
                "grade" => 12,
                "subject_id" => 24,
                ],
                [
                "grade" => 1,
                "subject_id" => 11,
                ],
                [
                "grade" => 2,
                "subject_id" => 11,
                ],
                [
                "grade" => 3,
                "subject_id" => 11,
                ],
                [
                "grade" => 4,
                "subject_id" => 11,
                ],
                [
                "grade" => 5,
                "subject_id" => 11,
                ],
                [
                "grade" => 6,
                "subject_id" => 11,
                ],
                [
                "grade" => 7,
                "subject_id" => 11,
                ],
                [
                "grade" => 8,
                "subject_id" => 11,
                ],
                [
                "grade" => 9,
                "subject_id" => 11,
                ],
                [
                "grade" => 10,
                "subject_id" => 11,
                ],
                [
                "grade" => 11,
                "subject_id" => 11,
                ],
                [
                "grade" => 12,
                "subject_id" => 11,
                ],
        ];


        $dataRegularGroup = [
            [
            "id" => 1,
            "name" => "Tổ nhà trẻ",
            "description" => null,
            "school_level" => 6,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:12:32",
            "updated_at" => "2022-06-15 02:12:32",
            ],
            [
            "id" => 2,
            "name" => "Tổ mẫu giáo 3-4 tuổi",
            "description" => null,
            "school_level" => 6,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:12:47",
            "updated_at" => "2022-06-15 02:12:47",
            ],
            [
            "id" => 3,
            "name" => "Tổ mẫu giáo 4-5 tuổi",
            "description" => null,
            "school_level" => 6,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:13:01",
            "updated_at" => "2022-06-15 02:13:01",
            ],
            [
            "id" => 4,
            "name" => "Tổ mẫu giáo 5-6 tuổi",
            "description" => null,
            "school_level" => 6,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:13:16",
            "updated_at" => "2022-06-15 02:13:16",
            ],
            [
            "id" => 5,
            "name" => "Tổ 1",
            "description" => "Bao gồm khối 1",
            "school_level" => 1,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:13:47",
            "updated_at" => "2022-06-15 02:13:47",
            ],
            [
            "id" => 6,
            "name" => "Tổ 2, 3",
            "description" => "Bao gồm khối 2 và khối 3",
            "school_level" => 1,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:14:11",
            "updated_at" => "2022-06-15 02:14:11",
            ],
            [
            "id" => 7,
            "name" => "Tổ 4, 5",
            "description" => "Bao gồm khối 4 và khối 5",
            "school_level" => 1,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:14:36",
            "updated_at" => "2022-06-15 02:14:36",
            ],
            [
            "id" => 8,
            "name" => "Tổ khoa học tự nhiên",
            "description" => null,
            "school_level" => 2,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:15:31",
            "updated_at" => "2022-06-15 02:15:31",
            ],
            [
            "id" => 9,
            "name" => "Tổ khoa học xã hội",
            "description" => null,
            "school_level" => 2,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:16:55",
            "updated_at" => "2022-06-15 02:16:55",
            ],
            [
            "id" => 10,
            "name" => "Toán Tin",
            "description" => null,
            "school_level" => 3,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:17:46",
            "updated_at" => "2022-06-15 02:17:46",
            ],
            [
            "id" => 11,
            "name" => "Ngữ văn - Lịch sử- Địa lí- giáo dục công dân",
            "description" => null,
            "school_level" => 3,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:18:30",
            "updated_at" => "2022-06-15 02:18:30",
            ],
            [
            "id" => 12,
            "name" => "Vật lí - Hóa học - Sinh học - Công nghệ",
            "description" => null,
            "school_level" => 3,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:19:01",
            "updated_at" => "2022-06-15 02:19:01",
            ],
            [
            "id" => 13,
            "name" => "Tiếng anh - Thể dục - Giáo dục quốc phòng",
            "description" => null,
            "school_level" => 3,
            "school_id" => null,
            "deleted_at" => null,
            "created_at" => "2022-06-15 02:19:28",
            "updated_at" => "2022-06-15 02:19:28",
            ],
        ];
     
        $dataRegularGroupSubject = [
            [
            "regular_group_id" => 9,
            "subject_id" => 8,
            ],
            [
            "regular_group_id" => 9,
            "subject_id" => 9,
            ],
            [
            "regular_group_id" => 9,
            "subject_id" => 11,
            ],
            [
            "regular_group_id" => 9,
            "subject_id" => 16,
            ],
            [
            "regular_group_id" => 9,
            "subject_id" => 17,
            ],
            [
            "regular_group_id" => 9,
            "subject_id" => 18,
            ],
            [
            "regular_group_id" => 9,
            "subject_id" => 24,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 2,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 5,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 12,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 13,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 14,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 15,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 21,
            ],
            [
            "regular_group_id" => 8,
            "subject_id" => 22,
            ],
            [
            "regular_group_id" => 10,
            "subject_id" => 2,
            ],
            [
            "regular_group_id" => 10,
            "subject_id" => 12,
            ],
            [
            "regular_group_id" => 11,
            "subject_id" => 16,
            ],
            [
            "regular_group_id" => 11,
            "subject_id" => 17,
            ],
            [
            "regular_group_id" => 11,
            "subject_id" => 18,
            ],
            [
            "regular_group_id" => 11,
            "subject_id" => 24,
            ],
            [
            "regular_group_id" => 12,
            "subject_id" => 13,
            ],
            [
            "regular_group_id" => 12,
            "subject_id" => 14,
            ],
            [
            "regular_group_id" => 12,
            "subject_id" => 15,
            ],
            [
            "regular_group_id" => 12,
            "subject_id" => 21,
            ],
            [
            "regular_group_id" => 13,
            "subject_id" => 11,
            ],
            [
            "regular_group_id" => 13,
            "subject_id" => 22,
            ],
            [
            "regular_group_id" => 13,
            "subject_id" => 23,
            ],
        ];
        DB::beginTransaction();

        try {

            //Import subject
            Subject::truncate();
            foreach ($dataSubjects as $subject) {
                Subject::create($subject);
            }


            //import subject grade
            GradeSubject::truncate();
            foreach ($dataGradeSubjects as $item) {
                GradeSubject::create($item);
            }

            //import subject grade
            RegularGroup::truncate();
            foreach ($dataRegularGroup as $item) {
                RegularGroup::create($item);
            }

            //import subject grade
            RegularGroupSubject::truncate();
            foreach ($dataRegularGroupSubject as $item) {
                RegularGroupSubject::create($item);
            }

            DB::commit();
        } catch (\Throwable $th) {
            Log::info($th);
            DB::rollback();
        }
    }
}
