<?php

namespace App\Admin\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TeacherWeeklyLessonExport implements FromView, ShouldAutoSize, WithEvents
{
    private array $data;

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('admin.export.teacher_weekly_lesson', $this->data);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $styleFontTextAll = [
            'font' => [
                'name' => 'Times New Roma',
                'size' => 13,
            ],
        ];

        $styleHeader = [
            'font' => [
                'bold' => true,
            ],
        ];

        $styleTextCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $rowCount = $this->data['teacherWeeklyLessons']->count();
        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleFontTextAll,
                $styleHeader,
                $styleBorder,
                $styleTextCenter,
                $rowCount
            ) {
                $event->sheet->styleCells('A1:I' . ($rowCount + 4), $styleFontTextAll);
                $event->sheet->styleCells('A2', $styleHeader);
                $event->sheet->styleCells('A2', $styleTextCenter);
                $event->sheet->styleCells('A4:A' . ($rowCount + 4), $styleHeader);
                $event->sheet->styleCells('A4:I4', $styleHeader);
                $event->sheet->getDelegate()->getStyle('A4:I' . ($rowCount + 4))->applyFromArray($styleBorder);
            },
        ];
    }
}
