<?php

namespace App\Admin\Models\Exports\HealthProfile;

use App\Models\School;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TheoDoiSucKhoeSheet implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $school;
    protected $student;

    public function __construct(School $school, Student $student)
    {
        $this->school = $school;
        $this->student = $student;
    }

    public function view(): View
    {
        return view('exports.health_profile.theo_doi_suc_khoe', [
            'school' => $this->school,
            'student' => $this->student
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Theo dõi sức khỏe';
    }

    public function styles(Worksheet $sheet)
    {
        $allBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];
        $outlineBorders = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman',
                'size' => 12,
            )
        ]);
        if ($this->school->isMamNon()) {
            $this->styleForMamnon($sheet, $outlineBorders);
        } else if ($this->school->isTieuHoc()) {
            $this->styleForOtherSchool($sheet, $outlineBorders, 5);
        } else if ($this->school->isThcs()) {
            $this->styleForOtherSchool($sheet, $outlineBorders, 4);
        } else if ($this->school->isThpt()) {
            $this->styleForOtherSchool($sheet, $outlineBorders, 3);
        }
        $this->styleForSucKhoeBatThuong($sheet, $allBorders);
    }

    public function styleForMamnon(Worksheet $sheet, $outlineBorders)
    {
        $cHealthIndex = count($this->student->healthIndexes);
        $healthIndexStart = 8;
        for ($i = 0; $i < $cHealthIndex; $i++) {
            $nextHealthIndex = $healthIndexStart + 2;
            $sheet->getStyle('C' . $healthIndexStart . ':C' . $nextHealthIndex)->applyFromArray($outlineBorders);
            $sheet->getStyle('D' . $healthIndexStart . ':E' . $nextHealthIndex)->applyFromArray($outlineBorders);
            $healthIndexStart = $healthIndexStart + 3;
        }

        $nextHealthIndex = $healthIndexStart + 3;
        $sheet->getStyle('C' . $healthIndexStart . ':C' . $nextHealthIndex)->applyFromArray($outlineBorders);
        $sheet->getStyle('D' . $healthIndexStart . ':E' . $nextHealthIndex)->applyFromArray($outlineBorders);
        $healthIndexStart = $nextHealthIndex + 14;

        for ($i = 0; $i < 3; $i++) {
            $nextHealthIndex = $healthIndexStart + 6;
            $sheet->getStyle('C' . $healthIndexStart . ':C' . $nextHealthIndex)->applyFromArray($outlineBorders);
            $sheet->getStyle('D' . $healthIndexStart . ':F' . $nextHealthIndex)->applyFromArray($outlineBorders);
            $healthIndexStart = $healthIndexStart + 7;
        }

        $healthIndexStart = $nextHealthIndex + 9;
        for ($i = 0; $i < 3; $i++) {
            $nextHealthIndex = $healthIndexStart + 8;
            $sheet->getStyle('C' . $healthIndexStart . ':C' . $nextHealthIndex)->applyFromArray($outlineBorders);
            $sheet->getStyle('D' . $healthIndexStart . ':F' . $nextHealthIndex)->applyFromArray($outlineBorders);
            $healthIndexStart = $healthIndexStart + 9;
        }
    }

    public function styleForOtherSchool(Worksheet $sheet, $outlineBorders, $maxClass)
    {
        $startCell = 7;
        for ($classCount = 0; $classCount < $maxClass; $classCount++) {
            $firstTimeStart = $startCell;
            $firstTimeEnd = $firstTimeStart + 5;

            $secondTimeStart = $firstTimeEnd + 1;
            $secondTimeEnd = $secondTimeStart + 5;
            $sheet->getStyle('A' . $firstTimeStart . ':B' . $firstTimeEnd)->applyFromArray($outlineBorders);
            $sheet->getStyle('C' . $firstTimeStart . ':F' . $firstTimeEnd)->applyFromArray($outlineBorders);
            $sheet->getStyle('A' . $secondTimeStart . ':B' . $secondTimeEnd)->applyFromArray($outlineBorders);
            $sheet->getStyle('C' . $secondTimeStart . ':F' . $secondTimeEnd)->applyFromArray($outlineBorders);
            $startCell = $secondTimeEnd + 8;
        }
    }

    public function styleForSucKhoeBatThuong(Worksheet $sheet, $allBorders)
    {
        $sheet->getStyle('B' . ($sheet->getHighestRow() - 14) . ':' . $sheet->getHighestDataColumn() . $sheet->getHighestRow())
            ->applyFromArray($allBorders);
    }

    public
    function columnWidths(): array
    {
        return [
            'A' => 1,
            'B' => 12,
            'C' => 15,
            'D' => 16,
            'E' => 16,
            'F' => 15
        ];
    }
}