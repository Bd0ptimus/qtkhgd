@extends('layouts/contentLayoutMaster')

@section('title', 'Danh sách các sở giáo dục và đào tạo trên cả nước')

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
                        <h4 class="card-title">Danh sách các sở giáo dục và đào tạo trên cả nước</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <p class="card-text"> </p>
                            
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">GSO ID</th>
                                        
                                        <th scope="col">Số lượng phòng giáo dục</th>
                                        <th scope="col">Số lượng trường</th>
                                        <th scope="col">Số lượng trường THPT</th>
                                        <th scope="col">Số lượng nhân viên</th>
                                        <th scope="col">Số lượng học sinh</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($provinces as $key => $province)
                                        @if (count($province->users) > 0)
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $province->name}}</td>
                                            <td>{{ $province->gso_id}}</td>
                                            
                                            <td>{{ $province->total_phong_gd}}</td>
                                            <td>{{ $province->total_school}}</td>
                                            <td>{{ $province->total_thpt}}</td>
                                            <td>{{ $province->total_staff}}</td>
                                            <td>{{ $province->total_student}}</td>
                                            <td>
                                                @if( Admin::user()->inRoles(['administrator', 'customer-support']))
                                                    @if (count($province->users) > 0)
                                                    <a style="margin-top: 3px" type="button" 
                                                        name="{{ $province->gso_id }}"
                                                        class="btn btn-flat btn-success"
                                                        href="{{  route('admin.province.add_thpt_school', ['id' => $province->id ]) }}">
                                                        <i class="fa fa-plus" aria-hidden="true"></i> Thêm Trường THPT
                                                    </a>
                                                    <a style="margin-top: 3px" type="button"
                                                        name='{{ $province->gso_id }}' class="btn btn-flat btn-success"
                                                        href="{{  route('admin.province.export_thpt_account', ['id' => $province->id])  }}">
                                                        <span title="Export TK THPT"><i class="fa fa-download" aria-hidden="true"></i> Export TK THPT
                                                    </a>
                                                    
                                                    
                                                    <a style="margin-top: 3px" type="button" 
                                                        name="{{ $province->gso_id }}"
                                                        class="btn btn-flat btn-success"
                                                        href="{{  route('admin.province.import_thpt_school', ['id' => $province->id ]) }}">
                                                        <i class="fa fa-plus" aria-hidden="true"></i> Import Trường THPT
                                                    </a>
                                                    <a style="margin-top: 3px" type="button" id="create_sgd_account"
                                                        name='{{ $province->gso_id }}' class="btn btn-flat btn-info"
                                                        href="{{  route('province.manage', ['id' => $province->id])  }}">
                                                        <span title="Xem hoạt động"><i class="fa fa-bars" aria-hidden="true"></i> Xem hoạt động
                                                    </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
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
            })
        });
    </script>
@endsection
