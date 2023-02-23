<?php

namespace App\Admin\Models\Exports\District\Covid\TinhHinhHSGV;

use App\Models\FoodInspection;
use App\Models\SchoolClass;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BaoCaoTieuHoc implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $district;
    protected $schools;
    protected $date;

    public function __construct($district, $date)
    {
        $this->district = $district;
        $this->date = $date;
        $this->schools = School::with(['classes', 'classes.students', 'classes.students.vaccineHistory','classes.students.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        },'staffs', 'staffs.vaccineHistory','staffs.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        }])->where(['school_type' => 1, 'district_id' => $this->district->id])->get();
    }

    public function view(): View
    {
    
        return view('exports.district.covid_reports.thong_ke_tong_hop.tieuhoc', [
            'schools' => $this->schools,
            'district' => $this->district,
            'date' => $this->date
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Báo cáo tình hình HS, GV";
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
        $endOfProcessingItems = 5 + 2 + count($this->schools) + 1;

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
        ];
    }
}