<?php

namespace App\Admin\Models\Exports\FoodTracking;

use App\Models\School;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Str;

class ExportFoodEntry implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $school;
    protected $trackingFoodEntries;

    public function __construct(School $school, Collection $trackingFoodEntries)
    {
        $this->school = $school;
        $this->trackingFoodEntries = $trackingFoodEntries;
    }

    public function view(): View
    {
        return view('exports.food_tracking.food_entry', [
            'trackingFoodEntries' => $this->trackingFoodEntries,
            'school_name' => Str::upper($this->school->school_name)
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Sheet1';
    }

    public function styles(Worksheet $sheet)
    {
        $allBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];

        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A3:' . $sheet->getHighestDataColumn() . ($sheet->getHighestDataRow() - 4))
            ->applyFromArray($allBorders);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 15,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 30,
            'i' => 25,
        ];
    }

    public function downloadAsName()
    {
        return $this->download('TheoDoiNhapThucPham' . Carbon::now()->toDateString() . $this->school->school_name . '.xls');
    }
}