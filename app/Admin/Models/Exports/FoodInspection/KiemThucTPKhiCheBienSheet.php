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

class KiemThucTPKhiCheBienSheet implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $foodInspection;
    protected $onProcessingItems;

    public function __construct(FoodInspection $foodInspection)
    {
        $this->foodInspection = $foodInspection;
    }

    public function view(): View
    {
        $this->onProcessingItems = $this->foodInspection->onProcessingItems;

        return view('exports.food_inspection.kiem_thuc_tp_khi_che_bien', [
            'onProcessingItems' => $this->onProcessingItems,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Kiểm thực TP khi chế biến';
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
        $endOfProcessingItems = (7 + 2 + count($this->onProcessingItems));

        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(false);
        $sheet->getStyle('A7:' . $sheet->getHighestDataColumn() . $endOfProcessingItems)
            ->applyFromArray($allBorders);

        $sheet->getStyle('F10:G' . $sheet->getHighestDataRow())
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 34,
            'D' => 45,
            'E' => 22,
            'F' => 31,
            'G' => 33,
            'H' => 28,
            'I' => 27,
            'J' => 38,
            'K' => 30,
            'L' => 30,
            'M' => 37
        ];
    }
}