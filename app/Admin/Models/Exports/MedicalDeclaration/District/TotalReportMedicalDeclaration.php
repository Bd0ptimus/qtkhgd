<?php

namespace App\Admin\Models\Exports\MedicalDeclaration\District;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Admin\Models\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class TotalReportMedicalDeclaration extends BaseExport implements WithStyles, WithTitle, WithEvents
{
    use RegistersEventListeners;
    protected $data;
    protected $district;

    public function __construct($district, $data)
    {
        $this->district = $district;
        $this->data = $data;
    }

    public function view(): View
    {
        $tableName = "BÁO CÁO KẾT QUẢ KHAI BÁO Y TẾ";
        $extraInformation = 'Ngày ' . Carbon::now()->format('d/m/Y');

        return view('exports.medical_declaration.district.total_report_medical_declaration', [
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'district' => $this->district,
            'data' => $this->data
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
        self::defaultStyleHeader($sheet, 7);
        self::defaultStyleOther($sheet, 7, $sheet->getHighestDataColumn(), $sheet->getHighestRow() - 3, ['is_align' => false]);
    }
}