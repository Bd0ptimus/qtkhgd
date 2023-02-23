<?php

namespace App\Admin\Models\Exports;

use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class ExportManageDistrictInsurance extends BaseExport implements WithStyles
{
    protected $province;
    protected $districts;

    public function __construct(Province $province, $districts)
    {
        $this->province = $province;
        $this->districts = $districts;
    }

    public function view(): View
    {
        $extraLength = 7;
        $title = $this->province->name;
        $tableName = Str::upper('Quản lý bảo hiểm y tế');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.manage_district_insurance', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'districts' => $this->districts,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 35,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
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