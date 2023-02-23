@extends('layouts.contentLayoutMaster')

@php
    $title = 'Danh sách lớp theo trường';
    $breadcrumbs = [
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
        ['name' => $title],
    ];
    $tableHeadings = [
        'ID',
        'Lớp',
        'Khối',
        'Điểm trường',
        'Ngày cập nhật',
        'Thao tác'
    ];
@endphp

@section('title', $title)

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex mb-1">
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle"
                                            type="button"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        Tùy biến lưới
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <ul class="list-unstyled list-scrollable m-0">
                                            @foreach($tableHeadings as $index => $tableHeading)
                                                <li>
                                                    <fieldset>
                                                        <div class="dropdown-item vs-checkbox-con vs-checkbox-primary">
                                                            <input type="checkbox" checked
                                                                   class="toggle-datatable-vis"
                                                                   data-table-class="zero-configuration"
                                                                   data-column="{{$index}}">
                                                            <span class="vs-checkbox">
                                                                            <span class="vs-checkbox--check"><i
                                                                                        class="vs-icon feather icon-check m-0"></i></span>
                                                                            </span>
                                                            <span class="">{{$tableHeading}}</span>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex mb-1 justify-content-between">
                                <div class="d-flex">
                                    @if(Admin::user()->isAdministrator())
                                        <button class="main-action btn btn-danger ag-grid-export-btn waves-effect waves-light mr-1 btn-delete-all">
                                            {{ trans('admin.remove_all') }}
                                        </button>
                                    @endif
                                    <button class="main-action btn btn-danger ag-grid-export-btn waves-effect waves-light mr-1 btn-delete-multiple">
                                        {{ trans('admin.delete') }}
                                    </button>
                                </div>
                                <div class="d-flex">
                                    <a type="button" id="create_category" name='create_category'
                                       class="main-action btn btn-flat btn-success"
                                       href="{{ route('admin.school.import_class', ['id' => $school->id]) }}">

                                        Import
                                    </a>
                                    <a type="button" id="create_category" name='create_category'
                                       class="main-action ml-1 btn btn-flat btn-success"
                                       href="{{ route('admin.school.add_class', ['id' => $school->id]) }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm lớp
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="data-table" class="table nowrap zero-configuration">
                                    <thead>
                                    <tr>
                                        @foreach($tableHeadings as $tableHeading)
                                            <th scope="col">{{$tableHeading}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($school->classes as $key => $class)
                                        <tr data-id="{{$class->id}}">
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $class->class_name}}</td>
                                            <td>{{ $class->getGrade()}}</td>
                                            <td>{{ $class->schoolBranch ? $class->schoolBranch->branch_name : '' }}</td>
                                            <td>{{ $class->updated_at}}</td>
                                            <td>
                                                <a type="button"
                                                   name='{{ $school->id }}' class="main-action btn btn-flat btn-success"
                                                   href="{{  route('admin.school.edit_class', ['id' => $class->id])  }}">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </a>
                                                <a type="button"
                                                   name='{{ $school->id }}'
                                                   class="main-action btn-delete-class btn btn-flat btn-danger"
                                                   href="#"
                                                   data-id="{{$class->id}}"
                                                >
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>
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
            $('.select2').select2()
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#data-table tbody').on('click', 'tr', function () {
                $(this).toggleClass('selected');
            });
        });

        $(document).on('click', '.btn-delete-all', function (e) {
            e.preventDefault();
            deleteClass("{{ route('admin.school.delete_all_class', ['id' => $school->id]) }}");
        });

        $(document).on('click', '.btn-delete-multiple', function (e) {
            e.preventDefault();
            var ids = $("tr.selected").map(function () {
                return $(this).attr("data-id");
            }).get();
            deleteClass("{{ route('admin.school.delete_class') }}", ids);
        });

        $('.btn-delete-class').on('click', function (e) {
            if (checkIfAccountIsDemo()) return false;
            e.preventDefault();
            const ids = $(this).data('id');
            deleteClass("{{ route('admin.school.delete_class') }}", [ids]);
        });

        function deleteClass(routeUrl, ids = []) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            })
            swalWithBootstrapButtons.fire({
                title: 'Toàn bộ học sinh và dữ liệu học sinh của lớp sẽ bị xoá.Bạn có chắc chắc?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Đúng, Tôi chắc chắn xoá!',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: 'Không, Huỷ lệnh xoá!',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'post',
                            url: routeUrl,
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    swalWithBootstrapButtons.fire(
                                        'Đã huỷ do có lỗi ',
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
                    dataTableGroups['zero-configuration'].row('[data-id="' + ids + '"]').remove().draw(false);
                    swalWithBootstrapButtons.fire(
                        'Đã xoá lớp!',
                        'Lớp đã bị xoá khỏi hệ thống.',
                        'success'
                    );
                    location.reload()
                }
            })
        }
    </script>
@endsection
