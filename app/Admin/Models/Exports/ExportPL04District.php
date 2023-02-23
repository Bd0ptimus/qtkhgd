<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportPL04District extends BaseExport
{
    protected $schools;
    protected $data;

    public function __construct($district, $data)
    {
        $this->district = $district;
        $this->data = $data;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->district;
        $tableName = Str::upper('Báo cáo tổng hợp PL04');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.pl04_district', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $this->data,
            'district' => $this->district
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 40,
            'C' => 35,
            'D' => 50,
            'E' => 25,
        ];
    }
}