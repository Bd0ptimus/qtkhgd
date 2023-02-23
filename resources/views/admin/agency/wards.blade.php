
@extends('layouts/contentLayoutMaster')

@section('title', 'DataTables')

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
                        <h4 class="card-title">Danh sách các phường/xã trên cả nước</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <p class="card-text">Nạn có thể thêm sửa, xoá xã với các nút bấm trong cột thao tác.</p>

                            <div class="form-group   {{ $errors->has('pay_type') ? ' has-error' : '' }}">
                                <div class='row'>
                                    <div class="col-sm-3">
                                        <select class="form-control parent select2 filter-province" style="width: 100%;"
                                                {{ !Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? 'disabled' : '' }}
                                                name="province" >
                                            @foreach ($provinces as $key => $province)
                                                <option value="{{ $province->id }}" @if($province->id == $selectedProvince->id) selected @endif>{!! $province->name !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <select class="form-control parent select2 filter-district" style="width: 100%;"
                                                {{ !Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_SO_GD]) ? 'disabled' : '' }}
                                                name="district" >
                                            @foreach ($districts as $key => $district)
                                                <option value="{{ $district->id }}"  @if($district->id == $selectedDistrict->id) selected @endif>{!! $district->name !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="main-action btn btn-primary" data-toggle="modal" data-target="#inlineForm">
                                        <i class="fa fa-plus"></i> Thêm phường xã
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">Tên Phường/Xã</th>
                                            <th scope="col">Thuộc Quận/Huyện</th>
                                            <th scope="col">Tỉnh</th>
                                            <th scope="col">GSO ID</th>
                                            <th scope="col">Ngày cập nhật</th>
                                            <th scope="col">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wards as $key => $ward)
                                            <tr>
                                                <th scope="row">{{ $key + 1}}</th>
                                                <td>{{ $ward->name}}</td>
                                                <td>{{ $ward->district->name}}</td>
                                                <td>{{ $ward->district->province->name}}</td>
                                                <td>{{ $ward->gso_id}}</td>
                                                <td>{{ $ward->updated_at }}</td>
                                                <td>
                                                @if( Admin::user()->inRoles(['administrator', 'customer-support']))
                                                <a type="button" 
                                                    name='{{ $ward->gso_id }}' class="btn btn-flat btn-info"
                                                    href="{{  route('admin.agency.ward.view_account_list', ['id' => $ward->id])  }}">
                                                    <i class="fa fa-bars" aria-hidden="true"></i> DS TK
                                                </a>
                                                <a type="button"
                                                    name='{{ $ward->gso_id }}' class="btn btn-flat btn-info"
                                                    href="{{  route('ward.manage.view_school', ['id' => $ward->id]) }}">
                                                    <i class="fa fa-bars" aria-hidden="true"></i> DS Trường
                                                </a>
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

          {{-- Modal --}}
            <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Thêm phường xã</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="POST">
                        @csrf
                        <div class="modal-body">
                        
                        <label>Tên phường xã</label>
                        <div class="form-group">
                            <input required type="text" name="ward_name" placeholder="Tên Phường Xã" class="form-control">
                        </div>

                        
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
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
@endsection
@push('scripts')

<script type="text/javascript">

$(document).ready(function() {
    $('.select2').select2({
        allowClear: true
    });
    
    let searchParams = new URLSearchParams(window.location.search);
    var province = searchParams.get('province');
    var district = searchParams.get('district');
    if(province != null) {
        $('select[name="province"]').val(province);
    }

    if(district != null) {
        $('select[name="district"]').val(district);
    }

    $('.filter-province').on('change', function(){
        var url = "{!! route('admin.agency.wards') !!}";
        if($('select[name="province"]').val() != '') {
            url = addUrlParam(url, 'province', $('select[name="province"]').val());
        }

        window.location.replace(url);
    });

    $('.filter-district').on('change', function(){
        var url = "{!! route('admin.agency.wards') !!}";
        if($('select[name="province"]').val() != '') {
            url = addUrlParam(url, 'province', $('select[name="province"]').val());
        }

        if($('select[name="district"]').val() != '') {
            url = addUrlParam(url, 'district', $('select[name="district"]').val());
        }

        window.location.replace(url);
    });

    function addUrlParam(url, param, value) {
        if(url.includes('?')) {
            url += `&${param}=${value}`;
        }else{
            url += `?${param}=${value}`;
        }
        return url;
    }
});

</script>

@endpush