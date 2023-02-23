@extends('layouts/contentLayoutMaster')

@php
    $title = 'Danh sách account của trường';
    $breadcrumbs = [
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
    ];
@endphp
@section('title', $title)
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@section('main')

    {{-- Statistics card section start --}}
    <section id="statistics-card">

        <!-- Table Hover Animation start -->
        <div class="row" id="table-hover-animation">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">

                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="btn-group pull-right " style="margin-right: 5px">
                                    <div class="btn waves-effect waves-light">
                                        <a type="button" class="main-action btn btn-flat btn-success waves-effect waves-light"
                                           href="{{ route('admin.school.add_user',['school_id' => $school->id, 'role_id' => ROLE_SCHOOL_MANAGER_ID]) }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            Thêm TK Quản lý
                                        </a>
                                    </div>
                                   
                                    <div class="btn waves-effect waves-light">
                                        <a type="button" class="btn btn-flat btn-success waves-effect waves-light"
                                           href="{{ route('school.export_school_accounts', ['id' => $school->id]) }}">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                            Xuất DS TK
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h4 class="card-title">Danh sách tài khoản của trường</h4>
                            <div class="table-responsive">
                                <table class="table zero-configuration mb-0">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên đăng nhập</th>
                                        <th scope="col">Tên đầy đủ</th>
                                        <th scope="col">Vai trò</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($school->users as $index => $user)
                                    @if($user->status)
                                        <tr>
                                            <th scope="row"> {{ $index + 1 }}</th>
                                            <td>{{$user->username}}</td>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->roles[0]->name}}</td>
                                            <td>{{$user->status ? 'Hoạt động' : 'Không hoạt động'}}</td>
                                            @if (!$role)
                                            <td>
                                                <a type="button"
                                                   class="main-action btn btn-flat btn-light waves-effect waves-light mr-1"
                                                   class="update-status" data-name="{{$user->name}}"
                                                   data-userid="{{$user->id}}" data-status="{{$user->status}}"
                                                   data-toggle="modal" data-target="#updateStatus" href="#">
                                                    <span title="Cập nhật trạng thái"><i class="fa fa-user"></i></span>
                                                </a>
                                                <a type="button"
                                                   href="{{ route('admin_user.edit', ['id' => $user->id, 'school_id' => $school->id]) }}"
                                                   class="main-action btn btn-flat btn-success waves-effect waves-light mr-1">
                                                    <span title="{{ trans('user.admin.edit') }}"> <i class="fa fa-edit"
                                                                                                     aria-hidden="true"></i></span>
                                                </a>

                                                <a type="button"
                                                   href="{{ route('admin_user.reset_password', ['id' => $user->id, 'school_id' => $school->id]) }}"
                                                   class="main-action btn btn-flat btn-warning waves-effect waves-light mr-1">
                                                    <span title="Đặt lại mật khẩu"> <i class="fa fa-key"
                                                                                       aria-hidden="true"></i></span>
                                                </a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade text-left" id="createForm" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                             role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel33">Thêm TK giáo viên</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('admin.school.users.assign_teacher', ['id' => $school->id]) }}"
                                      method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="teacher_id">Chọn giáo viên</label>
                                                        <select class="custom-select form-control required select2"
                                                                name="teacher_id">
                                                            @foreach($school->teachers as $teacher)
                                                                <option value="{{ $teacher->id }}">{{$teacher->fullname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="class_id">Chọn lớp</label>
                                                        <select class="custom-select form-control required select2"
                                                                name="class_id">
                                                            @foreach($school->classes as $class)
                                                                <option value="{{$class->id}}">{{ $class->class_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">@lang('admin.submit')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="updateStatus" data-backdrop="static" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Thay đổi trạng thái user: <span
                                                id="agencyName"></span></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                </div>
                                <form method="POST" action="">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    <div class="modal-body">
                                        <!-- Customer Info -->
                                        <div class="row">
                                            <div class="col-md-8">
                                                <label>Chọn trạng thái</label>
                                                <div class="form-group">
                                                    <select class="form-control" id="selection" name="status">
                                                        <option value="1">Hoạt động</option>
                                                        <option value="0">Tạm khoá</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table head options end -->
    </section>

    {{-- // Statistics Card section end--}}

@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#updateStatus').on('show.bs.modal', function (e) {
                var value = $(e.relatedTarget).data('status');
                $('#updateStatus #agencyName').text($(e.relatedTarget).data('name'));
                $("#updateStatus #selection option[value=" + value + "]").attr('selected', 'selected');
                $('#updateStatus form').attr('action', "{!! url('/portal/auth/user/update_status') !!}" + `/${$(e.relatedTarget).data('userid')}` + `?school_id={!! $school->id !!}`);
                $('#updateStatus').modal('show');
            });
        });
    </script>
@endpush