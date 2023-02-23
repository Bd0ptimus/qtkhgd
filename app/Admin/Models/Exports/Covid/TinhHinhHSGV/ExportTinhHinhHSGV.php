<?php

namespace App\Admin\Models\Exports\Covid\TinhHinhHSGV;

use App\Models\School;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportTinhHinhHSGV implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $date;

    public function __construct(School $school, $date)
    {
        $this->school = $school;
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        switch($this->school->school_type) {
            case 6: 
                $sheets[] = new BaoCaoMamNon($this->school, $this->date); break;
            case 1: 
                $sheets[] = new BaoCaoTieuHoc($this->school, $this->date); break;
            case 2:
                $sheets[] = new BaoCaoTHCS($this->school, $this->date); break;
        }
        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('Báo cáo tình hình học sinh giáo viên' . Carbon::now()->toDateString() . $this->school->school_name . '.xls');
    }
}