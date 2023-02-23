<?php

namespace App\Admin\Models\Exports\TheoDoiSucKhoeBatThuong;

use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportTheoDoiSucKhoeBatThuong implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $abnormalData;

    public function __construct(School $school, Collection $abnormalData)
    {
        $this->school = $school;
        $this->abnormalData = $abnormalData;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new BiaSheet($this->school);
        foreach ($this->abnormalData as $title => $data) {
            $sheets[] = new SucKhoeSheet($title  , $data);
        }

        return $sheets;
    }
}