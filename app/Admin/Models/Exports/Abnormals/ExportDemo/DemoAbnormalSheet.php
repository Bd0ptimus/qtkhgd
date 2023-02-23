<?php

namespace App\Admin\Models\Exports\Abnormals\ExportDemo;

use App\Admin\Models\Exports\ExportTrait;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DemoAbnormalSheet implements FromView, WithEvents, WithColumnFormatting, WithTitle, WithColumnWidths
{
    use Exportable, ExportTrait;

    protected $school_id;
    protected $school_branch;
    protected $class;

    public function __construct($school_id, $school_branch, $class)
    {
        $this->school_id = $school_id;
        $this->school_branch = $school_branch;
        $this->class = $class;
    }

    public function view(): View
    {
        $students = Student::where([
            'school_id' => $this->school_id,
            'school_branch_id' => $this->school_branch,
            'class_id' => $this->class,
        ])->with('class')->get();
        $nowExcel = dateTimeToExcel(Carbon::now()->toDateString());

        return view('exports.demo.abnormals', [
            'students' => $students,
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
                $eDropDown = 'E';
                $fDropDown = 'F';
                $gDropDown = 'G';
                $hDropDown = 'H';
                $jDropDown = 'J';
                $eValidation = $this->getCellValidationFormula($sheet, $eDropDown, 1);
                $fValidation = $this->getCellValidationFormula($sheet, $fDropDown, 2);
                $gValidation = $this->getCellValidationFormula($sheet, $gDropDown, 3);
                $hValidation = $this->getCellValidationFormula($sheet, $hDropDown, 4);
                $jValidation = $this->getCellValidationFormula($sheet, $jDropDown, 5);

                // clone validation to remaining rows
                for ($i = 3; $i <= $sheet->getHighestRow(); $i++) {
                    $sheet->getCell("{$eDropDown}{$i}")->setDataValidation(clone $eValidation);
                    $sheet->getCell("{$fDropDown}{$i}")->setDataValidation(clone $fValidation);
                    $sheet->getCell("{$gDropDown}{$i}")->setDataValidation(clone $gValidation);
                    $sheet->getCell("{$hDropDown}{$i}")->setDataValidation(clone $hValidation);
                    $sheet->getCell("{$jDropDown}{$i}")->setDataValidation(clone $jValidation);
                }
                $other = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                $other->applyFromArray([
                    'borders' => $borderStyles,
                    'font' => [
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
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Theo dõi sức khỏe bất thường học sinh';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 25,
            'C' => 15,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 20,
            'H' => 14,
            'I' => 18,
            'J' => 18,
            'K' => 18,
            'L' => 20,
        ];
    }
}