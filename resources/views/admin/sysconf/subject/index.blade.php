@extends('layouts/contentLayoutMaster')

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
                    <div class="card-header">
                        <h4 class="card-title">Danh sách các môn học</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex">
                                <div class="btn-group text-nowrap">
                                    <a type="button"
                                        class="main-action btn btn-flat btn-success"
                                        href="{{ route('sysconf.subject.create') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm môn học
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="sysconf-subject">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Môn học</th>
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Khối</th>
                                        <th scope="col">Mặc định hệ thống</th>
                                        <th scope="col" style="width: 140px;">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($subjects as $key => $subject)
                                        <tr>
                                            <td class="font-weight-bold" scope="row">{{ $key + 1}}</td>
                                            <td class="font-weight-bold">{{ $subject->name}}</td>
                                            <td>{{ $subject->description}}</td>
                                            <td>
                                                @foreach($subject->grades as $grade)
                                                    <button class="success btn">{{ App\Models\SchoolClass::GRADES[$grade->grade] }}</button>
                                                @endforeach
                                            </td>
                                            <td class="text-center">{{ $subject->school ? $subject->school->school_name : "X"}}</td>
                                            <td class="text-center">
                                                <a style="margin-top: 3px" type="button"
                                                    class="main-action btn btn-flat btn-success"
                                                    href="{{ route('sysconf.subject.edit', ['id' => $subject->id]) }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                </a>
                                                <a style="margin-top: 3px" type="button"
                                                    class="main-action btn btn-flat btn-danger delete-item"
                                                    data-url="{{route('sysconf.subject.delete', ['id' => $subject->id])}}" 
                                                    href="javascript:void(0)">
                                                    <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
                                                    </span>
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

            $('#sysconf-subject').DataTable();

            $('#sysconf-subject').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá môn học này?');
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
                            alert('Đã xoá môn học');
                        }
                    });
                }
            });
        });

    </script>
@endsection
