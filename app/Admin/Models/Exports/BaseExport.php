<?php

namespace App\Admin\Models\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExport implements FromView, WithColumnWidths, WithEvents
{
    use Exportable;

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $borderStyles = [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ];

                $sheet = $event->sheet->getDelegate();

                $header = $sheet->getStyle('A6:' . $sheet->getHighestDataColumn() . '6');
                $header->getFont()->setBold(true);

                $other = $sheet->getStyle('A6:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                /** @var Style $item */
                foreach ([$header, $other] as $item) {
                    $item->applyFromArray([
                        'borders' => $borderStyles
                    ]);
                    $item->getAlignment()
                        ->setWrapText(true)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }

    public static function defaultStyleHeader(Worksheet $sheet, int $row){
        $header = $sheet->getStyle('A' . $row . ':' . $sheet->getHighestDataColumn() . $row);
        $header->getFont()->setBold(true);
    }

    public static function defaultStyleOther(Worksheet $sheet, int $rowStart, string $col, int $rowFinish, $opts = []){
        $borderStyles = [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ],
        ];

        $item = $sheet->getStyle('A' . $rowStart . ':' . $col . $rowFinish);
        $item->applyFromArray([
            'borders' => $borderStyles
        ]);
        $item->getAlignment()
            ->setWrapText(true);
        if (array_key_exists('is_align', $opts) && $opts['is_align'] == false) {
            
        }else{
            $item->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
    }
}