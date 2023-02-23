@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = 'Kế hoạch giáo dục của trường';
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
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <p class="card-text"> </p>
                            @if(Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_ADMIN, ROLE_SCHOOL_MANAGER]))
                            <a href="{{ route('school.school_plan.create', ['id' => $school->id])  }}" class="main-action btn btn-success">Tạo kế hoạch ngay</a>
                            @endif
                            <table class="table zero-configuration" id="shool-plans">
                                <thead>
                                <tr>
                                    <th scope="col">STT</th>
                                    <th scope="col">Trường</th>
                                    <th scope="col">Thời gian tạo</th>
                                    <th scope="col">Cập nhật lần cuối</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($schoolPlans as $key => $plan)
                                        @php if( $plan->status != PLAN_APPROVED && !Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_ADMIN, ROLE_SCHOOL_MANAGER])) continue; @endphp
                                        <tr>
                                            <th scope="row">#{{ $plan->id}}</th>
                                            <td>{{ $school->school_name}}</td>
                                            <td>{{ $plan->created_at }}</td>
                                            <td>{{ $plan->updated_at }}</td>
                                            <td>{{ PLAN_STATUSES[$plan->status]}}</td>
                                            <td>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info"
                                                    href="{{ route('school.school_plan.edit', ['id' => $school->id, 'planId' => $plan->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-warning"
                                                    href="{{ route('school.school_plan.download', ['id' => $school->id, 'planId' => $plan->id]) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>Tải về
                                                </a>
                                                @if(Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_ADMIN, ROLE_SCHOOL_MANAGER]))
                                                    <a style="margin-top: 3px" type="button" 
                                                        name=""
                                                        class="main-action btn btn-flat btn-success"
                                                        href="{{ route('school.school_plan.edit', ['id' => $school->id, 'planId' => $plan->id]) }}">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                    </a>
                                                    @if($plan->status == PLAN_PENDING)
                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="main-action btn btn-flat btn-info"
                                                            href="{{ route('school.school_plan.submit', ['id' => $school->id, 'planId' => $plan->id]) }}">
                                                            <i class="fa fa-send" aria-hidden="true"></i>Gửi cho phòng giáo dục
                                                        </a>
                                                        <a style="margin-top: 3px" type="button"
                                                            class="main-action btn btn-flat btn-danger delete-item"
                                                            data-url="{{route('school.school_plan.delete', ['id' => $school->id, 'planId' => $plan->id])}}" href="#">
                                                            <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
                                                        </a>
                                                    @endif
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

            $('#shool-plans').DataTable();

            $('#shool-plans').on('click','.delete-item',function () {
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
        });
    </script>
@endsection
