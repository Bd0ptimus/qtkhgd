<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportDistrictAccounts extends BaseExport
{
    protected $district;
    protected $schools;

    public function __construct($district, $schools)
    {
        $this->district = $district;
        $this->schools = $schools;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->district->name;
        $tableName = Str::upper('Danh sách tài khoản');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.district_account', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'district' => $this->district,
            'schools' => $this->schools
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