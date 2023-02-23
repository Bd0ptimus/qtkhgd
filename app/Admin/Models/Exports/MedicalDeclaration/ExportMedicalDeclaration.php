<?php

namespace App\Admin\Models\Exports\MedicalDeclaration;

use App\Models\School;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportMedicalDeclaration implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $type;

    public function __construct(School $school, $type)
    {
        $this->school = $school;
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new StudentDeclaration($this->school, $this->type);
        $sheets[] = new StaffDeclaration($this->school, $this->type);

        return $sheets;
    }
}