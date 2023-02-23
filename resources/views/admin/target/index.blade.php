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
                        <h4 class="card-title">Kho chỉ tiêu</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @if($permission)
                            <div class="d-flex ">
                                <div class="btn-group text-nowrap">
                                    <a type="button"
                                        class="main-action btn btn-flat btn-success"
                                        href="{{ route('target.create') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm chỉ tiêu
                                    </a>
                                </div>
                            </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-subject">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên chỉ tiêu</th>
                                        <th scope="col">Loại chỉ tiêu</th>
                                        <th scope="col">Cấp học</th>
                                       <!--  <th scope="col">Mô tả</th>
                                        <th scope="col">Giải pháp</th> -->
                                        <th scope="col">Chỉ số</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($targets as $key => $target)
                                        
                                        <tr>
                                            <td scope="row" class="font-weight-bold">{{ $key + 1}}</td>
                                            <td class="font-weight-bold">{{ $target->title}}</td>
                                            <td>{{ TARGET_TYPES[$target->type] ?? ''}}</td>
                                            <td>{{ SCHOOL_TYPES[$target->school_type] ?? ''}}</td>
                                            <!-- <td><pre>{{ $target->description}}</pre></td>
                                            <td><pre>{{ $target->solution}}</pre></td> -->
                                            <td>{{ $target->final_target . '%' }}</td>
                                            <td>
                                            @if($permission)
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="main-action btn btn-datatable btn-warning"
                                                    href="{{ route('target.edit', ['id' => $target->id]) }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                </a>

                                                

                                                <a style="margin-top: 3px" type="button"
                                                    class="main-action btn btn-datatable btn-danger delete-item"
                                                    data-url="{{route('target.delete', ['id' => $target->id])}}" href="#">
                                                    <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
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

            $('#shool-subject').DataTable();

            $('#shool-subject').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá chỉ tiêu này?');
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
                            alert('Đã xoá chỉ tiêu');
                        }
                    });
                }
            });
        });

    </script>
@endsection
