<?php

namespace App\Admin\Models\Exports\FoodTracking\FoodSample;

use App\Models\TrackingFoodSample;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Sheet1 implements FromView, WithTitle, WithStyles, WithColumnWidths
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
        return view('exports.food_tracking.food_sample.sheet1', [
            'trackingFoodSample' => $this->trackingFoodSample,
            'foodSamples' => $this->foodSamples,
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
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman',
            )
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 70,
        ];
    }
}