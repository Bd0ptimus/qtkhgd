<?php

namespace App\Admin\Models\Exports\HealthProfile;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ThongTinChungSheet implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function view(): View
    {
        return view('exports.health_profile.thong_tin_chung', [
            'student' => $this->student
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Thông tin chung';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman',
                'size' => 12,
            )
        ]);
        $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . '2')->applyFromArray([
            'font' => array(
                'bold' => true
            )
        ]);

        $sheet->getStyle('B22:' . $sheet->getHighestDataColumn() . ($sheet->getHighestRow() - 1))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ]
            ]);
        $sheet->getStyle($sheet->getHighestRow())->getAlignment()->setWrapText(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 1,
            'B' => 4,
            'C' => 26,
            'D' => 12,
            'E' => 10,
            'F' => 15,
        ];
    }
}