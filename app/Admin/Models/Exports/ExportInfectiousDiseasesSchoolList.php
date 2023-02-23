<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportInfectiousDiseasesSchoolList extends BaseExport implements WithStyles
{
    protected $schools;
    protected $selectDistrict;

    public function __construct($schools, $selectDistrict)
    {
        $this->schools = $schools;
        $this->selectDistrict = $selectDistrict;
    }

    public function view(): View
    {
        $extraLength = 9;
        $district_name = $this->selectDistrict->name ?? null;
        $province_name = $this->schools[0]->district->province->name ?? null;
        $title = $district_name ? ($district_name.' - ') : null;
        $title .=  $province_name;
        $tableName = Str::upper('Báo cáo bệnh truyền nhiễm');
        $extraInformation = 'Ngày xuất báo cáo: ' . Carbon::now()->format('d/m/Y');

        return view('exports.infectious_diseases_school_list', [
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
            'E' => 35,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
            'N' => 10,
            'O' => 10,
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