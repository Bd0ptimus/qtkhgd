<?php

namespace App\Admin\Models\Exports\MedicalDeclaration\District;

use App\Models\School;
use App\Models\District;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportReportMedicalDeclaration implements WithMultipleSheets
{
    use Exportable;

    protected $district;
    protected $schools;
    protected $data;

    public function __construct(District $district, $schools, $data)
    {
        $this->district = $district;
        $this->schools = $schools;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new TotalReportMedicalDeclaration($this->district, $this->data);
        $sheets[] = new SpecialStudents($this->schools);
        $sheets[] = new SpecialStaffs($this->schools);

        return $sheets;
    }
}