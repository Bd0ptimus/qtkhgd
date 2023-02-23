<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportSchoolUsers extends BaseExport
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
        $tableName = Str::upper('Danh sách tài khoản');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.school_accounts', [
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
            'B' => 40,
            'C' => 35,
            'D' => 50,
            'E' => 25,
        ];
    }
}