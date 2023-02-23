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

class KhamChuyenKhoaSheet implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function view(): View
    {
        return view('exports.health_profile.kham_chuyen_khoa', [
            'student' => $this->student
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Khám chuyên khoa';
    }

    public function styles(Worksheet $sheet)
    {
        $borderStyles = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman',
                'size' => 12,
            )
        ]);

        $cSpecialistTests = count($this->student->specialistTests);

        $start = 5;
        for ($i = 0; $i < $cSpecialistTests; $i++) {
            $next = $start + 6;
            $sheet->getStyle('C' . $start . ':C' . $next)
                ->applyFromArray($borderStyles);
            $sheet->getStyle('D' . $start . ':' . $sheet->getHighestDataColumn() . $next)
                ->applyFromArray($borderStyles);

            $start = $next + 1;
            $next = $start + 4;
            $sheet->getStyle('C' . $start . ':C' . $next)
                ->applyFromArray($borderStyles);
            $sheet->getStyle('D' . $start . ':' . $sheet->getHighestDataColumn() . $next)
                ->applyFromArray($borderStyles);

            $start = $next + 1;
            $next = $start + 4;
            $sheet->getStyle('C' . $start . ':C' . $next)
                ->applyFromArray($borderStyles);
            $sheet->getStyle('D' . $start . ':' . $sheet->getHighestDataColumn() . $next)
                ->applyFromArray($borderStyles);

            $start = $next + 1;
            $next = $start + 4;
            $sheet->getStyle('C' . $start . ':C' . $next)
                ->applyFromArray($borderStyles);
            $sheet->getStyle('D' . $start . ':' . $sheet->getHighestDataColumn() . $next)
                ->applyFromArray($borderStyles);

            $start = $next + 1;
            $next = $start + 3;
            $sheet->getStyle('C' . $start . ':C' . $next)
                ->applyFromArray($borderStyles);
            $sheet->getStyle('D' . $start . ':' . $sheet->getHighestDataColumn() . $next)
                ->applyFromArray($borderStyles);

            $start = $next + 3;
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 0.5,
            'B' => 0.5,
            'C' => 20,
            'D' => 11,
            'E' => 10,
            'F' => 26,
        ];
    }
}