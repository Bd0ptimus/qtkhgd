<?php

namespace App\Admin\Models\Exports\MedicalDeclaration;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Admin\Models\Exports\BaseExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;

class SpecialStudents extends BaseExport implements WithStyles, WithTitle, WithEvents
{
    use RegistersEventListeners;
    protected $medicalDeclarations;
    protected $data;

    public function __construct($medicalDeclarations)
    {
        $this->medicalDeclarations = $medicalDeclarations;
    }

    public function view(): View
    {
        $tableName = "DANH SÁCH HỌC SINH BẤT THƯỜNG";

        return view('exports.medical_declaration.special_student', [
            'tableName' => $tableName,
            'medicalDeclarations' => $this->medicalDeclarations
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 15,
            'D' => 15,
            'E' => 20,
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
        self::defaultStyleOther($sheet, 4, $sheet->getHighestDataColumn(), $sheet->getHighestRow());
    }
}