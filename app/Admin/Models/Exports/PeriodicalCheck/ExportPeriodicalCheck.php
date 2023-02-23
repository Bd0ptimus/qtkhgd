<?php

namespace App\Admin\Models\Exports\PeriodicalCheck;

use App\Admin\Models\Exports\BaseExport;
use App\Models\SchoolBranch;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportPeriodicalCheck extends BaseExport implements WithStyles
{
    protected $schoolBranch;
    protected $selectedMonth;

    public function __construct(SchoolBranch $schoolBranch, $selectedMonth)
    {
        $this->schoolBranch = $schoolBranch;
        $this->selectedMonth = $selectedMonth;
    }

    public function view(): View
    {
        $extraLength = 6;
        $title = optional($this->schoolBranch->school)->school_name;
        $tableName = Str::upper('Danh sách theo dõi sức khỏe định kỳ tháng '. $this->selectedMonth);
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.periodical_check', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
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
            'J' => 16,
            'K' => 16,
            'L' => 12,
            'M' => 12,
            'N' => 12,
            'O' => 12,
        ];
    }

    public function downloadAsName()
    {
        return $this->download('Cập nhật chỉ số sức khoẻ ' . optional($this->schoolBranch->school)->school_name . ' tháng ' . $this->selectedMonth . '.xls');
    }
}