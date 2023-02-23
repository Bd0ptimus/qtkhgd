<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportSchoolStaffs extends BaseExport
{
    protected $school;

    public function __construct($school)
    {
        $this->school = $school;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->school->school_name;
        $tableName = Str::upper('Danh sách nhân viên');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.school_staffs', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'school' => $this->school
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 18,
            'D' => 22,
            'E' => 15,
            'F' => 10,
            'G' => 12,
            'H' => 12,
            'I' => 12,
            'J' => 20,
            'K' => 15,
            'L' => 15,
            'M' => 18,
            'N' => 20,
            'O' => 18,
            'P' => 18,
            'Q' => 18,
            'R' => 20,
            'S' => 20,
        ];
    }
}