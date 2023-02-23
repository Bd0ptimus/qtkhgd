<?php

namespace App\Admin\Models\Exports\Abnormals;

use App\Models\School;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportStaffAbnormals implements WithMultipleSheets
{
    use Exportable;

    protected $school;
    protected $staffs;

    public function __construct(School $school, Collection $staffs)
    {
        $this->school = $school;
        $this->staffs = $staffs;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new BiaSheet("Tình trạng sức khỏe nhân viên", $this->school);
        $sheets[] = new StaffAbnormalsSheet('Diến biến bất thường sức khỏe', $this->staffs);

        return $sheets;
    }

    public function downloadAsName()
    {
        return $this->download('Sổ theo dõi tình trạng sức khỏe nhân viên ' . $this->school->school_name . '.xls');
    }
}