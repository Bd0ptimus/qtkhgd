@extends('layouts/contentLayoutMaster')

@php
    $title = 'Quản lý phòng '.$district->name;
    $breadcrumbs = [
        ['name' => 'Danh sách phòng', 'link' => route('district.index')],
        ['name' => $district->name, 'link' => route('district.manage', ['id' => $district->id])],
    ];
@endphp
@section('title', $title)

@section('vendor-style')
    {{-- Vendor Css files --}}
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/swiper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jquery.contextMenu.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/swiper.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/context-menu.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/toastr.css')) }}">
@endsection

@section('main')

    {{-- Statistics card section start --}}
    <section id="statistics-card">
        <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700">{{ $schoolCount }}</h2>
                            <p class="mb-0">Trường</p>
                        </div>
                        <div class="avatar bg-rgba-primary p-50">
                            <div class="avatar-content">
                                <i class="feather icon-monitor text-primary font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-content" style="margin: 10px">
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700">{{ $staffCount }}</h2>
                            <p class="mb-0">Nhân viên</p>
                        </div>
                        <div class="avatar bg-rgba-success p-50">
                            <div class="avatar-content">
                                <i class="feather icon-user-check text-success font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-content" style="margin: 10px">
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section id="component-swiper-centered-slides-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Quản lý thông tin</h4>
            </div>
            <div class="card-content">
                <div class="card-body pt-0">
                    <div class="swiper-centered-slides-2 swiper-container p-1">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('district.users', ['id' => $district->id]) }}">Quản lý tài khoản phòng</a></div>
                            </div>

                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('district.specialist_users', ['provinceId' => $district->province->id, 'districtId' => $district->id]) }}">Quản lý chuyên viên phòng</a></div>
                            </div>
                           
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('school.index',['provinceId' => $district->province->id, 'districtId' => $district->id]) }}">Xem hoạt động các trường</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Quản trị kế hoạch</h4>
            </div>
            <div class="card-content">
                <div class="card-body pt-0">
                    <div class="swiper-centered-slides-2 swiper-container p-1">
                        <div class="swiper-wrapper">           
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('district.schools.pending_school_plans',['districtId' => $district->id]) }}">Duyệt kế hoạch các trường</a></div>
                            </div>
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('district.schools.school_plans',['districtId' => $district->id]) }}">Kế hoạch giáo dục các trường</a></div>
                            </div>
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('district.schools.group_plans',['districtId' => $district->id]) }}">Kế hoạch giáo dục tổ chuyên môn các trường</a></div>
                            </div>

                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('district.schools.teacher_plans',['districtId' => $district->id]) }}">Kế hoạch giáo dục giáo viên các trường</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/extensions/swiper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/jquery.contextMenu.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/jquery.ui.position.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/extensions/swiper.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/context-menu.js')) }}"></script>
@endsection

@push('scripts')

    <script type="text/javascript">


    </script>

@endpush