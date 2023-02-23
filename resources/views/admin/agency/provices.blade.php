@extends('layouts/contentLayoutMaster')

@section('title', 'Danh sách các sở giáo dục và đào tạo trên cả nước')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
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
                            <p class="card-text">Bạn có thể sửa, xoá, thêm người quản lý cấp sở với các nút bấm trong
                                cột thao tác.</p>
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">GSO ID</th>
                                        <th scope="col">Ngày cập nhật</th>
                                        <th scope="col">Số lượng tài khoản</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($provinces as $key => $provice)
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $provice->name}}</td>
                                            <td>{{ $provice->gso_id}}</td>
                                            <td>{{ $provice->updated_at }}</td>
                                            <td>{{ count($provice->users) }}</td>
                                            <td>
                                                @if( Admin::user()->inRoles(['administrator', 'view.all', 'so-gd', 'customer-support']))
                                                    <a type="button" id="create_sgd_account"
                                                       name="{{ $provice->gso_id }}" class="btn btn-flat btn-info"
                                                       href="{{  route('admin.agency.province.view_account_list', ['id' => $provice->id])  }}">
                                                        <i class="fa fa-eyes" aria-hidden="true"></i>
                                                        {{ trans('admin.agency.account_list') }}
                                                    </a>
                                                    @if (count($provice->users) > 0)
                                                        <a type="button" id="create_sgd_account"
                                                           name="{{ $provice->gso_id }}"
                                                           class="btn btn-flat btn-warning"
                                                           href="{{  route('admin_user.create_more_sgd_account', ['gso_id' => $provice->gso_id])  }}">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                            {{ trans('admin.agency.create_more_account') }}
                                                        </a>
                                                       
                                                    @else
                                                        <a type="button" id="create_sgd_account"
                                                           name='{{ $provice->gso_id }}'
                                                           class="main-action btn btn-flat btn-success"
                                                           href="{{  route('admin_user.create_sgd_account', ['gso_id' => $provice->gso_id])  }}">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                            {{ trans('admin.agency.create_account') }}
                                                        </a>
                                                    @endif
                                                @endif
                                                <a type="button" class="btn btn-flat btn-info"
                                                   href="{{  route('admin.agency.districts', ['province' => $provice->id])  }}">
                                                    <i class="fa fa-eyes" aria-hidden="true"></i>
                                                    {{ trans('admin.agency.pgd_list') }}
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
@endsection
