<?php

namespace App\Admin\Models\Exports\Report;

use App\Admin\Models\Exports\BaseExport;
use App\Models\SchoolBranch;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportTongHopKiemTraSucKhoeTheoThang extends BaseExport implements WithStyles
{
    protected $schoolBranch;
    protected $selectedMonth;
    protected $listData;

    public function __construct(SchoolBranch $schoolBranch, $selectedMonth, $listData)
    {
        $this->schoolBranch = $schoolBranch;
        $this->selectedMonth = $selectedMonth;
        $this->listData = $listData;
    }

    public function view(): View
    {
        $extraLength = 6;
        $title = optional($this->schoolBranch->school)->school_name;
        $tableName = Str::upper('Tổng hợp kiểm tra sức khoẻ theo tháng ' . $this->selectedMonth);
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.report.tonghopsuckhoetheothang', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'listData' => $this->listData,
            'school' => $this->schoolBranch->school
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
        if ($this->schoolBranch->school->school_type == 6) {
            return [
                'A' => 13,
                'B' => 15,
                'C' => 13,
                'D' => 13,
                'E' => 13,
                'F' => 13,
                'G' => 13,
                'H' => 13,
                'I' => 13,
                'J' => 13,
                'K' => 13,
                'L' => 13,
                'M' => 13,
                'N' => 13,
                'O' => 13,
                'P' => 13,
                'Q' => 13,
                'R' => 13,
                'S' => 13,
                'T' => 13,
                'U' => 13,
                'V' => 13,
                'W' => 13,
                'X' => 13,
                'Z' => 13,
                'Y' => 13,
                'AA' => 13,
                'AB' => 13,
                'AC' => 13,
                'AD' => 13,
                'AE' => 13,
                'AF' => 13
            ];
        }
        return [
            'A' => 13,
            'B' => 15,
            'C' => 13,
            'D' => 13,
            'E' => 13,
            'F' => 13,
            'G' => 13,
            'H' => 13,
            'I' => 13,
            'J' => 13,
            'K' => 13,
            'L' => 13,
            'M' => 13,
            'N' => 13,
        ];
    }

    public function downloadAsName()
    {
        return $this->download('Tổng hợp kiểm tra sức khỏe hàng tháng ' . optional($this->schoolBranch->school)->school_name . ' tháng ' . $this->selectedMonth . '.xls');
    }
}