@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
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
                                                <div class="table-responsive" style="max-height: 500px; overflow:scroll;">
                                                    <table class="table table-bordered table-striped text-nowrap" id="teacherPlanLessons">
                                                        <thead>
                                                            <tr>
                                                                @if($canManage)
                                                                <th scope="col" rowspan="2">Hành động </th>
                                                                @endif
                                                                <th scope="col" rowspan="2">Tháng năm</th>
                                                                <th scope="col" rowspan="2">Khoảng thời gian</th>
                                                                <th scope="col" colspan="3">Chương trình và sách giáo khoa</th>
                                                                <th scope="col" rowspan="2">Nội dung điều chỉnh, bổ sung(nếu có)</th>
                                                                <th scope="col" rowspan="2">Ghi chú</th>
                                                            </tr>

                                                            <tr>
                                                                <th scope="col">Chủ đề/Mạch nội dung</th>
                                                                <th scope="col">Tên bài học</th>
                                                                <th scope="col">Tiết học/thời lượng</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($teacherPlan) && count($teacherPlan->lessons) > 0)
                                                                @foreach($teacherPlan->lessons as $index => $lesson)
                                                                <tr>
                                                                    @if($canManage)
                                                                        @if($teacherPlan->status == PLAN_APPROVED) 
                                                                            <td>
                                                                                <a style="margin-top: 3px" href="#" class="btn btn-flat btn-info btn-datatable"
                                                                                    data-toggle="modal" data-target="#modalLessonContent{{$lesson->id}}">
                                                                                    <i class="fa fa-edit" aria-hidden="true"></i>Lên bài giảng
                                                                                </a>
                                                                            </td>
                                                                        @else
                                                                            <td><a href='#' class="delete-row btn btn-danger">Xoá</a></td>
                                                                        @endif
                                                                    @endif
                                                                    @foreach(['month_year', 'khoang_thoi_gian', 'chu_de', 'ten_bai_hoc', 'so_tiet', 'noi_dung_dieu_chinh', 'ghi_chu'] as $field)
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
                                                                        @elseif(in_array( $field, ['chu_de', 'bai_hoc', 'ten_bai_hoc', 'thiet_bi_day_hoc', 'dia_diem_day_hoc', 'noi_dung_dieu_chinh', 'ghi_chu'] ))
                                                                            <td>
                                                                                <textarea {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }} name="lessons[{{$lesson->id}}][{{$field}}]" value="{{$lesson[$field]}}">{{$lesson[$field]}}</textarea>
                                                                            </td>
                                                                        @else
                                                                            <td><input class='form-control' name="lessons[{{$lesson->id}}][{{$field}}]" value="{{$lesson[$field]}}" {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }}></td>
                                                                        @endif
                                                                    @endforeach
                                                                </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                
                                                    @if(!isset($teacherPlan) || (isset($teacherPlan) && $teacherPlan->status != PLAN_APPROVED)) 
                                                        <a class='btn btn-success addRow' id="addRow" data-month-years="{{ json_encode($monthYears) }}">Thêm dòng</a>
                                                    @endif

                                                    @if(isset($teacherPlan) && count($teacherPlan->lessons) > 0)
                                                        @foreach($teacherPlan->lessons as $index => $lesson)
                                                            <div class="modal fade" id="modalLessonContent{{$lesson->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Nội dung bài giảng</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                    
                                                                        <div class="modal-body">
                                                                            <div class="table-responsive">
                                                                                <textarea class='form-control description' name='lessons[{{$lesson->id}}][content]' value="{{$lesson->content}}">{{$lesson->content}}</textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary">Lưu bài giảng</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    
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
                "paging": false
            });

            var counter = parseInt("{{ isset($teacherPlan) ? (count($teacherPlan->lessons) > 0 ? $teacherPlan->lessons[count($teacherPlan->lessons)-1]->id + 1 : 1) : 1 }}");

            $('#addRow').on('click', function (e) {
                e.preventDefault();
                const monthYears = $(this).data('month-years');
                let selectMonthYears = `<select name="lessons[${counter}][month_year]" data-counter="${counter}" class="form-control">`;
                monthYears.forEach(monthYear => {
                    selectMonthYears += `<option value="${monthYear}"> ${monthYear}</option>`;
                });
                selectMonthYears += '</select>'; 
                t.row.add([ // tuan_thang chu_de ten_bai_hoc so_tiet noi_dung_dieu_chinh ghi_chu
                    `<a href='#' class="delete-row btn btn-danger">Xoá</a>`,
                    selectMonthYears,
                    `<input id="date-${counter}" name=lessons[${counter}][khoang_thoi_gian]>`,
                    `<input name=lessons[${counter}][chu_de]>`,
                    `<input name=lessons[${counter}][ten_bai_hoc]>`,
                    `<input name=lessons[${counter}][so_tiet]>`,
                    `<input name=lessons[${counter}][noi_dung_dieu_chinh]>`,
                    `<input name=lessons[${counter}][ghi_chu]>`
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
