<?php

namespace App\Admin\Models\Exports\MedicalDeclaration;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Admin\Models\Exports\BaseExport;

class StudentDeclaration extends BaseExport implements WithStyles, WithTitle
{
    protected $school;
    protected $data;

    public function __construct($school, $type)
    {
        $this->school = $school;
        $this->type = $type;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->school->school_name;
        $tableName = "DANH SÁCH KHAI BÁO Y TẾ HỌC SINH";
        $extraInformation = 'Ngày ' . Carbon::now()->format('d/m/Y');

        $data = [];
        if($this->type == 'full') $data = $this->school->students;
        elseif($this->type == 'special') {
            foreach($this->school->students as $item) {
                if(count($item->medicalDeclarations->all()) > 0) array_push($data, $item);
            }
        }

        return view('exports.medical_declaration.student_declaration', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $data,
            'school' => $this->school
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 20
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman',
            )
        ]);
    }

    public function title(): string
    {
        return 'Khai báo y tế học sinh';
    }
}