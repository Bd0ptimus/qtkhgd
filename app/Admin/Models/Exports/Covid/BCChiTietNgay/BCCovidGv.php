<?php

namespace App\Admin\Models\Exports\Covid\BCChiTietNgay;

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

class BCCovidGv implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $school;
    protected $staffs;
    protected $date;

    public function __construct($school, $date)
    {
        $this->school = $school;
        $this->staffs = $this->school->staffs;
        $this->date = $date;
    }

    public function view(): View
    {
    
        return view('exports.covid_reports.bc_chi_tiet_ngay.staff', [
            'school' => $this->school,
            'staffs' => $this->staffs,
            'date' => $this->date
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Báo cáo Covid 19 (GV)";
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
        $endOfProcessingItems = (5 + 2 + count($this->staffs));

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
            'C' => 8,
            'D' => 8,
            'E' => 8,
            'F' => 8,
            'G' => 8,
            'H' => 8,
            'I' => 8,
            'J' => 8,
            'K' => 8,
            'L' => 8,
            'M' => 8,
            'N' => 8,
            'O' => 8,
            'P' => 8,
        ];
    }
}