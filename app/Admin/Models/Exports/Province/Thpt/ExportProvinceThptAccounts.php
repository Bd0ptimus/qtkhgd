<?php

namespace App\Admin\Models\Exports\Province\Thpt;

use App\Admin\Models\Exports\BaseExport;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportProvinceThptAccounts extends BaseExport
{
    protected $province;
    protected $schools;

    public function __construct($province, $schools)
    {
        $this->province = $province;
        $this->schools = $schools;
    }

    public function view(): View
    {
        $extraLength = 5;
        $title = $this->province->name;
        $tableName = Str::upper('Danh sách tài khoản');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.district_account', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'district' => $this->province,
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