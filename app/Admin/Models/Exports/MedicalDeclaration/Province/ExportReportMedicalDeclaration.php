<?php

namespace App\Admin\Models\Exports\MedicalDeclaration\Province;

use App\Models\District;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportReportMedicalDeclaration implements WithMultipleSheets
{
    use Exportable;

    protected $province;
    protected $districts;
    protected $data;

    public function __construct($province, $districts, $data)
    {
        $this->province = $province;
        $this->districts = $districts;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new TotalReportMedicalDeclaration($this->province, $this->data);
        $sheets[] = new SpecialStudents($this->districts);
        $sheets[] = new SpecialStaffs($this->districts);

        return $sheets;
    }
}