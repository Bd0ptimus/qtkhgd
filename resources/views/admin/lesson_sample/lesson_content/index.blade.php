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
                        <h4 class="card-title">Danh sách các nội dung</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex">
                                <div class="btn-group text-nowrap">
                                    <a type="button"
                                        class="main-action btn btn-flat btn-success"
                                    href="{{route('lesson_sample.lesson_content.create',['id' => $lessonsampleId]) }} "  >
                                   
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm nội dung
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-subject">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên nội dung </th>
                                        <th scope="col">Nội dung bổ sung</th> 
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>  
                                        @foreach($lessoncontents as $key => $lessoncontent)
                                        <tr>
                                            <td scope="row" class="font-weight-bold">{{ $key + 1}}</td>
                                            <td class="font-weight-bold">{{ $lessoncontent->name}}</td>
                                            <td>{{ $lessoncontent->additional_content}}</td>
                                            <td>                                               
                                                    <a style="margin-top: 3px" type="button" 
                                                        name=""
                                                        class="main-action btn btn-flat btn-success"
                                                      href="{{ route('lesson_sample.lesson_content.edit', ['id' => $lessonsampleId, 'lesson_sample_id' => $lessoncontent->id]) }}">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                    </a>

                                                    <a style="margin-top: 3px" type="button"
                                                        class="main-action btn btn-flat btn-danger delete-item"
                                                        data-url="{{route('lesson_sample.lesson_content.delete', ['id' => $lessonsampleId, 'lesson_sample_id' => $lessoncontent->id])}}" href="#">
                                                        <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
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
            $('.select2').select2({
                allowClear: true
            });

            $('#shool-subject').DataTable();

            $('#shool-subject').on('click','.delete-item',function (e) {
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
