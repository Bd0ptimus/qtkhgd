<?php

namespace App\Admin\Models\Exports\Abnormals;

use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportAbnormals implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $students;

    public function __construct(School $school, Collection $students)
    {
        $this->school = $school;
        $this->students = $students;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new BiaSheet("Tình trạng sức khỏe học sinh", $this->school);
        $sheets[] = new AbnormalsSheet('Diến biến bất thường sức khỏe', $this->students);

        return $sheets;
    }
}