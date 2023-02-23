<?php

namespace App\Admin\Models\Exports\QuanLyDichBenh;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuanLyDichBenhSheet implements FromView, WithTitle, WithStyles, WithEvents, WithColumnWidths, WithColumnFormatting
{
    use Exportable;

    protected $title;
    protected $students;

    public function __construct($title, Collection $students)
    {
        $this->title = $title;
        $this->students = $students;
    }

    public function view(): View
    {
        return view('exports.soquanlydichbenh.quanlydichbenh', [
            'title' => Str::upper($this->title),
            'students' => $this->students,
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
        $sheet->getStyle('A2:' . $sheet->getHighestDataColumn() . '3')->applyFromArray([
            'font' => array(
                'bold' => true
            )
        ]);
        $sheet->getStyle('A5:' . $sheet->getHighestDataColumn() . '6')->applyFromArray([
            'font' => array(
                'bold' => true
            )
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 28,
            'C' => 10,
            'D' => 10,
            'E' => 12,
            'F' => 25,
            'G' => 22,
            'H' => 22,
            'I' => 16,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 25,
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


    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}