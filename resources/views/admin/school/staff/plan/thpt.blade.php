@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <style>
        .txt-tiet-thu, .txt-so-tiet {
            width: 5%;
        }
    </style>
@endsection

@section('main')
    @php 
        $title = "Kế hoạch giáo dục của giáo viên $staff->fullname";
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => 'Danh sách kế hoạch', 'link' => route('school.staff.plan.index', ['school_id' => $school->id,'rgId' => $regularGroup->id, 'staffId' => $staff->id])],
            ['name' => $title]
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard" style="margin: auto; text-align:center">
                            {{-- Accordion with margin start --}}
                            <section id="accordion-with-margin">
                            <form method="POST">
                                @csrf
                               
                                <input type="hidden" name="school_id" value="{{ $school->id }}">
                                <div class="row">
                                    <div class="col-sm-12">
                                    <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                                    <input type="hidden" name="regular_group_id" value="{{ $regularGroup->id }}">
                                    <div class="card card-school-plan collapse-icon accordion-icon-rotate">
                                        <div class="card-header">
                                        <strong class="card-title"></strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="accordion" id="accordionExample">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <select class="form-control" name="grade">
                                                            @foreach($gradeOptions as $grade)
                                                                <option {{ ( isset($teacherPlan) && $teacherPlan->grade ==  $grade ) ? 'selected' : ''}} value="{{ $grade}}">{{ GRADES[$grade] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" name="subject_id">
                                                            @foreach($subjectOptions as $id => $name)
                                                                <option {{ ( isset($teacherPlan) && $teacherPlan->subject_id == $id ) ? 'selected' : '' }} value="{{ $id }}">{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="collapse-margin">
                                                    <div class="card-header" id="headingOne" data-toggle="collapse" role="button" data-target="#collapseOne"
                                                        aria-expanded="false" aria-controls="collapseOne">
                                                        <span class="lead collapse-title">
                                                        I. Phân phối chương trình
                                                        </span>
                                                    </div>
                                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <div class="table-responsive" style="max-height: 500px; overflow:scroll;">
                                        `                       <table class="table table-bordered table-striped text-nowrap" id="teacherPlanLessons">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">STT</th>
                                                                            <th scope="col">Tháng năm</th>
                                                                            <th scope="col">Khoảng thời gian</th>
                                                                            <th scope="col">Bài học</th>
                                                                            <th scope="col">Tên bài học</th>
                                                                            {{-- <th scope="col" class="txt-tiet-thu">Tiết thứ</th> --}}
                                                                            <th scope="col" class="txt-so-tiet">Số tiết</th>
                                                                            <th scope="col">Thời điểm</th>
                                                                            <th scope="col">Thiết bị dạy học</th>
                                                                            <th scope="col">Địa điểm dạy học</th>
                                                                            @if($canManage)
                                                                            <th scope="col">Hành động </th>
                                                                            @endif
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if(isset($teacherPlan) && count($teacherPlan->lessons) > 0)
                                                                            @foreach($teacherPlan->lessons as $index => $lesson)
                                                                            <tr>
                                                                                <td>{{ $lesson->id }}</td>
                                                                                @foreach(['month_year', 'khoang_thoi_gian', 'bai_hoc', 'ten_bai_hoc', 'so_tiet', 'thoi_diem', 'thiet_bi_day_hoc', 'dia_diem_day_hoc'] as $field)
                                                                                    @if ($field === 'month_year')
                                                                                        <td>
                                                                                            <select {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }} class="form-control" name="lessons[{{$lesson->id}}][{{$field}}]">
                                                                                                @foreach ($monthYears as $monthYear)
                                                                                                    <option {{ $lesson->month_year === $monthYear ? 'selected' : '' }} value="{{ $monthYear }}">{{ $monthYear }}</option>    
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </td>
                                                                                    @elseif ($field === 'khoang_thoi_gian')
                                                                                        <td>
                                                                                            <input {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }} 
                                                                                            class="date" 
                                                                                            id="date-{{ $lesson->id }}" 
                                                                                            data-id="{{ $lesson->id }}" 
                                                                                            data-start="{{ $lesson->start_date }}" 
                                                                                            data-end="{{ $lesson->end_date }}" 
                                                                                            name="lessons[{{ $lesson->id }}][{{ $field }}]">
                                                                                        </td>
                                                                                    @else
                                                                                        <td><input name="lessons[{{$lesson->id}}][{{$field}}]" value="{{$lesson[$field]}}"></td>
                                                                                    @endif
                                                                                @endforeach
                                                                                @if($canManage)
                                                                                    @if($teacherPlan->status == PLAN_APPROVED) 
                                                                                        <td><a class="btn btn-info">Lên bài giảng</a></td>
                                                                                    @else
                                                                                        <td><a class="delete-row btn btn-danger">Xoá</a></td>
                                                                                    @endif
                                                                                @endif
                                                                            </tr>
                                                                            @endforeach
                                                                       @endif
                                                                    </tbody>
                                                                </table>
                                                                @if(!isset($teacherPlan) || (isset($teacherPlan) && !$teacherPlan->status == PLAN_APPROVED)) 
                                                                    <button class='btn btn-success' id="addRow" data-month-years="{{ json_encode($monthYears) }}">Thêm dòng</button>
                                                                @else
                                                                    
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="collapse-margin">
                                                    <div class="card-header" id="headingFour" data-toggle="collapse" role="button" data-target="#collapse6"
                                                        aria-expanded="false" aria-controls="collapseFour">
                                                        <span class="lead collapse-title">
                                                        II. Nhiệm vụ khác
                                                        </span>
                                                    </div>
                                                    <div id="collapse6" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <textarea class='form-control description' name='additional_tasks' value="{{ old('additional_tasks', $teacherPlan->additional_tasks ?? '' ) }}">{{ old('additional_tasks', $teacherPlan->additional_tasks ?? "" ) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                @if($canManage)
                                <div class="d-flex col-md-4 offset-md-4">
                                    <div class="text-nowrap ml-1">
                                        <button type="submit" class="btn btn-success">
                                            Lưu kế hoạch
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </form>
                            </section>
                            {{-- Accordion with margin end --}}
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Scroll - horizontal and vertical table -->
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        
        $(document).ready(function () {
            $('.select2').select2({
                allowClear: true
            });
            
            var t = $('#teacherPlanLessons').DataTable({
                "searching": false,
                "lengthChange": false,
                "paging": false,
                
            });

            var counter = parseInt("{{ isset($teacherPlan) ? ($teacherPlan->lessons[count($teacherPlan->lessons)-1]->id + 1) : 1 }}");

            $('#addRow').on('click', function (e) {
                e.preventDefault();
                const monthYears = $(this).data('month-years');
                let selectMonthYears = `<select name="lessons[${counter}][month_year]" data-counter="${counter}" class="form-control">`;
                monthYears.forEach(monthYear => {
                    selectMonthYears += `<option value="${monthYear}"> ${monthYear}</option>`;
                });
                let table = t;
                t.row.add([
                    `${counter}`,
                    selectMonthYears,
                    `<input id="date-${counter}" name=lessons[${counter}][khoang_thoi_gian]>`,
                    `<input name=lessons[${counter}][bai_hoc]>`,
                    `<input name=lessons[${counter}][ten_bai_hoc]>`,
                    `<input name=lessons[${counter}][tiet_thu]>`,
                    `<input name=lessons[${counter}][so_tiet]>`,
                    `<input name=lessons[${counter}][thoi_diem]>`,
                    `<input name=lessons[${counter}][thiet_bi_day_hoc]>`,
                    `<input name=lessons[${counter}][dia_diem_day_hoc]>`,
                    `<a class="delete-row btn btn-danger">Xoá</a>`,
                ]).draw(false);
                const monthYear = $(`select[name="lessons[${counter}][month_year]"]`).val();
                const startDate = '01-' + monthYear;
                $(`#date-${counter}`).daterangepicker({
                    startDate: startDate,
                    endDate: startDate,
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Áp dụng',
                        cancelLabel: 'Hủy bỏ',
                        fromLabel: 'Từ',
                        toLabel: 'Đến',
                        customRangeLabel: 'Tùy chỉnh',
                        daysOfWeek: [
                            'CN',
                            'T2',
                            'T3',
                            'T4',
                            'T5',
                            'T6',
                            'T7'
                        ],
                        monthNames: [
                            'Tháng 1',
                            'Tháng 2',
                            'Tháng 3',
                            'Tháng 4',
                            'Tháng 5',
                            'Tháng 6',
                            'Tháng 7',
                            'Tháng 8',
                            'Tháng 9',
                            'Tháng 10',
                            'Tháng 11',
                            'Tháng 12',
                        ],
                        firstDay : 1
                    }
                });
                $(`select[name="lessons[${counter}][month_year]"]`).on('change', function(e) {
                    const monthYear = $(this).val();
                    const counter = $(this).data('counter');
                    $(`#date-${counter}`).data('daterangepicker').setStartDate('01-' + monthYear);
                    $(`#date-${counter}`).data('daterangepicker').setEndDate('01-' + monthYear);
                })
                counter++;
            });

            $('#teacherPlanLessons').on('click', '.delete-row', function(){
                t.row( $(this).parents('tr') )
                .remove()
                .draw();
            });

            $('.date').each(function() {
                const id = $(this).data('id');
                const start = $(this).data('start');
                const end = $(this).data('end');
                const monthYear = $(`select[name="lessons[${id}][month_year]"]`).val();
                const startDate = start ? start : '01-' + monthYear;
                const endDate = end ? end : '01-' + monthYear;
                $(`#date-${id}`).daterangepicker({
                    startDate: startDate,
                    endDate: endDate,
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Áp dụng',
                        cancelLabel: 'Hủy bỏ',
                        fromLabel: 'Từ',
                        toLabel: 'Đến',
                        customRangeLabel: 'Tùy chỉnh',
                        daysOfWeek: [
                            'CN',
                            'T2',
                            'T3',
                            'T4',
                            'T5',
                            'T6',
                            'T7'
                        ],
                        monthNames: [
                            'Tháng 1',
                            'Tháng 2',
                            'Tháng 3',
                            'Tháng 4',
                            'Tháng 5',
                            'Tháng 6',
                            'Tháng 7',
                            'Tháng 8',
                            'Tháng 9',
                            'Tháng 10',
                            'Tháng 11',
                            'Tháng 12',
                        ],
                        firstDay : 1
                    }
                });
                $(`select[name="lessons[${id}][month_year]"]`).on('change', function(e) {
                    const monthYear = $(this).val();
                    $(`#date-${id}`).data('daterangepicker').setStartDate('01-' + monthYear);
                    $(`#date-${id}`).data('daterangepicker').setEndDate('01-' + monthYear);
                })
            });
        });
    </script>
@endsection
