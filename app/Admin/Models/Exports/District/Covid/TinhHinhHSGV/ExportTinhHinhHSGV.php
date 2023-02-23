<?php

namespace App\Admin\Models\Exports\District\Covid\TinhHinhHSGV;

use App\Models\District;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportTinhHinhHSGV implements WithMultipleSheets
{
    use Exportable;

    protected $district;
    protected $date;
    protected $schoolType;

    public function __construct(District $district, $schoolType, $date)
    {
        $this->district = $district;
        $this->date = $date;
        $this->schoolType = $schoolType;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        switch($this->schoolType) {
            case 6: 
                $sheets[] = new BaoCaoMamNon($this->district, $this->date); break;
            case 1: 
                $sheets[] = new BaoCaoTieuHoc($this->district, $this->date); break;
            case 2:
                $sheets[] = new BaoCaoTHCS($this->district, $this->date); break;
        }
        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('Báo cáo tình hình học sinh giáo viên ' . Carbon::now()->toDateString() . $this->district->name . '.xls');
    }
}