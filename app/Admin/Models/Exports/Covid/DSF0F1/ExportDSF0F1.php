<?php

namespace App\Admin\Models\Exports\Covid\DSF0F1;

use App\Models\School;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportDSF0F1 implements WithMultipleSheets
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

        $sheets[] = new BCCovidHs($this->school, $this->date);
        $sheets[] = new BCCovidGv($this->school, $this->date);

        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('Báo cáo F0,F1' . Carbon::now()->toDateString() . $this->school->school_name . '.xls');
    }
}