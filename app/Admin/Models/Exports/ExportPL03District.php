<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportPL03District extends BaseExport implements WithStyles
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
        $extraLength = 13;
        $title = $this->district;
        $tableName = Str::upper('Báo cáo tổng hợp PL03');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');
        return view('exports.pl03_district', [
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

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman',
            )
        ]);
    }
}