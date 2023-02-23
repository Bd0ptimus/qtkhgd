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

            <div class="mr-1 mb-1">
                <a href="{{ route('admin.school.user_activity', ['school_id' => $school->id]) }}"
                type="button"
                class="btn {{ \Request::route()->getName() == 'admin.school.user_activity' ? 'active' : ''}} btn-outline-success">
                    Chi tiết lịch sử</a>
                <a href="{{ route('admin.school.activities_summary', ['school_id' => $school->id]) }}"
                class="btn {{ \Request::route()->getName() == 'admin.school.activities_summary' ? 'active' : ''}} btn-outline-success">
                    Thống kê lịch sử</a>
            </div><!-- List functions -->

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
                                        <input placeholder="Từ ngày" class="form-control" type="datetime-local" name="filter_start_date" value="{{ date('Y-m-d\TH:i', strtotime($filter_start_date)) ?? null }}" required/> 
                                        
                                    </div><!-- Ngày bắt đầu -->

                                    <div class="col-md-3">
                                        <label>Đến ngày</label>    
                                        <input placeholder="Đến ngày" class="form-control" type="datetime-local" name="filter_end_date" value="{{ date('Y-m-d\TH:i', strtotime($filter_end_date)) ?? null }}" required/>
                                        
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
                                        <th scope="col">Thời gian</th>
                                        <th scope="col">Thực hiển bởi</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($user_activities as $user_activity)
                                        <tr>
                                            <td>{{ $user_activity->created_at }}</td>
                                            <td>{{ $user_activity->user->name ?? null }}</td>
                                            <td>{{ $user_activity->activity }}</td>
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
