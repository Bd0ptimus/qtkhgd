<?php

namespace App\Admin\Models\Exports\HealthProfile;

use App\Models\School;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportHealthProfile implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $student;

    public function __construct(School $school, Student $student)
    {
        $this->school = $school;
        $this->student = $student;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new BiaSheet($this->school, $this->student);
        $sheets[] = new ThongTinChungSheet($this->student);
        $sheets[] = new TheoDoiSucKhoeSheet($this->school, $this->student);
        $sheets[] = new KhamChuyenKhoaSheet($this->student);

        return $sheets;
    }

    public function downloadAsName()
    {
        if ($this->school->isTieuHoc()) {
            $name = "SoTheoDoiSucKhoe_Tieuhoc.xls";
        } else if ($this->school->isThcs()) {
            $name = "SoTheoDoiSucKhoe_Trunghoccoso.xls";
        } else if ($this->school->isThpt()) {
            $name = "SoTheoDoiSuckhoe_Trunghocphothong.xls";
        } else if ($this->school->isLC12()) {
            $name = "SoTheoDoiSuckhoe_LienCap1-2.xls";
        } else if ($this->school->isLC23()) {
            $name = "SoTheoDoiSuckhoe_LienCap2-3.xls";
        } else if ($this->school->isMamNon()) {
            $name = "SoTheoDoiSuckhoe_MamNon.xls";
        } else {
            $name = "SoTheoDoiSuckhoe_TTGD.xls";
        }
        return $this->download($name);
    }
}