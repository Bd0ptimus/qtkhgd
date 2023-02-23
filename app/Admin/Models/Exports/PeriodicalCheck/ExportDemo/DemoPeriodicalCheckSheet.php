<?php

namespace App\Admin\Models\Exports\PeriodicalCheck\ExportDemo;

use App\Admin\Models\Exports\ExportTrait;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DemoPeriodicalCheckSheet implements FromView, WithEvents, WithColumnFormatting, WithTitle, WithColumnWidths
{
    use Exportable, ExportTrait;

    protected $students;

    public function __construct(Collection $students)
    {
        $this->students = $students;
    }

    public function view(): View
    {
        $nowExcel = dateTimeToExcel(Carbon::now()->toDateString());

        return view('exports.demo.periodical_check', [
            'students' => $this->students,
            'nowExcel' => $nowExcel
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

                // set dropdown column
                $iDropDown = 'I';
                $jDropDown = 'J';
                $kDropDown = 'K';
                $lDropDown = 'L';
                $iValidation = $this->getCellValidationFormula($sheet, $iDropDown, 1, 3);
                $jValidation = $this->getCellValidationFormula($sheet, $jDropDown, 1, 3);
                $kValidation = $this->getCellValidationFormula($sheet, $kDropDown, 1, 3);
                $lValidation = $this->getCellValidationFormula($sheet, $lDropDown, 1, 3);

                // clone validation to remaining rows
                for ($i = 4; $i <= $sheet->getHighestRow(); $i++) {
                    $sheet->getCell("{$iDropDown}{$i}")->setDataValidation(clone $iValidation);
                    $sheet->getCell("{$jDropDown}{$i}")->setDataValidation(clone $jValidation);
                    $sheet->getCell("{$kDropDown}{$i}")->setDataValidation(clone $kValidation);
                    $sheet->getCell("{$lDropDown}{$i}")->setDataValidation(clone $lValidation);
                }
                $other = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                $other->applyFromArray([
                    'borders' => $borderStyles,
                    'font' => [
                        'name' => 'Times New Roman',
                        'size' => 13,
                    ]
                ]);
                $other->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => 'mm/yyyy',
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Theo dõi sức khỏe định kỳ';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 25,
            'C' => 10,
            'D' => 12,
            'E' => 12,
            'F' => 12,
            'G' => 14,
            'H' => 12,
            'I' => 12,
            'J' => 12,
            'K' => 12,
            'L' => 12,
        ];
    }
}