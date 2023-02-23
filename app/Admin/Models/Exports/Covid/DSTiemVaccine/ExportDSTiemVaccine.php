<?php

namespace App\Admin\Models\Exports\Covid\DSTiemVaccine;

use App\Models\School;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportDSTiemVaccine implements WithMultipleSheets
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
        return $this->download('Báo cáo tiêm chủng' . Carbon::now()->toDateString() . $this->school->school_name . '.xls');
    }
}