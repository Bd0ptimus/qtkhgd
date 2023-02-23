<?php

namespace App\Admin\Models\Exports;

use App\Models\School;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportSanitationCheck extends BaseExport implements WithStyles
{
    protected $title;
    protected $school;
    protected $schoolSanitationChecks;

    public function __construct(School $school, Collection $schoolSanitationChecks)
    {
        $this->school = $school;
        $this->schoolSanitationChecks = $schoolSanitationChecks;
        $this->title = "Phân công theo dõi vệ sinh trường học tại " . $this->school->school_name;
    }

    public function view(): View
    {
        $extraLength = 7;
        $tableName = 'ĐÁNH GIÁ CÔNG TÁC VỆ SINH HỌC ĐƯỜNG';
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.sanitation_check', [
            'extraLength' => $extraLength,
            'title' => Str::upper($this->school->school_name),
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'schoolSanitationChecks' => $this->schoolSanitationChecks
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 10,
            'C' => 12,
            'D' => 12,
            'E' => 15,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 15,
            'K' => 15,
            'L' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle('A1:L20')->applyFromArray([
            'font' => array(
                'size' => 13,
                'bold' => true
            )
        ]);
    }

    public function downloadAsName()
    {
        return $this->download($this->title . '.xls');
    }
}