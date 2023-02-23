<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportManageInsurance extends BaseExport implements WithStyles
{
    protected $schools;


    public function __construct($schools)
    {
        $this->schools = $schools;
    }

    public function view(): View
    {
        $extraLength = 9;
        $district_name = $this->schools[0]->district->name ?? null;
        $province_name = $this->schools[0]->district->province->name ?? null;
        $title = $district_name ? ($district_name.' - ') : null;
        $title .=  $province_name;
        $tableName = Str::upper('Quản lý bảo hiểm y tế');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.manage_insurance', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'schools' => $this->schools,
            'district_name' => $district_name,
            'province_name' => $province_name,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 15,
            'C' => 35,
            'D' => 20,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25, 
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