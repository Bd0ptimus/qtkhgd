<?php

namespace App\Admin\Models\Exports\FoodTracking\FoodSample;

use App\Models\School;
use App\Models\TrackingFoodSample;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportFoodTrackingSample implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $trackingFoodSample;

    public function __construct(School $school, TrackingFoodSample $trackingFoodSample)
    {
        $this->school = $school;
        $this->trackingFoodSample = $trackingFoodSample;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new Sheet1($this->trackingFoodSample);
        $sheets[] = new Sheet2($this->trackingFoodSample);

        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('TheoDoiLuuHuyMau' . Carbon::now()->toDateString() . $this->school->school_name . '.xls');
    }
}