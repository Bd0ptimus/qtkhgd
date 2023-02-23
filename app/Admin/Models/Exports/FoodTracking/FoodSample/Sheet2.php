<?php

namespace App\Admin\Models\Exports\FoodTracking\FoodSample;

use App\Models\TrackingFoodSample;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Sheet2 implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $trackingFoodSample;
    protected $foodSamples;

    public function __construct(TrackingFoodSample $trackingFoodSample)
    {
        $this->trackingFoodSample = $trackingFoodSample;
    }

    public function view(): View
    {
        $this->foodSamples = $this->trackingFoodSample->food_samples;

        return view('exports.food_tracking.food_sample.sheet2', [
            'foodSamples' => $this->foodSamples,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Sheet2';
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
                'size' => 11,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
            ->applyFromArray($allBorders);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 17,
            'G' => 15,
            'H' => 18,
            'I' => 18,
            'J' => 15,
            'K' => 20,
            'L' => 20
        ];
    }
}