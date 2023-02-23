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
                                    <div class="card card-school-plan collapse-icon accordion-icon-rotate">
                                        <div class="card-header">
                                        <strong class="card-title"></strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="accordion" id="accordionExample">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <select class="form-control" name="planId">
                                                            @foreach($teacherPlans as $plan)
                                                                <option {{ $teacherPlan->id == $plan->id ? 'selected' : ''}} value="{{ $plan->id }}">#{{ $plan->id}} - Kế hoạch - {{GRADES[$plan->grade]}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4">
                                                        <button class="btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                                                            {{ trans('admin.apply') }}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped text-nowrap" id="teacherPlanLessons">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Tuần</th>
                                                                <th scope="col">Từ Ngày đến ngày</th>
                                                                <th scope="col">Chủ đề</th>
                                                                <th scope="col">Nội dung</th>
                                                                <th scope="col">Phối hợp thực hiện</th>
                                                                <th scope="col">Kết quả</th>
                                                                <th scope="col">Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($teacherPlan) && count($teacherPlan->lessons) > 0)
                                                                @foreach($teacherPlan->lessons as $index => $lesson)
                                                                <tr>
                                                                    <td>Tuần {{ $index + 1 }}</td>
                                                                    @foreach(['thoi_gian', 'chu_de','noi_dung', 'phoi_hop', 'ket_qua'] as $field)
                                                                        <td><input class='form-control' name="lessons[{{$lesson->id}}][{{$field}}]" value="{{$lesson[$field]}}" {{$teacherPlan->status == PLAN_APPROVED ? 'readonly' : '' }}></td>
                                                                    @endforeach
                                                                    <td>
                                                                        @include('admin.school.staff.lesson.action_buttons')
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            @else
                                                                @foreach([1,2,3,4] as $index)
                                                                    <tr>
                                                                        <td>Tuần {{ $index }}</td>
                                                                        @foreach(['thoi_gian', 'chu_de','noi_dung', 'phoi_hop', 'ket_qua'] as $field)
                                                                            <td>
                                                                                <input class="form-control" type="text" name="lessons[{{$index}}][{{$field}}]" value="">
                                                                            </td>
                                                                        @endforeach
                                                                        <td></td>
                                                                    </tr>
                                                                @endforeach    
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    
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

            var counter = parseInt("{{ isset($teacherPlan) ? (count($teacherPlan->lessons) + 1) : 1 }}");

            $('#addRow').on('click', function (e) {
                e.preventDefault();
                let table = t;
                t.row.add([ // tuan_thang chu_de ten_bai_hoc so_tiet noi_dung_dieu_chinh ghi_chu
                    `<input name=lessons[${counter}][tuan_thang]>`,
                    `<input name=lessons[${counter}][chu_de]>`,
                    `<input name=lessons[${counter}][ten_bai_hoc]>`,
                    `<input name=lessons[${counter}][so_tiet]>`,
                    `<input name=lessons[${counter}][noi_dung_dieu_chinh]>`,
                    `<input name=lessons[${counter}][ghi_chu]>`,
                    `<a class="delete-row btn btn-danger">Xoá</a>`,
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
