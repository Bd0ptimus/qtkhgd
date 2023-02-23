<?php

namespace App\Admin\Models\Exports\Covid\TinhHinhHSGV;

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

class BaoCaoTHCS implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $school;
    protected $classes;
    protected $date;
    protected $staffs;

    public function __construct($school, $date)
    {
        $this->school = $school;
        $this->classes = $this->school->classes;
        $this->date = $date;
        $this->staffs = $this->school->staffs;
    }

    public function view(): View
    {
    
        return view('exports.covid_reports.thong_ke_tong_hop.thcs', [
            'school' => $this->school,
            'classes' => $this->classes,
            'staffs' => $this->staffs,
            'date' => $this->date
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Báo cáo Covid 19 (HS)";
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
        $endOfProcessingItems = 9;

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
            'Q' => 8,
            'R' => 8,
            'S' => 8,
            'T' => 8,
            'U' => 8,
            'V' => 8,
            'W' => 8,
            'X' => 8,
            'Y' => 8,
            'Z' => 8,
            'AA' => 8,
            'AB' => 8,
            'AC' => 8,
            'AD' => 8,
            'AE' => 8,
            'AF' => 8,
            'AG' => 8,
            'AH' => 8,
            'AI' => 8,
            'AJ' => 8,
            'AK' => 8,
            'AL' => 8,
            'AM' => 8,
            'AN' => 8,
            'AO' => 8,
            'AP' => 8,
            'AQ' => 8,
            'AR' => 8,
            'AS' => 8,
            'AT' => 8,
            'AU' => 8,
            'AV' => 8,
            'AW' => 8,
        ];
    }
}