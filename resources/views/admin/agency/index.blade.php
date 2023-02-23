@extends('layouts/contentLayoutMaster')

@section('title', 'Danh sách đơn vị theo địa chính')

@section('vendor-style')
        {{-- Vendor Css files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('page-style')
        {{-- Page Css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
@endsection
@section('main')
{{-- Statistics card section start --}}
<section id="statistics-card">
    <div class="row">
        <div class="col-xl-4 col-md-4 col-sm-6">
          <div class="card text-center">
            <div class="card-content">
              <div class="card-body">
                <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                  <div class="avatar-content">
                    <a href="{{ route('admin.agency.provinces') }}"><i class="feather icon-award text-success font-medium-5"></i></a>
                  </div>
                </div>
                <h2 class="text-bold-700">{{count($total_provinces)}}</h2>
                <p class="mb-0 line-ellipsis">Sở giáo dục và đào tạo</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-4 col-sm-6">
          <div class="card text-center">
            <div class="card-content">
              <div class="card-body">
                <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                  <div class="avatar-content">
                  <a href="{{ route('admin.agency.districts') }}"><i class="feather icon-award text-success font-medium-5"></i></a>
                  </div>
                </div>
                <h2 class="text-bold-700">{{count($total_districts)}}</h2>
                <p class="mb-0 line-ellipsis">Phòng giáo dục và đào tạo</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-4 col-sm-6">
          <div class="card text-center">
            <div class="card-content">
              <div class="card-body">
                <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                  <div class="avatar-content">
                    <a href="{{ route('admin.agency.wards') }}"><i class="feather icon-award text-success font-medium-5"></i></a>
                  </div>
                </div>
                <h2 class="text-bold-700">{{count($total_wards)}}</h2>
                <p class="mb-0 line-ellipsis">Xã phường</p>
              </div>
            </div>
          </div>
        </div>
    </div>
</section>
{{-- // Statistics Card section end--}}
@endsection
@section('vendor-script')
{{-- Vendor js files --}}
        <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection
@section('page-script')
{{-- Page js files --}}
        <script src="{{ asset(mix('js/scripts/cards/card-statistics.js')) }}"></script>
@endsection
