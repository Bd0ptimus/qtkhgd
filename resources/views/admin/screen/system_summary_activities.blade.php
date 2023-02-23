@extends('layouts/contentLayoutMaster')

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
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                                <form method="POST" action="" class="form row">
                                @csrf
                                    <div class="col-md-3">
                                        <label>Từ ngày</label>
                                        <input placeholder="Từ ngày" class="form-control" type="date" name="filter_start_date" value="{{ date('Y-m-d', strtotime($filter_start_date)) ?? null }}" required/>
                                    </div><!-- Ngày bắt đầu -->

                                    <div class="col-md-3">
                                        <label>Đến ngày</label>    
                                        <input placeholder="Đến ngày" class="form-control" type="date" name="filter_end_date" value="{{ date('Y-m-d', strtotime($filter_end_date)) ?? null }}" required/>
                                    </div><!-- Ngày kết thúc -->
                                    <div class="col-md-3  nopadding-left">
                                    <label> </label>
                                        <button type="submit" class="btn btn-default form-control"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    </div>
                                </form>
                            <div class="table-responsive">
                                <table class="table zero-configuration table-bordered table-striped text-nowrap" id="table" style="border-spacing: 1px">
                                    <thead>
                                        <tr>
                                            <th scope="col">Trường</th>
                                            <th scope="col">Huyện</th>
                                            <th scope="col">Tỉnh</th>
                                            <th scope="col">Import học sinh</th>
                                            <th scope="col">Import nhân viên</th>
                                            <th scope="col">Theo dõi sức khỏe định kì học sinh</th>
                                            <th scope="col">Theo dõi sức khỏe bất thường học sinh</th>
                                            <th scope="col">Khám chuyên khoa học sinh</th>
                                            <th scope="col">Theo dõi sức khỏe bất thường nhân viên</th>
                                            <th scope="col">Khám chuyên khoa nhân viên</th>
                                            <th scope="col">Bảo hiểm</th>
                                            <th scope="col">Thuốc</th>
                                            <th scope="col">Trang thiết bị</th>
                                            <th scope="col">Vệ sinh học đường</th>
                                            <th scope="col">An toàn thực phẩm</th>
                                            <th scope="col">Cơ sở vật chất</th>
                                            <th scope="col">Báo cáo theo quy định</th>
                                            <th scope="col">Báo cáo theo yêu cầu</th>
                                            <th scope="col">Báo cáo động</th>
                                            <th scope="col">Báo cáo Covid</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($summary as $index => $school)
                                        <tr>
                                            <td>{{ $school['school_name'] }}</td>
                                            <td>{{ $school['district'] }}</td>
                                            <td>{{ $school['province'] }}</td>
                                            <td>{{ $school['import_student'] }}</td>
                                            <td>{{ $school['import_staff'] }}</td>
                                            <td>{{ $school['period_check'] }}</td>
                                            <td>{{ $school['student_health_absnormal'] }}</td>
                                            <td>{{ $school['student_specilist_check'] }}</td>
                                            <td>{{ $school['staff_health_absnormal'] }}</td>
                                            <td>{{ $school['staff_specilist_check'] }}</td>
                                            <td>{{ $school['insurance'] }}</td>
                                            <td>{{ $school['medicines'] }}</td>
                                            <td>{{ $school['equipment'] }}</td>
                                            <td>{{ $school['sanitation'] }}</td>
                                            <td>{{ $school['food_inspection'] }}</td>
                                            <td>{{ $school['csvc'] }}</td>
                                            <td>{{ $school['basic_report'] }}</td>
                                            <td>{{ $school['advandce_report'] }}</td>
                                            <td>{{ $school['dynamic_report'] }}</td>
                                            <td>{{ $school['covid_report'] }}</td>
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
    <script>
        $(document).ready(function () {
            
            var table = $('#table').DataTable({
                retrieve: true,
                
                columnDefs: [{ 'targets': 0, type: 'date-euro' }],
                order: [0, 'desc'],
                paging: false,
            });
        });    
    
    </script>
@endsection
