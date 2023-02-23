@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = 'Danh sách thời khoá biểu';
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
                            <div class="d-flex">
                                <div class="btn-group text-nowrap ml-1">
                                    <a type="button"
                                        class="main-action btn btn-flat btn-success"
                                        href="{{ route('school.timetable.auto_genderate', ['id' => $school->id]) }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Tạo thời khoá biểu mới tự động
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-timetable">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Từ ngày</th>
                                        <th scope="col">Đến ngày</th>
                                        <th scope="col">Kích hoạt</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($timetables as $key => $timetable)
                                        
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $timetable->from_date }}</td>
                                            <td>{{ $timetable->to_date }}</td>
                                            <td>{{ $timetable->is_actived ? "Đang kích hoạt" : 'Chưa kích hoạt' }}
                                            <td>
                                                @if(!$timetable->is_actived)
                                                    <a style="margin-top: 3px" type="button" 
                                                        name=""
                                                        class="main-action btn btn-flat btn-info active-item"
                                                        data-url="{{ route('school.timetable.active', ['id' => $school->id, 'timetableId' => $timetable->id]) }}"
                                                        href="#">
                                                        <i class="fa fa-check" aria-hidden="true"></i>Kích hoạt TKB này
                                                    </a>
                                                @endif
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="main-action btn btn-flat btn-success"
                                                    href="{{ route('school.timetable.edit', ['id' => $school->id, 'timetableId' => $timetable->id]) }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                </a>
                                                <a style="margin-top: 3px" type="button"
                                                    class="main-action btn btn-flat btn-danger delete-item"
                                                    data-url="{{route('school.timetable.delete', ['id' => $school->id, 'timetableId'  => $timetable->id])}}" 
                                                    href="#">
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

            $('#shool-timetable').DataTable();

            $('#shool-timetable').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá thời khoá biểu này này?');
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
                            alert('Đã xoá Thời khoá biểu');
                        }
                    });
                }
            });

            $('#shool-timetable').on('click','.active-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn kích hoạt thời khoá biểu này?');
                if(confirmDelete) {
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            window.location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endsection
