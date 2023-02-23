<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportCovidReportDistrict extends BaseExport
{
    protected $district;
    protected $data;
    protected $from_date;
    protected $to_date;

    public function __construct($district, $data, $from_date, $to_date)
    {
        $this->district = $district;
        $this->data = $data;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->district->name;
        $tableName = 'THỐNG KÊ TÌNH HÌNH PHÒNG, CHỐNG DỊCH BỆNH SARS-CoV-2 TỪ NGÀY '.$this->from_date.' đến ngày '.$this->to_date;
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.covid_export_district', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $this->data,
            'district' => $this->district,
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
        return $this->download('Thống kê tình hình phòng chữa bệnh Covid.xls');
    }
}