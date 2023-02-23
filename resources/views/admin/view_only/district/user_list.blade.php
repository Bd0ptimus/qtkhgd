@extends('layouts/contentLayoutMaster')

@php
    $title = 'Danh sách chuyên viên phòng giáo dục';
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => $title],
    ];
@endphp

@section('title', $title)
@section('vendor-style')
    {{-- Vendor Css files --}}
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection
@section('main')
    <!-- Modal -->
    <div class="modal fade" id="addMemberDistrict" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{route('district.post.add_user')}}" autocomplete="off">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><strong>Thêm tài khoản chuyên viên</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="district_id" value="{{$districtId}}">
                        <input type="hidden" class="form-control" name="role_id" value="{{ROLE_CV_PHONG_ID}}">
                        <div class="form-group">
                            <label for="name">Tên chuyên viên</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex justify-content-between mb-1">
                                <div class="btn-group">
                                    @if(isset($districtId))
                                        <div class="btn waves-effect waves-light pt-0 pl-0">
                                            <a type="button" class="btn btn-flat btn-success waves-effect waves-light"
                                               style="height: 40px"
                                               data-toggle="modal" data-target="#addMemberDistrict"
                                               href="#">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                Thêm TK chuyên viên
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class='row'>
                                    <div class="col-sm-3">
                                        <select {{ !Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? 'disabled' : '' }}
                                                class="form-control parent select2 filter-province" style="width: 100%;"
                                                name="province">
                                            <option value="">---Chọn tỉnh/thành---</option>
                                            @foreach ($provinces as $key => $province)
                                                <option value="{{ $province->id }}"
                                                        @if($province->id == $provinceId) selected @endif>
                                                    {!! $province->name !!}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <select {{ !Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? 'disabled' : '' }}
                                                class="form-control parent select2 filter-district" style="width: 100%;"
                                                name="district">
                                            <option value="">---Chọn quận/huyện---</option>
                                            @foreach ($districts as $key => $district)
                                                <option value="{{ $district->id }}"
                                                        @if($district->id == $districtId) selected @endif>{!! $district->name !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        @if($districtName)
                            <h4 class="card-title">Danh sách các tài khoản Phòng Giáo dục và Đào
                                tạo <strong>{{ $districtName }}</strong></h4>
                        @endif
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <p class="card-text"></p>
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên đăng nhập</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">Chức vụ</th>
                                        {{--<th scope="col">Kích hoạt</th>--}}
                                        <th scope="col">GSO ID</th>
                                        <th scope="col">Ngày cập nhật</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($users as $key => $user)
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $user->username}}</td>
                                            <td>{{ $user->name}}</td>
                                            <td>
                                                @if($user->roles->first())
                                                    {{ ROLE_VALUE[$user->roles->first()->slug]}}
                                                @endif
                                            </td>
                                            {{--<td>{!! USER_ACTIVE[$user->force_change_pass] !!}</td>--}}
                                            <td>{{ $district->gso_id}}</td>
                                            <td>{{ $user->updated_at }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger"
                                                        data-toggle="modal" data-target="#editMember" data-name="{{$user->name}}" data-userid="{{$user->id}}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i> Sửa
                                                </button>
                                                @if(Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_PHONG_GD]))
                                                    <a type="button" id="create_sgd_account" name="001"
                                                       class="btn btn-flat btn-info waves-effect waves-light"
                                                       href="{{route('district.manage.specialist_school', ['districtId' => $districtId, 'provinceId' => $provinceId, 'specialistId' => $user->id])}}">
                                                        <i class="fa fa-bars" aria-hidden="true"></i> DS truờng phụ
                                                        trách
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center bg-light">
                                                Dữ liệu trống
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal -->
    <div class="modal fade" id="editMember" tabindex="-1" aria-labelledby="editMember" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{route('district.put.edit_user')}}" autocomplete="off">
                    @csrf
                    @method('put')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMember"><strong>Sửa tên chuyên viên</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="id" id="userId">
                        <div class="form-group">
                            <label for="name">Tên chuyên viên</label>
                            <input type="text" class="form-control" name="name" required id="userName">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Sửa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $(document).ready(function () {

            $('.filter-province').change(function () {
                let newUrl = window.location.href.substring(0, window.location.href.indexOf('?'));
                let optionSelected = $(this).find("option:selected");
                let valueSelected = optionSelected.val();
                if (valueSelected == '') window.location.href = newUrl;
                let searchParams = new URLSearchParams(window.location.search)
                if (searchParams.has('provinceId')) {
                    window.location.href = newUrl + `?provinceId=${valueSelected}`;
                } else {
                    window.location.href = window.location.href + `?provinceId=${valueSelected}`;
                }
            });

            $('.filter-district').change(function () {
                let newUrl = window.location.href.substring(0, window.location.href.indexOf('&districtId'));
                let optionSelected = $(this).find("option:selected");
                let valueSelected = optionSelected.val();
                if (valueSelected == '') window.location.href = newUrl;
                let searchParams = new URLSearchParams(window.location.search)
                if (searchParams.has('districtId')) {
                    window.location.href = newUrl + `&districtId=${valueSelected}`;
                } else {
                    window.location.href = window.location.href + `&districtId=${valueSelected}`;
                }
            });

            $('#user-list').on('click', '.btn-delete-user', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: true,
                })
                /*swalWithBootstrapButtons.fire({
                    title: 'Bạn có chắc chắn muốn xoá ?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Tôi chắc chắn',
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: 'Không!',
                    reverseButtons: true,

                    preConfirm: function () {
                        return new Promise(function (resolve) {
                            $.ajax({
                                method: 'post',
                                url: '{{ route("admin_user.delete") }}',
                                data: {
                                    id: id,
                                    _token: '{{ csrf_token() }}',
                                },
                                success: function (data) {
                                    if (data.error == 1) {
                                        swalWithBootstrapButtons.fire(
                                            'Đã huỷ',
                                            data.msg,
                                            'error'
                                        )
                                    } else {
                                        resolve(data);
                                    }
                                }
                            });
                        });
                    }
                }).then((result) => {
                    if (result.value) {
                        swalWithBootstrapButtons.fire(
                            'Đã xoá!',
                            'Thông tin đã bị xoá.',
                            'success'
                        );
                        location.reload()
                    }
                })*/
            });
            $('#editMember').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var name = button.data('name') // Extract info from data-* attributes
                var id = button.data('userid') // Extract info from data-* attributes
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this)
                modal.find('#userId').val(id)
                modal.find('#userName').val(name)
            })
        });
    </script>
@endsection