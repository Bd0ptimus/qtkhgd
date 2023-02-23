<?php

namespace App\Admin\Models\Exports\FoodInspection;

use App\Models\FoodInspection;
use App\Models\School;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportFoodInspection implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $foodInspection;

    public function __construct(School $school, FoodInspection $foodInspection)
    {
        $this->school = $school;
        $this->foodInspection = $foodInspection;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new KiemThucTPTruocKhiCheBienSheet($this->foodInspection);
        $sheets[] = new KiemThucTPKhiCheBienSheet($this->foodInspection);
        $sheets[] = new KiemThucTruocKhiAnSheet($this->foodInspection);

        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('SoKiemThucBaBuoc' . Carbon::now()->toDateString() . $this->school->school_name . '.xls');
    }
}