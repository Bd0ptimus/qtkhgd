<?php

namespace App\Admin\Models\Exports\Abnormals;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

class StaffAbnormalsSheet implements FromView, WithTitle, WithStyles, WithEvents, WithColumnWidths, WithColumnFormatting
{

    protected $title;
    protected $staffs;

    public function __construct($title, Collection $staffs)
    {
        $this->title = $title;
        $this->staffs = $staffs;
    }

    public function view(): View
    {
        return view('exports.staff_abnormals', [
            'title' => Str::upper($this->title),
            'staffs' => $this->staffs,
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
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 15,
            'C' => 20,
            'D' => 15,
            'E' => 25,
            'F' => 25,
            'G' => 20,
            'H' => 20,
            'I' => 15,
            'J' => 20,
            'K' => 15,
            'L' => 20,
            'M' => 25,
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

                $sheet->getStyle('A5:' . $sheet->getHighestDataColumn() . '5')->applyFromArray([
                    'font' => array(
                        'bold' => true
                    )
                ]);

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