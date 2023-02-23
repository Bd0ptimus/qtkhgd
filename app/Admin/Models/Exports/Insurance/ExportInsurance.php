<?php

namespace App\Admin\Models\Exports\Insurance;

use App\Admin\Models\Exports\BaseExport;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportInsurance extends BaseExport implements WithColumnFormatting, WithStyles
{
    protected $class;

    public function __construct(SchoolClass $class)
    {
        $this->class = $class;
    }

    public function view(): View
    {
        $extraLength = 4;
        $title = optional($this->class->school)->school_name;
        $tableName = Str::upper('Danh sách học sinh tham gia bảo hiểm lớp '.$this->class->class_name);
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.insurance', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'students' => $this->class->students,
        ]);
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

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 22,
            'D' => 25,
            'E' => 30,
            'F' => 17,
            'G' => 17,
            'H' => 17,
            'I' => 20,
            'J' => 19,
            'K' => 17,
        ];
    }

    public function downloadAsName()
    {
        return $this->download('Danh sách học sinh tham gia bảo hiểm lớp ' . $this->class->class_name . '.xls');
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}