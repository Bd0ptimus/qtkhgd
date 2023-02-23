<?php

namespace App\Admin\Models\Exports\HealthProfile;

use App\Models\School;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BiaSheet implements FromView, WithTitle, WithStyles, WithColumnWidths
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
        if ($this->school->isTieuHoc()) {
            $title = 'Dành cho học sinh từ lớp 1 đến lớp 5';
        } else if ($this->school->isThcs()) {
            $title = 'Dành cho học sinh từ lớp 6 đến lớp 9';
        } else if ($this->school->isThpt()) {
            $title = 'Dành cho học sinh từ lớp 10 đến lớp 12';
        } else if ($this->school->isLC12()) {
            $title = 'Dành cho học sinh Liên cấp 1-2';
        } else if ($this->school->isLC23()) {
            $title = 'Dành cho học sinh Liên cấp 2-3';
        } else if ($this->school->isMamNon()) {
            $title = "Dành cho học sinh cở sở giáo dục mầm non<br>(từ 3 tháng tuổi đến &lt; 6 tuổi)";
        } else if ($this->school->isTTGD()) {
            $title = 'Dành cho học sinh TTGD Thường Xuyên';
        } else {
            $title = "";
        }

        return view('exports.health_profile.bia', [
            'school' => $this->school,
            'student' => $this->student,
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Bìa';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman',
                'size' => 12,
            )
        ]);
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => array(
                'bold' => true
            )
        ]);
        $sheet->getStyle('A13:F13')->applyFromArray([
            'font' => array(
                'bold' => true
            )
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 1,
            'B' => 1.5,
            'C' => 22,
            'D' => 12,
            'E' => 10,
            'F' => 22,
        ];
    }
}