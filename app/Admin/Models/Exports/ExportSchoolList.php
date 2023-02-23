<?php

namespace App\Admin\Models\Exports;

use App\Admin\Admin;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportSchoolList extends BaseExport implements WithStyles
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
        if(Admin::user()->inRoles(['tuyen-ttyt-ward'])){
            if(count($this->schools) > 0) $title = $this->schools[0]->ward->name . ' - ' . $title;
        }
        $tableName = Str::upper('Danh sách trường học');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.school_list', [
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
            'F' => 20,
            'G' => 20,
            'H' => 30,
            'I' => 20,
            
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