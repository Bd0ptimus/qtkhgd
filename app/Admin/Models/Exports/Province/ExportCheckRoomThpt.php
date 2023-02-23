<?php

namespace App\Admin\Models\Exports\Province;

use App\Admin\Models\Exports\BaseExport;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportCheckRoomThpt extends BaseExport implements WithStyles
{
    protected $province;
    protected $data;

    public function __construct(Province $province, $data)
    {
        $this->province = $province;
        $this->data = $data;
    }

    public function view(): View
    {
        $extraLength = 9;
        $title = $this->province->name;
        $tableName = Str::upper('Đánh giá chất lượng phòng học');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->toDateString();

        return view('exports.province.check_room_thpt', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $this->data
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
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