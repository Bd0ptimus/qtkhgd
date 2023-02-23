<?php

namespace App\Admin\Models\Exports\QuanLyDichBenh;

use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportQuanLyDichBenh implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $students;

    public function __construct(School $school, Collection $students)
    {
        $this->school = $school;
        $this->students = $students;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new BiaSheet($this->school);
        $sheets[] = new QuanLyDichBenhSheet('Quản lý dịch bệnh', $this->students);

        return $sheets;
    }
}