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

class KiemThucTPTruocKhiCheBienSheet implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $foodInspection;
    protected $itemsTuoi;
    protected $itemsKho;

    public function __construct(FoodInspection $foodInspection)
    {
        $this->foodInspection = $foodInspection;
    }

    public function view(): View
    {
        $this->itemsTuoi = $this->foodInspection->beforeProcessingItems->filter(function ($item) {
            return $item->food_type === 'Thực phẩm tươi sống';
        });
        $this->itemsKho = $this->foodInspection->beforeProcessingItems->filter(function ($item) {
            return $item->food_type === 'Thực phẩm khô';
        });

        return view('exports.food_inspection.kiem_thuc_tp_truoc_khi_che_bien', [
            'foodInspection' => $this->foodInspection,
            'itemsTuoi' => $this->itemsTuoi,
            'itemsKho' => $this->itemsKho,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Kiểm thực TP trước khi chế biến';
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
        $endOfTuoiColumn = (8 + 2 + count($this->itemsTuoi));
        $startOfKhoColumn = $endOfTuoiColumn + 3;
        $endOfKhoColumn = ($startOfKhoColumn + 2 + count($this->itemsKho));

        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(false);
        $sheet->getStyle('A8:' . $sheet->getHighestDataColumn() . $endOfTuoiColumn)
            ->applyFromArray($allBorders);
        $sheet->getStyle('C11:C' . $endOfTuoiColumn)
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

        $sheet->getStyle('A' . $startOfKhoColumn . ':' . $sheet->getHighestDataColumn() . $endOfKhoColumn)
            ->applyFromArray($allBorders);


        $sheet->getStyle('E' . ($startOfKhoColumn + 3) . ':E' . $endOfKhoColumn)
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
        $sheet->getStyle('J' . ($startOfKhoColumn + 3) . ':J' . $endOfKhoColumn)
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 16,
            'C' => 25,
            'D' => 21,
            'E' => 15,
            'F' => 30,
            'G' => 24,
            'H' => 20,
            'I' => 30,
            'J' => 16,
            'K' => 35,
            'L' => 32,
            'M' => 28,
            'N' => 28,
            'O' => 35,
        ];
    }
}