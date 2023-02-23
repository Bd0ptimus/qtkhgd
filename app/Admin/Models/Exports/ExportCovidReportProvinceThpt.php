<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportCovidReportProvinceThpt extends BaseExport
{
    protected $province;
    protected $reports;
    protected $from_date;
    protected $to_date;

    public function __construct($province, $reports, $from_date, $to_date)
    {
        $this->province = $province;
        $this->reports = $reports;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->province->name;
        $tableName = 'THỐNG KÊ TÌNH HÌNH PHÒNG, CHỐNG DỊCH BỆNH SARS-CoV-2 TỪ NGÀY '.$this->from_date.' đến ngày '.$this->to_date;
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.covid_export_province_thpt', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'reports' => $this->reports,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
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
        return $this->download('Thống kê tình hình phòng chữa bệnh Covid sở khối THPT.xls');
    }
}