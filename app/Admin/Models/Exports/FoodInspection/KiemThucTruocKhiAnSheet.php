<?php

namespace App\Admin\Models\Exports\FoodInspection;

use App\Models\FoodInspection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KiemThucTruocKhiAnSheet implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $foodInspection;
    protected $beforeMealItems;

    public function __construct(FoodInspection $foodInspection)
    {
        $this->foodInspection = $foodInspection;
    }

    public function view(): View
    {
        $this->beforeMealItems = $this->foodInspection->beforeMealItems;

        return view('exports.food_inspection.kiem_thuc_truoc_khi_an', [
            'beforeMealItems' => $this->beforeMealItems,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Kiểm thực trước khi ăn';
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
        $endOfBeforeMealItems = (7 + 2 + count($this->beforeMealItems));

        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(false);
        $sheet->getStyle('A7:' . $sheet->getHighestDataColumn() . $endOfBeforeMealItems)
            ->applyFromArray($allBorders);

        $sheet->getStyle('E10:F' . $sheet->getHighestDataRow())
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 25,
            'E' => 35,
            'F' => 37,
            'G' => 55,
            'H' => 30,
            'I' => 30,
            'J' => 37
        ];
    }
}