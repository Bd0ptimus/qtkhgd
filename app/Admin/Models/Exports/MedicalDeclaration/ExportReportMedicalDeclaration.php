<?php

namespace App\Admin\Models\Exports\MedicalDeclaration;

use App\Models\School;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportReportMedicalDeclaration implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $medicalDeclarations;
    protected $students = [];
    protected $staffs = [];

    public function __construct(School $school, $medicalDeclarations)
    {
        $this->school = $school;
        $this->medicalDeclarations = $medicalDeclarations;
        foreach ($this->medicalDeclarations as $medicalDeclaration) {
            $obj = $medicalDeclaration->object;
            if ($obj) {
                if (isset($obj->student_code)) {
                    array_push($this->students, $medicalDeclaration);
                }elseif (isset($obj->staff_code)) {
                    array_push($this->staffs, $medicalDeclaration);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new TotalReportMedicalDeclaration($this->school, $this->medicalDeclarations);
        $sheets[] = new SpecialStudents($this->students);
        $sheets[] = new SpecialStaffs($this->staffs);

        return $sheets;
    }
}