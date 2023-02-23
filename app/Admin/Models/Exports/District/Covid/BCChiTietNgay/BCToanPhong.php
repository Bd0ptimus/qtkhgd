<?php

namespace App\Admin\Models\Exports\District\Covid\BCChiTietNgay;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BCToanPhong implements FromView, WithTitle, WithStyles, WithColumnWidths
{
    protected $mnSchools;
    protected $thSchools;
    protected $thcsSchools;
    protected $staffs;
    protected $date;

    public function __construct($district, $date)
    {
        $this->district = $district;
        $this->date = $date;
        $this->mnSchools = School::with(['classes', 'classes.students', 'classes.students.vaccineHistory','classes.students.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        },'staffs', 'staffs.vaccineHistory','staffs.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        }])->where(['school_type' => 6, 'district_id' => $this->district->id])->get();

        $this->thSchools = School::with(['classes', 'classes.students', 'classes.students.vaccineHistory','classes.students.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        },'staffs', 'staffs.vaccineHistory','staffs.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        }])->where(['school_type' => 1, 'district_id' => $this->district->id])->get();

        $this->thcsSchools = School::with(['classes', 'classes.students', 'classes.students.vaccineHistory','classes.students.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        },'staffs', 'staffs.vaccineHistory','staffs.dailyHealthChecks'=> function($query) use ($date) {
            $query->where('date', $date);
        }])->where(['school_type' => 2, 'district_id' => $this->district->id])->get();
    }

    public function view(): View
    {
        $summary = ['mn' => [],'th' => [], 'thcs' => []];
        $mn = $th = $thcs = [];
        
        foreach($this->mnSchools as $school) {
            $data = $school->tongHopTiemChungVaKiemTraSK($school->staffs, $school->classes); 
            array_push($mn, $data);
        }
        $summary['mn'] = $this->district->tongHopTiemChungVaKiemTraSK($mn);

        foreach($this->thSchools as $school) {
            $data = $school->tongHopTiemChungVaKiemTraSK($school->staffs, $school->classes); 
            array_push($th, $data);
        }
        $summary['th'] = $this->district->tongHopTiemChungVaKiemTraSK($th);

        foreach($this->thcsSchools as $school) {
            $data = $school->tongHopTiemChungVaKiemTraSK($school->staffs, $school->classes); 
            array_push($thcs, $data);
        }
        $summary['thcs'] = $this->district->tongHopTiemChungVaKiemTraSK($thcs);
        
        $summary['total'] = $this->district->tongHopTiemChungVaKiemTraSK($summary);
        return view('exports.district.covid_reports.bc_chi_tiet_ngay', [
            'district' => $this->district,
            'summary' => $summary,
            'date' => $this->date
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Báo cáo Covid 19 (GV)";
    }

    public function styles(Worksheet $sheet)
    {
        $allBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];
        $endOfProcessingItems = (5 + 2 + 5);

        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman',
            )
        ]);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:' . $sheet->getHighestDataColumn() . $endOfProcessingItems)
            ->applyFromArray($allBorders);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 8,
            'C' => 8,
            'D' => 8,
            'E' => 8,
            'F' => 8,
            'G' => 8,
            'H' => 8,
            'I' => 8,
            'J' => 8,
            'K' => 8,
            'L' => 8,
            'M' => 8,
            'N' => 8,
            'O' => 8,
            'P' => 8,
            'Q' => 8,
            'R' => 8,
            'S' => 8,
            'T' => 8,
            'U' => 8,
            'V' => 8,
        ];
    }
}