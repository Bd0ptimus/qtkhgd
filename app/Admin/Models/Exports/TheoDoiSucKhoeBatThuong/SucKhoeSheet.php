<?php

namespace App\Admin\Models\Exports\TheoDoiSucKhoeBatThuong;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SucKhoeSheet implements FromView, WithTitle, WithStyles, WithEvents, WithColumnWidths
{

    protected $title;
    protected $abnormals;

    public function __construct($title, Collection $abnormals)
    {
        $this->title = $title;
        $this->abnormals = $abnormals;
    }

    public function view(): View
    {
        return view('exports.sotheodoisuckhoe.suckhoe', [
            'title' => Str::upper($this->title),
            'abnormals' => $this->abnormals,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle('A2:I3')->applyFromArray([
            'font' => array(
                'bold' => true
            )
        ]);
        $sheet->getStyle('A5:J6')->applyFromArray([
            'font' => array(
                'bold' => true
            )
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 30,
            'C' => 10,
            'D' => 10,
            'E' => 12,
            'F' => 20,
            'G' => 25,
            'H' => 15,
            'I' => 15,
            'J' => 25,
        ];
    }


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

                $other = $sheet->getStyle('A5:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                $other->applyFromArray([
                    'borders' => $borderStyles
                ]);
                $other->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}