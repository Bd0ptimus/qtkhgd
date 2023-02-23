<?php

namespace App\Admin\Models\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ExportSpecialistTest extends BaseExport
{
    protected $school;
    protected $data;

    public function __construct($school, $data)
    {
        $this->school = $school;
        $this->data = $data;
    }

    public function view(): View
    {
        $extraLength = 2;
        $title = $this->school->school_name;
        $tableName = Str::upper('Khám chuyên khoa học sinh');
        $extraInformation = 'Ngày xuất danh sách: ' . Carbon::now()->format('d/m/Y');

        return view('exports.specialist_test_student', [
            'extraLength' => $extraLength,
            'title' => $title,
            'tableName' => $tableName,
            'extraInformation' => $extraInformation,
            'data' => $this->data,
            'school' => $this->school
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 15,
            'D' => 35,
            'E' => 35,
            'F' => 35,
            'G' => 35,
            'H' => 35,
        ];
    }
}