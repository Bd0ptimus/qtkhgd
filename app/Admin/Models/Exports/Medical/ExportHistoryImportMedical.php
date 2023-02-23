<?php

namespace App\Admin\Models\Exports\Medical;

use App\Admin\Models\Exports\BaseExport;
use App\Models\School;
use App\Models\SchoolBranch;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportHistoryImportMedical extends BaseExport
{
    protected $school;
    protected $schoolBranch;

    public function __construct(School $school, SchoolBranch $schoolBranch)
    {
        $this->school = $school;
        $this->schoolBranch = $schoolBranch;
    }

    public function view(): View
    {
        $extraLength = 4;
        $title = $this->school->school_name;
        $tableName = Str::upper('Danh sách lịch sử nhập thuốc');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.medicine.history_import_medical', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'schoolBranch' => $this->schoolBranch
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 18,
            'D' => 20,
            'E' => 18,
            'F' => 20,
            'G' => 20,
            'H' => 12,
            'I' => 12,
            'J' => 12,
            'K' => 15,
        ];
    }
}