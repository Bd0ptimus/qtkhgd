<?php

namespace App\Admin\Models\Exports\VaccineHistory;

use App\Admin\Models\Exports\ExportTrait;
use App\Models\VaccineHistory;
use App\Models\SchoolStaff;
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

class ExportStaffVaccineForm implements FromCollection, WithMapping, WithHeadings, WithEvents, WithColumnFormatting, WithColumnWidths
{
    use Exportable, ExportTrait;

    protected $school_id;
    protected $results;

    public function __construct($school_id)
    {
        $this->school_id = $school_id;
        
    }

    public function collection()
    {
        $this->results = SchoolStaff::where([
            'school_id' => $this->school_id
        ])->with('vaccineHistory')->get();

        
        return $this->results;
    }

    /**
     * @param mixed $student
     * @return array[]
     * @var Student $student
     */
    public function map($staff): array
    {
        $vaccineHistory = $staff->vaccineHistory->all()[0] ?? null;
        
        return [
            [
                $staff->fullname,

                $vaccineHistory ? $vaccineHistory->m1_date : null,
                $vaccineHistory ? $vaccineHistory->m1_loai_vc : null,
                $vaccineHistory ? $vaccineHistory->m1_lo_vc : null,
                $vaccineHistory ? $vaccineHistory->m1_hsd : null,
                $vaccineHistory ? $vaccineHistory->m1_dvt : null,
                
                $vaccineHistory ? $vaccineHistory->m2_date : null,
                $vaccineHistory ? $vaccineHistory->m2_loai_vc : null,
                $vaccineHistory ? $vaccineHistory->m2_lo_vc : null,
                $vaccineHistory ? $vaccineHistory->m2_hsd : null,
                $vaccineHistory ? $vaccineHistory->m2_dvt : null,

                $vaccineHistory ? $vaccineHistory->m3_date : null,
                $vaccineHistory ? $vaccineHistory->m3_loai_vc : null,
                $vaccineHistory ? $vaccineHistory->m3_lo_vc : null,
                $vaccineHistory ? $vaccineHistory->m3_hsd : null,
                $vaccineHistory ? $vaccineHistory->m3_dvt : null,

                $vaccineHistory ? $vaccineHistory->m4_date : null,
                $vaccineHistory ? $vaccineHistory->m4_loai_vc : null,
                $vaccineHistory ? $vaccineHistory->m4_lo_vc : null,
                $vaccineHistory ? $vaccineHistory->m4_hsd : null,
                $vaccineHistory ? $vaccineHistory->m4_dvt : null,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'H??? t??n',
            
            'Ng??y ti??m m1',
            'Lo???i vaccine m1', //C
            'L?? vaccine m1',
            'H???n s??? d???ng m1',
            '????n v??? ti??m m1',
            
            'Ng??y ti??m m2',
            'Lo???i vaccine m2', //C
            'L?? vaccine m2',
            'H???n s??? d???ng m2',
            '????n v??? ti??m m2',

            'Ng??y ti??m m3',
            'Lo???i vaccine m3', //C
            'L?? vaccine m3',
            'H???n s??? d???ng m3',
            '????n v??? ti??m m3',

            'Ng??y ti??m m4',
            'Lo???i vaccine m4', //C
            'L?? vaccine m4',
            'H???n s??? d???ng m4',
            '????n v??? ti??m m4',
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
                $m1 = 'C';
                $m2 = 'H';
                $m3 = 'M';
                $m4 = 'R';
                $m1Validation = $this->getCellValidation($sheet, $m1, VaccineHistory::VACCINE_TYPE);
                $m2Validation = $this->getCellValidation($sheet, $m2, VaccineHistory::VACCINE_TYPE);
                $m3Validation = $this->getCellValidation($sheet, $m3, VaccineHistory::VACCINE_TYPE);
                $m4Validation = $this->getCellValidation($sheet, $m4, VaccineHistory::VACCINE_TYPE);

                // clone validation to remaining rows
                for ($i = 3; $i <= $rowCount; $i++) {
                    $sheet->getCell("{$m1}{$i}")->setDataValidation(clone $m1Validation);
                    $sheet->getCell("{$m2}{$i}")->setDataValidation(clone $m2Validation);
                    $sheet->getCell("{$m3}{$i}")->setDataValidation(clone $m3Validation);
                    $sheet->getCell("{$m4}{$i}")->setDataValidation(clone $m4Validation);
                    
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'L' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'Q' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,

            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 15,

            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 20,
            'K' => 15,

            'L' => 15,
            'M' => 15,
            'N' => 15,
            'O' => 20,
            'P' => 15,

            'Q' => 15,
            'R' => 15,
            'S' => 15,
            'T' => 20,
            'U' => 15
        ];
    }
}