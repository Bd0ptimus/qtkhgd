<?php

namespace App\Admin\Models\Exports\SoTheoDoiSucKhoe;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BiaSheet implements FromView, WithTitle, WithStyles
{
    protected $school;

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    public function view(): View
    {
        return view('exports.components.bia', [
            'school' => $this->school,
            'title' => $this->title()
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
            )
        ]);
        $sheet->getStyle('A2:I3')->applyFromArray([
            'font' => array(
                'size' => 13,
                'bold' => true
            )
        ]);
        $sheet->getStyle('A15:I15')->applyFromArray([
            'font' => array(
                'size' => 18,
                'bold' => true
            )
        ]);
        $sheet->getStyle('B34:F36')->applyFromArray([
            'font' => array(
                'size' => 13,
                'bold' => true
            )
        ]);
    }
}