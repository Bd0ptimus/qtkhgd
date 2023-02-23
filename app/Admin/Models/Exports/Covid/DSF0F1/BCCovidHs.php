<?php

namespace App\Admin\Models\Exports\Covid\DSF0F1;

use App\Models\FoodInspection;
use App\Models\SchoolClass;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BCCovidHs implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $school;
    protected $students;
    protected $date;

    public function __construct($school, $date)
    {
        $this->school = $school;
        $this->students = $this->school->students;
        $this->date = $date;
    }

    public function view(): View
    {
    
        return view('exports.covid_reports.ds_f0_f1.student', [
            'school' => $this->school,
            'students' => $this->students,
            'date' => $this->date
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "DS F0 F1 (HS)";
    }

    public function styles(Worksheet $sheet)
    {
        $allBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];
        $endOfProcessingItems = (5 + 2 + 22);

        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:' . $sheet->getHighestDataColumn() . $endOfProcessingItems)
            ->applyFromArray($allBorders);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 15,
            'D' => 10,
            'E' => 15,
            'F' => 15
        ];
    }
}