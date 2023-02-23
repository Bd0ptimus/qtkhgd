<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Repositories\NotificationAdminRepository;
use App\Admin\Repositories\RegularGroupRepository;
use App\Admin\Repositories\TeacherPlanRepository;
use App\Models\NotificationAdmin;
use App\Models\TeacherLessonHistory;
use App\Models\TeacherPlanHistory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TeacherPlanService
{
    protected $teacherPlanRepo;
    protected $notificationAdminRepo;
    protected $regularGroupRepo;

    public function __construct(TeacherPlanRepository $repo, NotificationAdminRepository $notificationAdminRepo, RegularGroupRepository $regularGroupRepo)
    {
        $this->teacherPlanRepo = $repo;
        $this->notificationAdminRepo = $notificationAdminRepo;
        $this->regularGroupRepo = $regularGroupRepo;
    }

    public function create($request) {
        $plan = $this->teacherPlanRepo->create([
            'regular_group_id' => $request['regular_group_id'],
            'staff_id' => $request['staff_id'], 
            'grade' => $request['grade'], 
            'subject_id' => $request['subject_id'] ?? null, 
            'month' => $request['month'] ?? null, 
            'chuyen_de' => $request['chuyen_de'] ?? null, 
            'additional_tasks'  => $request['additional_tasks'] ?? null,
            'status' => PLAN_PENDING
        ]);
        if($plan && isset($request['lessons']) && count($request['lessons']) > 0) {
            $this->teacherPlanRepo->setLessons($plan->id, $request['lessons']);
        }
        return $plan;
    }

    public function update($planId, $request) {
        DB::beginTransaction();
        try{
            if(isset($request['lessons'])) {
                $lessons = $request['lessons'];
                unset($request['lessons']);
            }
            unset($request['_token']);
            $this->teacherPlanRepo->update($planId, $request);
            if(isset($lessons) && count($lessons) > 0) {
                $this->teacherPlanRepo->setLessons($planId,$lessons);
            }

            DB::commit();
        } catch(Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create group plan]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
        }
    }

    public function checkIfCanMange($staffId) {
        if(Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_HIEU_TRUONG, ROLE_GIAO_VIEN])) {
            if(Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_HIEU_TRUONG])) {
                $userDetail = Admin::user()->staffDetail;
                if($userDetail && $userDetail->id == $staffId) return true;
                else return false;
            }
            return true;
        }
        return false;
    }

    public function findById($planId) {
        return $this->teacherPlanRepo->findById($planId, ['*'], ['subject', 'lessons', 'staff', 'histories']);
    }

    public function findByStaffAndGroup($staffId, $rgId) {
        return $this->teacherPlanRepo->findByStaffAndGroup($staffId, $rgId);
    }

    public function addHistory($plan, $content) {
        $history = TeacherPlanHistory::create([
            'teacher_plan_id' => $plan->id,
            'notes' => $content,
            'status' => $plan->status
        ]);
    }

    public function addLessonHistory($lesson, $content) {
        $history = TeacherLessonHistory::create([
            'teacher_lesson_id' => $lesson->id,
            'notes' => $content,
            'created_by' => Admin::user()->id
        ]);
    }

    public function allStaffBySchool($schoolId) {
        return $this->teacherPlanRepo->findBySchoolId($schoolId);
    }

    public function findPendingPlanByGroup($rgId) {
        return $this->teacherPlanRepo->findPendingPlanByGroup($rgId);
    }

    public function findApprovedPlanByGroup($rgId, $staffId = null) {
        return $this->teacherPlanRepo->findApprovedPlanByGroup($rgId, $staffId);
    }

    public function findApprovedPlanByStaff($staffId) {
        return $this->teacherPlanRepo->findApprovedPlanByStaff($staffId);
    }

    public function sendNotificationToPlanOwner($owner, $title, $content) {
        $this->notificationAdminRepo->create([
            'user_id' => $owner->id,
            'title' => $title,
            'content' => $content,
            'type' => NotificationAdmin::TYPE["teacher_plan"],
            'data' => json_encode([
            ])
        ]);
    }

    public function findApprovedPlanByDistrict($districtId, $params=[]) {
        return $this->teacherPlanRepo->findApprovedPlanByDistrictWithConditions($districtId, $params);
    }
    
    public function download($school, $teacherPlan)
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle('Sheet name');
        $schoolType = $school->school_type;
        $endColumn = [
            'index' => 0,
            'name' => 'A',
        ];
        $fullName = $teacherPlan->staff ? $teacherPlan->staff->fullname : 'Không có tên';
        $title = "KẾ HOẠCH GIÁO DỤC CỦA GIÁO VIÊN\nGiáo viên {$fullName}";
        $worksheet->getRowDimension('2')->setRowHeight(40);
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $spreadsheet->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Times New Roman',
                'size' =>  13,
            ]
        ]);
        // $worksheet->getStyle('A1:O20')
        //     ->applyFromArray([
        //         'font' => [
        //             'name' => 'Times New Roman',
        //             'size' =>  13,
        //         ]
        //     ]);

        // format header and data export
        switch($schoolType) {
            case 1:
                $worksheet->mergeCells('A2:G2');
                $worksheet->getCell('A2')->setValue($title)->getStyle()->applyFromArray($styleTitle);
                $worksheet->getCell('A4')->setValue('Tuần, tháng');
                $worksheet->getCell('B4')->setValue('Chương trình và sách giáo khoa');
                $worksheet->getCell('C4')->setValue('Chủ đề/Mạch nội dung');
                $worksheet->getCell('D4')->setValue('Tên bài học');
                $worksheet->getCell('E4')->setValue('Tiết học/thời lượng');
                $worksheet->getCell('F4')->setValue('Nội dung điều chỉnh, bổ sung(nếu có)');
                $worksheet->getCell('G4')->setValue('Ghi chú');
                $endColumn = [
                    'index' => 7,
                    'name' => 'G',
                ];
                break;
            case 2:
                $worksheet->mergeCells('A2:H2');
                $worksheet->getCell('A2')->setValue($title)->getStyle()->applyFromArray($styleTitle);
                $worksheet->getCell('A4')->setValue('STT');
                $worksheet->getCell('B4')->setValue('Bài học');
                $worksheet->getCell('C4')->setValue('Tên bài học');
                // $worksheet->getCell('D4')->setValue('Tiết thứ');
                $worksheet->getCell('D4')->setValue('Số tiết');
                $worksheet->getCell('E4')->setValue('Thời điểm');
                $worksheet->getCell('F4')->setValue('Thiết bị dạy học');
                $worksheet->getCell('G4')->setValue('Địa điểm dạy học');
                $endColumn = [
                    'index' => 8,
                    'name' => 'H',
                ];
                break;
            case 6:
                $worksheet->mergeCells('A2:F2');
                $worksheet->getCell('A2')->setValue($title)->getStyle()->applyFromArray($styleTitle);
                $worksheet->getCell('A4')->setValue('Tuần');
                $worksheet->getCell('B4')->setValue('Từ Ngày đến ngày');
                $worksheet->getCell('C4')->setValue('Chủ đề');
                $worksheet->getCell('D4')->setValue('Nội dung');
                $worksheet->getCell('E4')->setValue('Phối hợp thực hiện');
                $worksheet->getCell('F4')->setValue('Kết quả');
                $endColumn = [
                    'index' => 6,
                    'name' => 'F',
                ];
                break;
            default:
                $worksheet->mergeCells('A2:H2');
                $worksheet->getCell('A2')->setValue($title)->getStyle()->applyFromArray($styleTitle);
                $worksheet->getCell('A4')->setValue('STT');
                $worksheet->getCell('B4')->setValue('Bài học');
                $worksheet->getCell('C4')->setValue('Tên bài học');
                $worksheet->getCell('D4')->setValue('Tiết thứ');
                $worksheet->getCell('E4')->setValue('Số tiết');
                $worksheet->getCell('F4')->setValue('Thời điểm');
                $worksheet->getCell('G4')->setValue('Thiết bị dạy học');
                $worksheet->getCell('H4')->setValue('Địa điểm dạy học');
                $endColumn = [
                    'index' => 8,
                    'name' => 'H',
                ];
        }
        $dataExport = [];
        $row = 2;
        foreach ($teacherPlan->lessons as $index => $val) {
            if ($schoolType == 6) {
                $dataExport[] = [
                    $index + 1,
                    $val->thoi_gian,
                    $val->chu_de,
                    $val->noi_dung,
                    $val->phoi_hop,
                    $val->ket_qua,
                ] ;
                $row = 5;
            }
            elseif ($schoolType == 1) {
                $dataExport[] = [
                    $val->tuan_thang,
                    $val->chu_de,
                    $val->ten_bai_hoc,
                    $val->so_tiet,
                    $val->noi_dung_dieu_chinh,
                    $val->ghi_chu,
                ] ;
                $row = 5;
            }
            elseif ($schoolType == 2) {
                foreach(range('B','C') as $col) { 
                    $worksheet->getColumnDimension($col)->setWidth(10);
                }
                $dataExport[] = [
                    $index + 1,
                    $val->bai_hoc,
                    $val->ten_bai_hoc,
                    $val->so_tiet,
                    $val->thoi_diem,
                    $val->thiet_bi_day_hoc,
                    $val->dia_diem_day_hoc,
                ] ;
                $row = 5;
            }
            else {
                $dataExport[] = [
                    $index + 1,
                    $val->bai_hoc,
                    $val->ten_bai_hoc,
                    $val->tiet_thu,
                    $val->so_tiet,
                    $val->thoi_diem,
                    $val->thiet_bi_day_hoc,
                    $val->dia_diem_day_hoc,
                ] ;
                $row = 5;
            }
        }
        $worksheet->getColumnDimension('A')->setWidth(6);
        $worksheet->getColumnDimension('B')->setWidth(100);
        $worksheet->getColumnDimension('C')->setWidth(40);
        $worksheet->getColumnDimension('D')->setWidth(10);
        $worksheet->getColumnDimension('E')->setWidth(10);
        $worksheet->getColumnDimension('F')->setWidth(40);
        $worksheet->getColumnDimension('G')->setWidth(25);
        $worksheet->getColumnDimension('H')->setWidth(20);
        // foreach (range('A', $endColumn['name']) as $col) {
        //     $worksheet->getColumnDimension($col)->setAutoSize(true);
        // }
        $worksheet->getStyle('A4:' . $endColumn['name'] . '4')->applyFromArray($styleBold);
        $worksheet->getStyle('A4:' . $endColumn['name'] . (count($teacherPlan->lessons) + 4))->applyFromArray($styleBorder);
        $worksheet->fromArray($dataExport, $nullValue = null, $startCell = 'A' . $row);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xls");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $fullName . '.xls"');
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
        
    }
}
