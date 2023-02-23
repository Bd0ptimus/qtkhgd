@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = "Kế hoạch giáo dục giáo viên các trường";
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('district.manage', ['id' => $district->id])],
            ['name' => $title],
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Kế hoạch giáo dục của giáo viên</div>
                    </div>
                    <div class="card-content">
                        
                        <div class="card-body card-dashboard">
                            <div class="row">
                                <div class="col-md-10">
                                    <form class="" method="GET">
                                        <div class="row">
                                           
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-regular-group">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên giáo viên</th>
                                        <th scope="col">Trường</th>
                                        <th scope="col">Ngày tạo</th>
                                        <th scope="col">Khối lớp</th>
                                        <th scope="col">Tháng</th>
                                        <th scope="col">Môn học</th>
                                        <th scope="col">Trạng thái duyệt</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($plans as $key => $teacherPlan)
                                        <tr>
                                            <td>{{ $key + 1}}</td>
                                            <td>{{ $teacherPlan->staff->fullname ?? "" }}</td>
                                            <td>{{ $teacherPlan->staff->school->school_name}}</td>
                                            <td>{{ $teacherPlan->created_at }}</td>
                                            <td>{{ GRADES[$teacherPlan->grade] }}</td>
                                            <td>{{ $teacherPlan->month ? "Tháng ".$teacherPlan->month : '' }}</td>
                                            <td>{{ $teacherPlan->subject->name ?? '' }}</td>
                                            <td>{{ PLAN_STATUSES[$teacherPlan->status]}}</td>
                                            <td>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info btn-datatable"
                                                    href="{{ route('school.staff.plan.edit', ['school_id' => $teacherPlan->staff->school->id, 'staffId' => $teacherPlan->staff_id, 'rgId' => $teacherPlan->regular_group_id, 'planId' => $teacherPlan->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info btn-datatable"
                                                    href="{{ route('school.staff.teacher_lesson.index', ['school_id' => $teacherPlan->staff->school->id, 'staffId' => $teacherPlan->staff_id, 'planId' => $teacherPlan->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem bài giảng
                                                </a>
                                                <a style="margin-top: 3px" href="#" class="btn btn-flat btn-success btn-datatable"
                                                    data-toggle="modal" data-target="#modalHistory{{$teacherPlan->id}}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Lịch sử
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-warning btn-datatable"
                                                    href="{{ route('school.staff.plan.download', ['school_id' => $teacherPlan->staff->school->id, 'staffId' => $teacherPlan->staff_id, 'rgId' => $teacherPlan->regular_group_id, 'planId' => $teacherPlan->id]) }}">
                                                    <i class="fa fa-book" aria-hidden="true"></i>Download
                                                </a>
                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @foreach($plans as $key => $teacherPlan)
                                    <div class="modal fade" id="modalHistory{{$teacherPlan->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Lịch sử</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped text-nowrap table-plan">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Thời gian</th>
                                                                <th scope="col">Nội dung</th>
                                                                <th scope="col">Trạng thái kế hoạch</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($teacherPlan->histories as $history)
                                                                <tr>
                                                                    <td>{{$history->created_at}}</td>
                                                                    <td>{{$history->notes}}</td>
                                                                    <td>{{PLAN_STATUSES[$history->status]}}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="modalAddNote{{$teacherPlan->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Thêm nhận xét</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="POST" action="{{ route('school.staff.plan.add_review', ['school_id' => $teacherPlan->staff->school->id, 'staffId' => $teacherPlan->staff_id, 'rgId' => $teacherPlan->regular_group_id, 'planId' => $teacherPlan->id]) }}">
                                            @csrf    
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <textarea class='form-control description' name='notes'></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Nhận xét</button>
                                                </div>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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

            $('#shool-regular-group').DataTable();

            $('#shool-regular-group').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá kế hoạch tổ chuyên môn này?');
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
                            alert('Đã xoá kế hoạch Tổ chuyên môn');
                        }
                    });
                }
            });

            $('.select-teacher').on('change', function(e){
                e.preventDefault();
                var optionSelected = $("option:selected", this);
                window.location.replace(optionSelected.data('href'));
            });
        });
    </script>
@endsection
