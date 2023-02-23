<?php

namespace App\Admin\Models\Exports\District\Covid\BCChiTietNgay;

use App\Models\School;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportBCChiTietNgay implements WithMultipleSheets
{
    use Exportable;

    protected $district;
    protected $school;
    protected $date;

    public function __construct($district, $date)
    {
        $this->district = $district;
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new BCToanPhong($this->district, $this->date);

        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('Báo cáo theo dõi hàng ngày chi tiết ' . Carbon::now()->toDateString() . $this->district->name . '.xls');
    }
}