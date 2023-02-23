<?php

namespace App\Admin\Models\Exports\Insurance;

use App\Admin\Models\Exports\ExportTrait;
use App\Models\SchoolHealthInsurance;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportDemoInsurance implements FromCollection, WithMapping, WithHeadings, WithEvents, WithColumnFormatting, WithColumnWidths
{
    use Exportable, ExportTrait;

    protected $school_id;
    protected $school_branch;
    protected $class;
    protected $results;

    public function __construct($school_id, $school_branch, $class)
    {
        $this->school_id = $school_id;
        $this->school_branch = $school_branch;
        $this->class = $class;
    }

    public function collection()
    {
        $this->results = Student::where([
            'school_id' => $this->school_id,
            'school_branch_id' => $this->school_branch,
            'class_id' => $this->class,
        ])->get();

        return $this->results;
    }

    /**
     * @param mixed $student
     * @return array[]
     * @var Student $student
     */
    public function map($student): array
    {
        return [
            [
                $student->fullname,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Họ tên',
            'Hình thức tham gia bảo hiểm',
            'Nơi đăng ký khám chữa bệnh',
            'Thời hạn đóng bảo hiểm',
            'Thời hạn sử dụng - Từ ngày',
            'Thời hạn sử dụng - Đến ngày',
            'Số thẻ bảo hiểm',
            'Số tiền phải nộp',
            'Ngày bàn giao thẻ',
        ];
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

                // get layout counts (add 1 to rows for heading row)
                $rowCount = $this->results->count() + 1;

                $other = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                $other->applyFromArray([
                    'borders' => $borderStyles,
                    'font' => [
                        'size' => 13,
                    ]
                ]);
                $other->getAlignment()->setWrapText(true);

                // set dropdown column
                $formTypeDropdown = 'B';
                $termTypeDropDown = 'D';
                $formTypeValidation = $this->getCellValidation($sheet, $formTypeDropdown, SchoolHealthInsurance::FORM_TYPE);
                $termTypeValidation = $this->getCellValidation($sheet, $termTypeDropDown, SchoolHealthInsurance::TERM_TYPE);

                // clone validation to remaining rows
                for ($i = 3; $i <= $rowCount; $i++) {
                    $sheet->getCell("{$formTypeDropdown}{$i}")->setDataValidation(clone $formTypeValidation);
                    $sheet->getCell("{$termTypeDropDown}{$i}")->setDataValidation(clone $termTypeValidation);
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 30,
            'C' => 32,
            'D' => 27,
            'E' => 29,
            'F' => 30,
            'G' => 20,
            'H' => 19,
            'I' => 21,
        ];
    }
}