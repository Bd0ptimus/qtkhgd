@extends('layouts/contentLayoutMaster')
@php
    $breadcrumbs;
@endphp
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
                        <h4 class="card-title">Thống kê chỉ tiêu đã giao</h4>
                        <h4 class="card-title">Kết quả trung bình: {{ $currentTarget->getSummaryResult() }}%</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                           
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-subject">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Giáo viên</th>
                                        <th scope="col">Tên chỉ tiêu</th>
                                        <th scope="col">Loại chỉ tiêu</th>
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Giải pháp</th>
                                        <th scope="col">Kết quả </th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($currentTarget->staffTargets as $key => $target)
                                        
                                        <tr>
                                            <td scope="row" class="font-weight-bold">{{ $key + 1}}</td>
                                            <td class="font-weight-bold">{{ $target->staff->fullname}}</td>
                                            <td class="font-weight-bold">{{ $target->title}}</td>
                                            <td>{{ TARGET_TYPES[$target->type] ?? ''}}</td>
                                            <td><pre>{{ $target->description}}</pre></td>
                                            <td><pre>{{ $target->solution}}</pre></td>
                                            <td>{{ $target->getSummaryResult() }} %</td>
                                           
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
