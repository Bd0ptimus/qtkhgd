@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = "Kế hoạch giáo dục của  $staff->fullname";
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
                        <div class="card-title">Kế hoạch giáo dục của 
                            <select class="btn btn-outline-success btn-datatable select-teacher">
                            @foreach($school->staffs as $teacher)
                            <option data-href="{{ route('school.staff.plans', ['school_id' => $school->id, 'staffId' => $teacher->id])}}" {{$staff->id == $teacher->id ? 'selected' : '' }}>{{ $teacher->fullname }}</option>
                            @endforeach
                            </select>
                            <a type="button" class="btn btn-primary mr-1 reset-filter" href="{{route('admin.school.staff.all_staff_plans', ['id' => $school->id])}}">
                                <i class="feather icon-refresh-cw"></i>
                            </a>
                    </div>
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

                            
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-regular-group">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Ngày tạo</th>
                                        <th scope="col">Tên giáo viên</th>
                                        <th scope="col">Tổ chuyên môn</th>
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
                                    @foreach($staff->teacherPlans as $key => $teacherPlan)
                                        @php if( $teacherPlan->status != PLAN_APPROVED && !$canManage) continue; @endphp
                                        <tr>
                                            <th scope="row">#{{ $teacherPlan->id }}</th>
                                            <td>{{ $teacherPlan->created_at }}</td>
                                            <td>{{ $staff->fullname }}</td>
                                            <td>{{ $teacherPlan->regularGroup->name }}</td>
                                            <td>{{ GRADES[$teacherPlan->grade] }}</td>
                                            @if($school->school_type == 6)
                                                <td>Tháng {{ $teacherPlan->month }}</td>
                                            @else
                                                <td>{{ $teacherPlan->subject->name }}</td>
                                            @endif
                                            <td>{{ PLAN_STATUSES[$teacherPlan->status]}}</td>
                                            <td>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info btn-datatable"
                                                    href="{{ route('school.staff.plan.edit', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $group->id, 'planId' => $teacherPlan->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-warning btn-datatable"
                                                    href="{{ route('school.staff.plan.download', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $group->id, 'planId' => $teacherPlan->id]) }}">
                                                    <i class="fa fa-book" aria-hidden="true"></i>Download
                                                </a>
                                                @if($canManage && $teacherPlan->status == PLAN_PENDING)
                                                    <a style="margin-top: 3px" type="button" 
                                                        name=""
                                                        class="btn btn-flat btn-success btn-datatable"
                                                        href="{{ route('school.staff.plan.edit', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $group->id, 'planId' => $teacherPlan->id]) }}">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                    </a>
                                                    <a style="margin-top: 3px" type="button"
                                                        class="btn btn-flat btn-danger delete-item btn-datatable"
                                                        data-url="{{ route('school.staff.plan.delete', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $group->id, 'planId' => $teacherPlan->id]) }}" 
                                                        href="#">
                                                        <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
                                                    </a>
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
