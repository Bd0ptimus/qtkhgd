@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = "Kế hoạch giáo dục của $staff->fullname";
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $title],
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Kế hoạch giáo dục của {{$staff->fullname}}</div>
                    </div>
                    <div class="card-content">
                        
                        <div class="card-body card-dashboard">
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-sm-12">
                                    @php //$groups = Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? $staffGroups : $school->regularGroups; @endphp
                                    @foreach($staffGroups as $group)
                                        <a class="btn btn-datatable {{ $group->id == request()->rgId ? 'btn-success' : 'btn-outline-success' }}" href="{{route('school.staff.plan.index', ['school_id' => $school->id, 'staffId'  => $staff->id, 'rgId' => $group->id])}}">{{ $group->name }}</a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <form class="" method="GET">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select class="form-control" name="grade">
                                                    @foreach($grades as $grade)
                                                        <option value="{{ $grade}}">{{ GRADES[$grade] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="subject">
                                                    @foreach($subjects as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
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
                               @if($canManage)
                                    <div class="col-md-2 float-right">
                                        <div class="btn-group text-nowrap">
                                            <a type="button" class="btn btn-flat btn-success"
                                                href="{{ route('school.staff.plan.create', ['school_id' => $school->id, 'rgId' => $regularGroup->id, 'staffId' => $staff->id]) }}">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                Lên kế hoạch
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="teacherPlan">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">Thời gian tạo</th>
                                        <th scope="col">Khối lớp</th>
                                        @if($school->school_type == 6)
                                            <th scope="col">Tháng</th>
                                        @else
                                            <th scope="col">Môn học</th>
                                        @endif
                                        <th scope="col">Trạng thái duyệt</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($teacherPlans as $key => $teacherPlan)
                                        @php if( $teacherPlan->status != PLAN_APPROVED && !$canManage) continue; @endphp
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <th scope="row">#{{ $teacherPlan->id }}</th>
                                            <td>{{ $teacherPlan->created_at }}</td>
                                            <td>{{ GRADES[$teacherPlan->grade] }}</td>
                                            @if($school->school_type == 6)
                                                <td>Tháng {{ $teacherPlan->month }}</td>
                                            @else
                                                <td>{{ $teacherPlan->subject->name ?? '' }}</td>
                                            @endif
                                            <td>{{ PLAN_STATUSES[$teacherPlan->status]}}</td>
                                            <td>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info btn-datatable"
                                                    href="{{ route('school.staff.plan.edit', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $teacherPlan->regular_group_id, 'planId' => $teacherPlan->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                </a>
                                                <a style="margin-top: 3px" href="#" class="btn btn-flat btn-success btn-datatable"
                                                    data-toggle="modal" data-target="#modalHistory{{$teacherPlan->id}}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Lịch sử
                                                </a>

                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-warning btn-datatable"
                                                    href="{{ route('school.staff.plan.download', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $teacherPlan->regular_group_id, 'planId' => $teacherPlan->id]) }}">
                                                    <i class="fa fa-book" aria-hidden="true"></i>Download
                                                </a>
                                                @if($canManage)
                                                    @if($teacherPlan->status != PLAN_APPROVED)
                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="btn btn-flat btn-success btn-datatable"
                                                            href="{{ route('school.staff.plan.edit', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $teacherPlan->regular_group_id, 'planId' => $teacherPlan->id]) }}">
                                                            <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                        </a>
                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="btn btn-flat btn-info"
                                                            href="{{ route('school.staff.plan.submit', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $teacherPlan->regular_group_id, 'planId' => $teacherPlan->id]) }}">
                                                            <i class="fa fa-send" aria-hidden="true"></i>Gửi tổ trưởng duyệt
                                                        </a>
                                                    @endif
                                                    @if($teacherPlan->status == PLAN_PENDING)
                                                        
                                                        <a style="margin-top: 3px" type="button"
                                                            class="btn btn-flat btn-danger delete-item btn-datatable"
                                                            data-url="{{ route('school.staff.plan.delete', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $group->id, 'planId' => $teacherPlan->id]) }}" 
                                                            href="#">
                                                            <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
                                                        </a>
                                                    @endif
                                                @endif
                                                @if($teacherPlan->status == PLAN_APPROVED)
                                                    <a style="margin-top: 3px" type="button" 
                                                        name=""
                                                        class="btn btn-flat btn-info btn-datatable"
                                                        href="{{ route('school.staff.teacher_lesson.index', ['school_id' => $school->id, 'staffId' => $staff->id,'planId' => $teacherPlan->id]) }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>Xem bài giảng
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @foreach($teacherPlans as $key => $teacherPlan)
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

            $('#teacherPlan').DataTable();

            $('#teacherPlan').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá kế hoạch này?');
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
                            alert('Đã xoá kế hoạch');
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
