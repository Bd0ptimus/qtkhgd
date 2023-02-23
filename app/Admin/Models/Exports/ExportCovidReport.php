<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportCovidReport extends BaseExport
{
    protected $school;
    protected $data;

    public function __construct($school, $data)
    {
        $this->school = $school;
        $this->data = $data;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->school->school_name;
        $tableName = 'THỐNG KÊ TÌNH HÌNH PHÒNG, CHỐNG DỊCH BỆNH SARS-CoV-2 TỪ NGÀY '.$this->data['from_date'].' đến ngày '.$this->data['to_date'];
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.covid_export', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $this->data,
            'school' => $this->school
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 30,
        ];
    }

    public function downloadAsName()
    {
        return $this->download('Thống kê tình hình phòng chữa bệnh Covid.xls');
    }
}