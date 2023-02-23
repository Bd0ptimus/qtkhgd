<?php

namespace App\Admin\Models\Exports\PeriodicalCheck\ExportDemo;

use App\Admin\Models\Exports\Common\DataSheet;
use App\Models\SchoolClass;
use App\Models\StudentHealthIndex;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportDemoPeriodicalCheck implements WithMultipleSheets
{
    use Exportable;

    protected $class;
    protected $students;

    public function __construct(SchoolClass $class, Collection $students)
    {
        $this->class = $class;
        $this->students = $students;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $dataRecords = [
            [
                'field' => 'Mắt',
                'data' => StudentHealthIndex::EYE_SIGHT_OPTIONS
            ]
        ];

        $sheets = [];

        $sheets[] = new DemoPeriodicalCheckSheet($this->students);
        $sheets[] = (new DataSheet($dataRecords))->withRowFormat([
            '1' => NumberFormat::FORMAT_TEXT
        ]);

        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('import sức khỏe học sinh lớp ' . $this->class->class_name . '.xls');
    }
}