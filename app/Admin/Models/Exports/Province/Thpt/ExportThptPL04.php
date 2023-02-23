<?php

namespace App\Admin\Models\Exports\Province\Thpt;

use App\Admin\Models\Exports\BaseExport;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportThptPL04 extends BaseExport
{
    protected $province;
    protected $data;

    public function __construct(Province $province, $data)
    {
        $this->province = $province;
        $this->data = $data;
    }

    public function view(): View
    {
        $extraLength = 3;
        $title = $this->province->name;
        $tableName = Str::upper('Báo cáo tổng hợp PL04 THPT');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->toDateString();

        return view('exports.province.thpt.pl04', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $this->data,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 15,
            'G' => 15,
            'H' => 18,
            'I' => 12,
            'J' => 12,
            'K' => 12,
            'L' => 12,
            'M' => 12,
            'N' => 12,
        ];
    }
}