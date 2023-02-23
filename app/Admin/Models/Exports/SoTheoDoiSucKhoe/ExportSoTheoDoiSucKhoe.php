<?php

namespace App\Admin\Models\Exports\SoTheoDoiSucKhoe;

use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportSoTheoDoiSucKhoe implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $abnormalData;
    protected $sheets = [1 => 'Suy dinh dưỡng', 2 => 'Thừa cân,béo phì', 3 => 'Bệnh răng miệng',
    4 => 'Bệnh về mắt', 5 => 'Tim mạch', 6 => 'Hô hấp', 7 => 'Tâm thần-thần kinh',
    8 => 'Bệnh cơ xương khớp'];

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
        foreach ($this->sheets as $name) {
            $sheets[] = new SucKhoeSheet($name  , $this->abnormalData[$name] ?? new \Illuminate\Support\Collection());
        }

        return $sheets;
    }
}