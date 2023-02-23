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
            ['name' => $title]
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include("admin.component.loading")
                    <div class="card-content">
                        <div class="card-body card-dashboard" style="margin: auto; text-align:center">
                            
                            <div class="row">
                                <div class="col-md-10">
                                    <form class="" method="GET">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select class="form-control" name="planId">
                                                    @foreach($teacherPlans as $plan)
                                                        <option {{ $teacherPlan->id == $plan->id ? 'selected' : ''}} value="{{ $plan->id }}">#{{ $plan->id}} - Kế hoạch - {{GRADES[$plan->grade]}} - {{$plan->subject->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4">
                                                <button class="btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                                                    {{ trans('admin.apply') }}
                                                </button>
                                            </div>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                            <section id="accordion-with-margin">
                            
                                <div class="row">
                                    
                                    <div class="col-sm-12">
                                        <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                                        
                                        <div class="card card-school-plan collapse-icon accordion-icon-rotate">
                                            <div class="card-header">
                                            <strong class="card-title"></strong>
                                            </div>
                                            <div class="card-body">
                                                <div class="accordion" id="accordionExample">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered text-nowrap" id="teacherPlanLessons">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">STT</th>
                                                                    <th scope="col">Hành động </th>
                                                                    @if($teacherPlan->status == PLAN_APPROVED )
                                                                    <th scope="col" class="col-1 px-1">Trạng thái</th>
                                                                @endif
                                                                    <th scope="col">Tháng năm</th>
                                                                    <th scope="col">Khoảng thời gian</th>
                                                                    <th scope="col">Bài học</th>
                                                                    <th scope="col">Tên bài học</th>
                                                                    {{-- <th scope="col" class="txt-tiet-thu">Tiết thứ</th> --}}
                                                                    <th scope="col" class="txt-so-tiet">Số tiết</th>
                                                                    <th scope="col">Thời điểm</th>
                                                                    <th scope="col">Thiết bị dạy học</th>
                                                                    <th scope="col">Địa điểm dạy học</th>
                                                                   
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(isset($teacherPlan) && count($teacherPlan->lessons) > 0)
                                                                
                                                                    @foreach($teacherPlan->lessons as $index => $lesson)
                                                                      
                                                                        <tr>
                                                                            <td>#{{ $lesson->id}}</td>
                                                                            <td>
                                                                                @include('admin.school.staff.lesson.action_buttons')
                                                                            </td>
                                                                            @if($teacherPlan->status == PLAN_APPROVED )
                                                                            <td>
                                                                                @if( $lesson->status == PLAN_APPROVED)
                                                                                    <p class ="text-success">Đã duyệt</p>
                                                                                @else
                                                                                    <p class ="text-danger">Chưa duyệt</p>
                                                                                @endif
                                                                            </td>
                                                                        @endif
                                                                            @foreach(['month_year', 'khoang_thoi_gian', 'bai_hoc', 'ten_bai_hoc', 'so_tiet', 'thoi_diem', 'thiet_bi_day_hoc', 'dia_diem_day_hoc'] as $field) <!--removed: 'tiet_thu'-->
                                                                                @if ($field === 'month_year')
                                                                                    <td>
                                                                                        <select {{$teacherPlan->status == PLAN_APPROVED ? 'disabled' : '' }} class="form-control" name="lessons[{{$lesson->id}}][{{$field}}]">
                                                                                            @foreach ($monthYears as $monthYear)
                                                                                                <option {{ $lesson->month_year === $monthYear ? 'selected' : '' }} value="{{ $monthYear }}">{{ $monthYear }}</option>    
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </td>
                                                                                @elseif ($field === 'khoang_thoi_gian')
                                                                                    <td class="{{ PLAN_APPROVED }}">
                                                                                        <input
                                                                                        class="date form-control"
                                                                                        id="date-{{ $lesson->id }}"
                                                                                        data-id="{{ $lesson->id }}"
                                                                                        data-start="{{ $lesson->start_date }}"
                                                                                        data-end="{{ $lesson->end_date }}"
                                                                                        name="lessons[{{ $lesson->id }}][{{ $field }}]"
                                                                                        {{$teacherPlan->status == PLAN_APPROVED ? 'disabled' : '' }}>
                                                                                    </td>
                                                                                @elseif(in_array($field, ['bai_hoc', 'ten_bai_hoc']))
                                                                                    <td><textarea style="width:200px" class='form-control' name="lessons[{{$lesson->id}}][{{$field}}]" value="{{$lesson[$field]}}" {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }}>{{$lesson[$field]}}</textarea></td>
                                                                                @else
                                                                                    <td><input class='form-control' name="lessons[{{$lesson->id}}][{{$field}}]" value="{{$lesson[$field]}}" {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }}></td>
                                                                                @endif
                                                                            @endforeach
                                                                        
                                                                        </tr>
                                                                     
                                                                    
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                        
                                                        @if(!isset($teacherPlan) || (isset($teacherPlan) && !$teacherPlan->status == PLAN_APPROVED)) 
                                                            <button class='btn btn-success' id="addRow" data-month-years="{{ json_encode($monthYears) }}">Thêm dòng</button>
                                                        @endif

                                                        @if(isset($teacherPlan) && count($teacherPlan->lessons) > 0)
                                                            @foreach($teacherPlan->lessons as $index => $lesson)
                                                                @include('admin.school.staff.lesson.modal_actions')
                                                            @endforeach
                                                        
                                                        @endif
                                                        <div id="lessonContent"></div>
                                                        <div id="lessonSampleDetail"></div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                       
                                    </div>
                                </div>
                                
                            
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
                "searching": true,
                "lengthChange": true,
                "paging": false
            });

            $('#teacherPlanLessons').on('click','.show-modal-lesson',function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).data('url'),
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $("#loading-ajax").removeClass('d-none');
                    },
                    success: function(res) {
                        console.log(res.view);
                        $('#lessonContent').empty().append(res.view);
                        $('#modalLessonContent').modal('show');
                        $("#loading-ajax").addClass('d-none');
                    }   
                });
            });

            $('.tblSampleLessons').on('click','.show-lesson-sample-detail',function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).data('url'),
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        console.log(res.view);
                        $('#lessonSampleDetail').empty().append(res.view);
                        $('#modalLessonSampleDetail').modal('show');
                    }   
                });
            });

            var counter = parseInt("{{ isset($teacherPlan) ? (count($teacherPlan->lessons) + 1) : 1 }}");

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
                    `<a href="#" class="delete-row btn btn-danger">Xoá</a>`,
                    selectMonthYears,
                    `<input id="date-${counter}" name=lessons[${counter}][khoang_thoi_gian]>`,
                    `<input name=lessons[${counter}][bai_hoc]>`,
                    `<input name=lessons[${counter}][ten_bai_hoc]>`,
                    `<input name=lessons[${counter}][tiet_thu]>`,
                    `<input name=lessons[${counter}][so_tiet]>`,
                    `<input name=lessons[${counter}][thoi_diem]>`,
                    `<input name=lessons[${counter}][thiet_bi_day_hoc]>`,
                    `<input name=lessons[${counter}][dia_diem_day_hoc]>`,
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

            $('#teacherPlanLessons').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá thời khoá biểu này này?');
                if(confirmDelete) {
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            element.parents('tr').remove();
                            alert('Đã xoá Thời khoá biểu');
                        }
                    });
                }
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
            });

        });
    </script>
    <script src="{{ asset('js/scripts/SampleLessonFilter.js') }}"></script>
@endsection
