<?php

namespace App\Admin\Models\Exports\SpecialTest;

use App\Models\School;
use App\Models\StaffSpecialistTest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportStaffSpecialTest implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $school;
    protected $staffTest;

    public function __construct(School $school, StaffSpecialistTest $staffTest)
    {
        $this->school = $school;
        $this->staffTest = $staffTest;
    }

    public function view(): View
    {
        $now = Carbon::now();
        return view('exports.special_test.staff_special_test', [
            'staff' => $this->staffTest->staff,
            'staffTest' => $this->staffTest,
            'now' => $now
//            'trackingFoodEntries' => $this->trackingFoodEntries,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Sheet1';
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
        $outlineBorder = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];

        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 11,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);

        $sheet->getStyle('B25:' . $sheet->getHighestDataColumn() . '25')
            ->applyFromArray($allBorders);

        $sheet->getStyle('B39:' . $sheet->getHighestDataColumn() . '39')
            ->applyFromArray($allBorders);
        $sheet->getStyle('B40:D80')
            ->applyFromArray($outlineBorder);
        $sheet->getStyle('E40:E80')
            ->applyFromArray($outlineBorder);

        $sheet->getStyle('B83:' . $sheet->getHighestDataColumn() . '83')
            ->applyFromArray($allBorders);
        $sheet->getStyle('B84:D88')
            ->applyFromArray($outlineBorder);
        $sheet->getStyle('E84:E88')
            ->applyFromArray($outlineBorder);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 2,
            'B' => 22,
            'C' => 27,
            'D' => 20,
            'E' => 20,
        ];
    }

    public function downloadAsName()
    {
        return $this->download('GiayKhamSucKhoe_' . Carbon::now()->toDateString() . $this->school->school_name . '.xls');
    }
}