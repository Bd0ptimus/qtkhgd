<?php

namespace App\Admin\Models\Exports\Students;

use App\Admin\Models\Exports\ExportTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportStudents implements FromView, WithEvents, WithColumnFormatting, WithColumnWidths
{
    use Exportable, ExportTrait;

    protected $students;

    public function __construct(Collection $students)
    {
        $this->students = $students;
    }

    public function view(): View
    {
        return view('exports.students', [
            'students' => $this->students,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {
                $borderStyles = [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ];
                $sheet = $event->sheet->getDelegate();

                $other = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                $other->applyFromArray([
                    'borders' => $borderStyles,
                    'font' => [
                        'size' => 13,
                    ]
                ]);
                $other->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }


    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 20,
            'C' => 25,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 20,
            'J' => 25,
        ];
    }


    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}