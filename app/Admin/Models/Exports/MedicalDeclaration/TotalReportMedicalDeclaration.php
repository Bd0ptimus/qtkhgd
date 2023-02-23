<?php

namespace App\Admin\Models\Exports\MedicalDeclaration;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Admin\Models\Exports\BaseExport;
use App\Models\MedicalDeclaration;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class TotalReportMedicalDeclaration extends BaseExport implements WithStyles, WithTitle, WithEvents
{
    use RegistersEventListeners;
    protected $school;
    protected $medicalDeclarations;
    protected $data;

    public function __construct($school, $medicalDeclarations)
    {
        $this->school = $school;
        $this->medicalDeclarations = $medicalDeclarations;
    }

    public function view(): View
    {
        $tableName = "BÁO CÁO KẾT QUẢ KHAI BÁO Y TẾ";
        $extraInformation = 'Ngày ' . Carbon::now()->format('d/m/Y');
        
        $data = [];
        foreach (MedicalDeclaration::BOOLEAN_FIELDS as $field) {
            $data[$field] = 0;
        }
        foreach ($this->medicalDeclarations as $medicalDeclaration) {
            foreach ($data as $kdt => $vdt) {
                if ($medicalDeclaration->$kdt) {
                    $data[$kdt]++;
                }
            }
        }

        return view('exports.medical_declaration.total_report_medical_declaration', [
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $data,
            'school' => $this->school
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30
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
        return 'Tổng hợp';
    }

    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        self::defaultStyleHeader($sheet, 6);
        self::defaultStyleOther($sheet, 6, $sheet->getHighestDataColumn(), $sheet->getHighestRow() - 3);
    }
}