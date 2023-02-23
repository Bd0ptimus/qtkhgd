<?php

namespace App\Admin\Models\Exports\PeriodicalCheck;

use App\Admin\Models\Exports\BaseExport;
use App\Models\SchoolBranch;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ExportCsdlNganh extends BaseExport implements WithStyles
{
    protected $schoolBranch;
    protected $selectedMonth;

    public function __construct(SchoolBranch $schoolBranch, $selectedMonth)
    {
        $this->schoolBranch = $schoolBranch;
        $this->selectedMonth = $selectedMonth;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $borderStyles = [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ];

                $sheet = $event->sheet->getDelegate();

                $header = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . '1');
                $header->getFont()->setBold(true);

                $other = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                /** @var Style $item */
                foreach ([$header, $other] as $item) {
                    $item->applyFromArray([
                        'borders' => $borderStyles
                    ]);
                    $item->getAlignment()
                        ->setWrapText(true)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }

    public function view(): View
    {
        return view('exports.csdl_nganh', [
            'students' => $this->schoolBranch->students
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
            'A' => 6,
            'B' => 18,
            'C' => 20,
            'D' => 13,
            'E' => 12,
            'F' => 12,
            'G' => 18,
            'H' => 20,
            'I' => 16,
            'J' => 16
        ];
    }

    public function downloadAsName()
    {
        return $this->download('Chỉ số sức khoẻ - CSDL Ngành - ' . optional($this->schoolBranch->school)->school_name . ' tháng ' . $this->selectedMonth . '.xls');
    }
}