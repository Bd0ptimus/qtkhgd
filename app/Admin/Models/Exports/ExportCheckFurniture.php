<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportCheckFurniture extends BaseExport implements WithStyles
{
    protected $check_furniture;
    protected $check_furniture_details;
    protected $school_name;

    public function __construct($check_furnitures,  $school_name)
    {
        $this->check_furnitures = $check_furnitures;
        $this->school_name = $school_name;
    }

    public function view(): View
    {
        $extraLength = 9;
        $title = $this->school_name;
        $tableName = Str::upper('Đánh giá bàn ghế');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.check_furniture', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'check_furnitures' => $this->check_furnitures,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 35,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 35,
            'H' => 25,
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
}