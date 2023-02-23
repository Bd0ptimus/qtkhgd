<?php

namespace App\Admin\Models\Exports\MedicalDeclaration\Province\THPT;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Admin\Models\Exports\BaseExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;

class SpecialStudents extends BaseExport implements WithStyles, WithTitle, WithEvents
{
    use RegistersEventListeners;
    protected $districts;
    protected $data;

    public function __construct($districts)
    {
        $this->districts = $districts;
    }

    public function view(): View
    {
        $tableName = "DANH SÁCH HỌC SINH BẤT THƯỜNG";

        return view('exports.medical_declaration.province.special_student', [
            'tableName' => $tableName,
            'districts' => $this->districts
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 30,
            'D' => 15,
            'E' => 15,
            'F' => 20,
            'G' => 20
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

    public function title(): string
    {
        return 'Danh sách học sinh bất thường';
    }

    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        self::defaultStyleHeader($sheet, 4);
        self::defaultStyleOther($sheet, 4, $sheet->getHighestDataColumn(), $sheet->getHighestRow(), ['is_align' => false]);
    }
}