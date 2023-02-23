<?php

namespace App\Admin\Models\Exports\Abnormals\ExportDemo;

use App\Admin\Models\Exports\Common\DataSheet;
use App\Models\HealthAbnormal;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportDemoAbnormal implements WithMultipleSheets
{
    use Exportable;

    protected $school_id;
    protected $school_branch;
    protected $class;

    public function __construct($school_id, $school_branch, $class)
    {
        $this->school_id = $school_id;
        $this->school_branch = $school_branch;
        $this->class = $class;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $dataRecords = [
            [
                'field' => 'Phân loại',
                'data' => HealthAbnormal::TYPES
            ],
            [
                'field' => 'Chuẩn đoán',
                'data' => HealthAbnormal::DIAGNOSIS
            ],
            [
                'field' => 'KQ xét nghiệm',
                'data' => HealthAbnormal::TEST_RESULTS
            ],
            [
                'field' => 'Tình trạng',
                'data' => HealthAbnormal::PATIENT_STATUSES
            ],
            [
                'field' => 'Xử lý',
                'data' => HealthAbnormal::HANDLES
            ]
        ];

        $sheets = [];

        $sheets[] = new DemoAbnormalSheet($this->school_id, $this->school_branch, $this->class);
        $sheets[] = (new DataSheet($dataRecords))->withRowFormat([
            '1' => NumberFormat::FORMAT_TEXT,
            '2' => NumberFormat::FORMAT_TEXT,
            '3' => NumberFormat::FORMAT_TEXT,
            '4' => NumberFormat::FORMAT_TEXT,
            '5' => NumberFormat::FORMAT_TEXT,
        ]);

        return $sheets;
    }
}