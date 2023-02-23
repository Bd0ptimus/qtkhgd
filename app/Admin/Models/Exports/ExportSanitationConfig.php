<?php

namespace App\Admin\Models\Exports;

use App\Models\School;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportSanitationConfig extends BaseExport implements WithStyles
{
    protected $title;
    protected $school;
    protected $schoolSanitation;

    public function __construct(School $school, $schoolSanitation)
    {
        $this->school = $school;
        $this->schoolSanitation = $schoolSanitation;
        $this->title = "Thông tin vệ sinh học đường ngày " . $schoolSanitation['updated_date'];
    }

    public function view(): View
    {
        $extraLength = 7;
        $tableName = 'THÔNG TIN VỆ SINH HỌC ĐƯỜNG NGÀY '.$this->schoolSanitation['updated_date'];
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.sanitation_config', [
            'extraLength' => $extraLength,
            'title' => Str::upper($this->school->school_name),
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'schoolSanitation' => $this->schoolSanitation
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 15,
            'K' => 15,
            'L' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle('A1:L20')->applyFromArray([
            'font' => array(
                'size' => 13,
                'bold' => true
            )
        ]);
    }

    public function downloadAsName()
    {
        return $this->download('Thông tin vệ sinh học đường ngày.xls');
    }
}