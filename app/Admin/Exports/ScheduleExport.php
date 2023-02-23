<?php

namespace App\Admin\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ScheduleExport implements FromView, ShouldAutoSize, WithEvents
{
    private array $data;

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('admin.export.schedule', $this->data);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $styleFontTextAll = [
            'font' => [
                'name' => 'Times New Roma',
                'size' => 13,
            ],
        ];

        $styleHeader = [
            'font' => [
                'bold' => true,
            ],
        ];

        $styleTextCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleFontTextAll,
                $styleHeader,
                $styleBorder,
                $styleTextCenter
            ) {
                $event->sheet->styleCells('A1:H13', $styleFontTextAll);
                $event->sheet->styleCells('A2', $styleHeader);
                $event->sheet->styleCells('A2', $styleTextCenter);
                $event->sheet->styleCells('A4:A10', $styleHeader);
                $event->sheet->styleCells('A4:H4', $styleHeader);
                $event->sheet->getDelegate()->getStyle('A4:H13')->applyFromArray($styleBorder);
            },
        ];
    }
}
