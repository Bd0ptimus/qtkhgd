@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = "Bài giảng của giáo viên $staff->fullname";
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
                                                <div class="row">
                                                    
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped text-nowrap" id="teacherPlanLessons">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" rowspan="2">Hành động</th>
                                                                <th scope="col" rowspan="2">Tuần, tháng</th>
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
                                                                    <td>
                                                                        @include('admin.school.staff.lesson.action_buttons')
                                                                    </td>
                                                                    @foreach(['tuan_thang', 'chu_de', 'ten_bai_hoc', 'so_tiet', 'noi_dung_dieu_chinh', 'ghi_chu'] as $field)
                                                                        <td><input class='form-control' name="lessons[{{$lesson->id}}][{{$field}}]" value="{{$lesson[$field]}}" {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }}></td>
                                                                    @endforeach
                                                                </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    
                                                    @if(!isset($teacherPlan) || (isset($teacherPlan) && !$teacherPlan->status == PLAN_APPROVED)) 
                                                        <button class='btn btn-success' id="addRow">Thêm dòng</button>
                                                    @endif

                                                    @if(isset($teacherPlan) && count($teacherPlan->lessons) > 0)
                                                        @foreach($teacherPlan->lessons as $index => $lesson)
                                                            @include('admin.school.staff.lesson.modal_actions')
                                                        @endforeach
                                                        @include('admin.school.staff.lesson.modal_sample_lessons')
                                                    @endif
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
                "searching": false,
                "lengthChange": false,
                "paging": false,
                
            });

            var counter = parseInt("{{ isset($teacherPlan) ? (count($teacherPlan->lessons) + 1) : 1 }}");

            $('#addRow').on('click', function (e) {
                e.preventDefault();
                let table = t;
                t.row.add([ // tuan_thang chu_de ten_bai_hoc so_tiet noi_dung_dieu_chinh ghi_chu
                    `<a href='#' class="delete-row btn btn-danger">Xoá</a>`,
                    `<input name=lessons[${counter}][tuan_thang]>`,
                    `<input name=lessons[${counter}][chu_de]>`,
                    `<input name=lessons[${counter}][ten_bai_hoc]>`,
                    `<input name=lessons[${counter}][so_tiet]>`,
                    `<input name=lessons[${counter}][noi_dung_dieu_chinh]>`,
                    `<input name=lessons[${counter}][ghi_chu]>`
                ]).draw(false);
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
        });
    </script>
    <script src="{{ asset('js/scripts/SampleLessonFilter.js') }}"></script>
@endsection
