@extends('layouts/contentLayoutMaster')

@section('title', 'Danh sách các phòng giáo dục và đào tạo trên cả nước')

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
                        <h4 class="card-title">Danh sách các phòng giáo dục và đào tạo trên cả nước</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <p class="card-text">Bạn có thể sửa, xoá, thêm người quản lý cấp sở với các nút bấm trong
                                cột thao tác.</p>
                            <form class="d-flex flex-wrap mb-1" method="GET"
                                  action="{{ route('district.index') }}">
                                <div class="mr-1">
                                    <select class="custom-select form-control required select2"
                                            name="province" data-placeholder="Chọn tỉnh">
                                        <option></option>
                                        @foreach($provinces as $value)
                                            <option value="{{$value['id']}}" {{ strval($value['id']) === strval($province) ? 'selected' : '' }}>{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                                    {{ trans('admin.apply') }}
                                </button>
                            </form>
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tỉnh / Thành phố</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">GSO ID</th>
                                        <th scope="col">Ngày cập nhật</th>
                                        <th scope="col">Số lượng tài khoản</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($districts as $key => $district)
                                        @if (count($district->users) > 0)
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $district->province->name}}</td>
                                            <td>{{ $district->name}}</td>
                                            <td>{{ $district->gso_id}}</td>
                                            <td>{{ $district->updated_at}}</td>
                                            <td>{{ count($district->users) }}</td>
                                            <td>
                                                @if( Admin::user()->inRoles(['administrator', 'customer-support']))

                                                    @if (count($district->users) > 0)
                                                        <a type="button" id="create_sgd_account"
                                                           name='{{ $district->gso_id }}' class="btn btn-flat btn-info"
                                                           href="{{  route('admin.agency.district.view_account_list', ['id' => $district->id])  }}">
                                                            <i class="fa fa-bars" aria-hidden="true"></i> DS TK
                                                        </a>
                                                        <a type="button" id="create_sgd_account"
                                                           name='{{ $district->gso_id }}' class="btn btn-flat btn-info"
                                                           href="{{  route('school.maugiao_tieuhoc_thcs') }}?provinceId={{$district->province->id}}&districtId={{$district->id}}">
                                                            <i class="fa fa-bars" aria-hidden="true"></i> DS Trường
                                                        </a>
                                                        <a type="button" id="create_sgd_account"
                                                           name="{{ $district->gso_id }}"
                                                           class="btn btn-flat btn-warning"
                                                           href="{{  route('admin_user.create_more_pgd_account', ['gso_id' => $district->gso_id])  }}">
                                                            <i class="fa fa-plus" aria-hidden="true"></i> Thêm TK
                                                        </a>
                                                        <a style="margin-top: 3px" type="button" id="create_sgd_account"
                                                           name="{{ $district->gso_id }}"
                                                           class="btn btn-flat btn-success"
                                                           href="{{  route('admin.agency.districts.add_school', ['id' => $district->id ]) }}">
                                                            <i class="fa fa-plus" aria-hidden="true"></i> Thêm Trường
                                                        </a>

                                                        <a style="margin-top: 3px" type="button" id="create_sgd_account"
                                                           name="{{ $district->gso_id }}"
                                                           class="btn btn-flat btn-success"
                                                           href="{{  route('admin.agency.districts.export_account', ['id' => $district->id ]) }}">
                                                            <i class="fa fa-download" aria-hidden="true"></i> Export TK
                                                        </a>

                                                        <a style="margin-top: 3px" type="button" id="create_sgd_account"
                                                           name="{{ $district->gso_id }}"
                                                           class="btn btn-flat btn-success"
                                                           href="{{  route('admin.agency.districts.import_school', ['id' => $district->id ]) }}">
                                                            <i class="fa fa-plus" aria-hidden="true"></i> Import Trường
                                                        </a>
                                                        <a type="button"
                                                           name='{{ $district->gso_id }}' class="btn btn-flat btn-info"
                                                           href="{{  route('district.manage', ['id' => $district->id])  }}">
                                                            <i class="fa fa-bars" aria-hidden="true"></i> Xem hoạt động
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
