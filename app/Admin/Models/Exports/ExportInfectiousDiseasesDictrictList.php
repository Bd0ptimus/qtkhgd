<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportInfectiousDiseasesDictrictList extends BaseExport implements WithStyles
{
    protected $schools;
    protected $selectDistrict;

    public function __construct($districts)
    {
        $this->districts = $districts;
    }

    public function view(): View
    {
        $extraLength = 9;
        $province_name = $this->districts[0]->province->name ?? null;
        $title =  $province_name;
        $tableName = Str::upper('Báo cáo bệnh truyền nhiễm');
        $extraInformation = 'Ngày xuất báo cáo: ' . Carbon::now()->format('d/m/Y');

        return view('exports.infectious_diseases_district_list', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'districts' => $this->districts,
            'province_name' => $province_name,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 10,
            'C' => 30,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,

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