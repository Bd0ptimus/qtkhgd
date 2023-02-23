<?php

namespace App\Admin\Models\Exports\Equipment;

use App\Admin\Models\Exports\BaseExport;
use App\Models\School;
use App\Models\SchoolBranch;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportHistoryImportEquipment extends BaseExport
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
        $tableName = Str::upper('Danh sách lịch sử nhập thiết bị');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.equipment.history_import_equipment', [
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
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 18,
            'I' => 18,
            'J' => 15,
        ];
    }
}