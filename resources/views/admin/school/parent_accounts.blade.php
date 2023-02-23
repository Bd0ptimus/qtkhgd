@extends('layouts/contentLayoutMaster')

@php
    $title = 'Danh sách tài khoản phụ huynh';
    $breadcrumbs = [
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
        ['name' => $title]
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
                        <div class="d-flex justify-content-between mb-1">
                                <form id="filterHealthAbnormal" class="d-flex flex-wrap mb-1" method="GET"
                                      action="{{ route('admin.school.parent_accounts', ['id' => $school->id]) }}">
                                    <div class="mr-1">
                                        <select @php echo Admin::user()->inRoles(['giao-vien']) ? "disabled" : ""; @endphp class="custom-select form-control required select2"
                                                name="school_branch" required data-placeholder="Đểm trường">
                                            <option value="">Chọn điểm trường</option>
                                            @foreach($school->branches as $branch)
                                                <option value="{{$branch->id}}"
                                                        @if($selectedBranch && $selectedBranch->id == $branch->id) selected @endif>
                                                    {{ $branch->branch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mr-1">
                                        <select class="custom-select form-control select2"
                                                name="class" data-placeholder="Lớp">
                                            <option value="">Chọn Lớp</option>
                                            @foreach($classes as $class)
                                                <option value="{{$class->id}}"
                                                        @if($class_id && $class_id == $class->id) selected @endif>{{ $class->class_name}}</option>
                                            @endforeach
                                        </select>
                                    </div><!-- Chọn lớp -->
                                    
                                    <div class="ml-1">
                                        <button class="btn btn-primary">
                                            {{ trans('admin.apply') }}
                                        </button>
                                    </div>
                                </form>                     
                            </div><!-- Agency Selector -->
                            

                            
                            <div class="table-responsive">
                                <table class="table zero-configuration mb-0">
                                    <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tên đăng nhập</th>
                                        <th scope="col">Tên đầy đủ</th>
                                        <th scope="col">Vai trò</th>
                                        <th scope="col">Tên học sinh</th>
                                        <th scope="col">Lớp</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($selectedBranch->students) > 0)
                                        @php $index = 0; @endphp
                                        @foreach($selectedBranch->students as $student)
                                            @php 
                                                if(!($student->parent_accounts[0] ?? false)) continue;
                                                $user_status = $student->parent_accounts[0]->status ?? null; 
                                            @endphp
                                            <tr>
                                                <th scope="row"> {{ ++$index }}</th>
                                                <td>{{$student->parent_accounts[0]->username ?? null}}</td>
                                                <td>{{$student->parent_accounts[0]->name ?? null}}</td>
                                                <td>{{$student->parent_accounts[0]->roles[0]->name ?? null}}</td>
                                                <td>{{$student->fullname ?? null}}</td>
                                                <td>{{$student->class->class_name ?? null}}</td>
                                                <td>{{$user_status ? 'Hoạt động' : 'Không hoạt động'}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-flat btn-light  waves-effect waves-light mr-1" class="update-status" data-name="{{$student->parent_accounts[0]->name}}" data-userid="{{$student->parent_accounts[0]->id}}" data-status="{{$student->parent_accounts[0]->status}}" data-toggle="modal" data-target="#updateStatus" href="#">
                                                        <span title="Cập nhật trạng thái"><i class="fa fa-user"></i></span>
                                                    </a>  
                                                    <a type="button"  href="{{ route('admin_user.edit', ['id' => $student->parent_accounts[0]->id, 'school_id' => $school->id]) }}" 
                                                        class="btn btn-flat btn-success waves-effect waves-light mr-1">
                                                        <span title="{{ trans('user.admin.edit') }}"> <i  class="fa fa-edit" aria-hidden="true"></i></span>
                                                    </a>

                                                    <a type="button"  href="{{ route('admin_user.reset_password', ['id' => $student->parent_accounts[0]->id, 'school_id' => $school->id]) }}" 
                                                        class="btn btn-flat btn-warning waves-effect waves-light mr-1">
                                                        <span title="Đặt lại mật khẩu"> <i  class="fa fa-key" aria-hidden="true"></i></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
                    <div class="modal fade" id="updateStatus" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Thay đổi trạng thái user: <span id="agencyName"></span></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
    $(document).ready(function(){
        $('#updateStatus').on('show.bs.modal', function(e) {
            var status = $(e.relatedTarget).data('status');
          $('#updateStatus #agencyName').text($(e.relatedTarget).data('name'));
          $("#updateStatus #selection option[value="+status+"]").attr('selected', 'selected');
          $('#updateStatus form').attr('action', "{!! url('/portal/auth/user/update_status') !!}" + `/${$(e.relatedTarget).data('userid')}` + `?school_id={!! $school->id !!}`);
          $('#updateStatus').modal('show');
      });
    });
    </script>
@endpush