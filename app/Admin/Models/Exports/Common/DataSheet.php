<?php

namespace App\Admin\Models\Exports\Common;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DataSheet implements FromView, WithTitle, WithEvents
{
    protected $dataRecords;
    protected $rowFormats = [];

    public function __construct(array $dataRecords)
    {
        $this->dataRecords = $dataRecords;
    }

    public function view(): View
    {
        return view('exports.components.data', [
            'dataRecords' => $this->dataRecords
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach ($this->rowFormats as $rowNumber => $rowFormat) {
                    $sheet->getStyle('A' . $rowNumber . ':' . $sheet->getHighestDataColumn() . $rowNumber)
                        ->getNumberFormat()
                        ->setFormatCode($rowFormat);
                }

                $other = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());

                $other->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $other->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function withRowFormat($rowFormats = []): DataSheet
    {
        $this->rowFormats = $rowFormats;

        return $this;
    }
}