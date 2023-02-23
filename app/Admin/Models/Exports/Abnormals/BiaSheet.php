<?php

namespace App\Admin\Models\Exports\Abnormals;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BiaSheet implements FromView, WithTitle, WithStyles
{
    protected $title;
    protected $school;

    public function __construct($title, School $school)
    {
        $this->title = $title;
        $this->school = $school;
    }

    public function view(): View
    {
        return view('exports.components.bia', [
            'title' => Str::upper($this->title),
            'school' => $this->school
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