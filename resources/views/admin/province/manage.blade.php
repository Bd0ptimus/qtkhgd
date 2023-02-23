@extends('layouts/contentLayoutMaster')

@php
    $title = 'Quản lý sở '.$province->name;
    $breadcrumbs = [
        ['name' => 'Danh sách sở', 'link' => route('province.index')],
        ['name' => $province->name, 'link' => route('province.manage', ['id' => $province->id])],
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
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700">{{ $studentCount }}</h2>
                            <p class="mb-0">Học Sinh</p>
                        </div>
                        <div class="avatar bg-rgba-warning p-50">
                            <div class="avatar-content">
                                <i class="feather icon-mail text-warning font-medium-5"></i>
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
                <h4 class="card-title">Cấu hình thông tin</h4>
            </div>
            <div class="card-content">
                <div class="card-body pt-0">
                    <div class="swiper-centered-slides-2 swiper-container p-1">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank"
                                                            href="{{ route('school.school_list',['provinceId' => $province->id]) }}">
                                        Danh sách trường</a></div>
                            </div>
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank"
                                                            href="{{ route('school.index',['provinceId' => $province->id]) }}">
                                        Xem hoạt động các trường</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="dropdown-with-outline-btn">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cập nhật thông tin</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="btn-group dropdown mr-1 mb-1">
                                <button class="btn btn-outline-success">
                                    Quản lý Bảo hiểm y tế
                                </button>
                                <button type="button"
                                        class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <div class="dropdown-menu">
                                    <a target="_blank" class="dropdown-item"
                                       href="{{ route('province.manage_insurance.district', ['province' => $province->id]) }}">
                                        Bảo hiểm y tế các Phòng GD
                                    </a>
                                    <a target="_blank" class="dropdown-item"
                                       href="{{route('province.manage_insurance.thpt', ['province' => $province->id])}}">
                                        Bảo hiểm y tế các trường THPT
                                    </a>
                                </div>
                            </div> <!-- Bảo hiểm y tế cấp Sở -->
                            <div class="btn-group dropdown mr-1 mb-1">
                                <button class="btn btn-outline-success">
                                    Quản lý chất lượng phòng học
                                </button>
                                <button type="button"
                                        class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <div class="dropdown-menu">
                                    <a target="_blank" class="dropdown-item"
                                       href="{{ route('province.room-analytics.district', ['province' => $province->id]) }}">Chất
                                        lượng phòng học Phòng GD</a>
                                    <a target="_blank" class="dropdown-item"
                                       href="{{route('province.room-analytics.thpt', ['province' => $province->id])}}">Chất
                                        lượng phòng học Trường THPT</a>
                                </div>
                            </div> <!-- Quản lý chất lượng phòng học cấp sở -->
                            <div class="btn-group dropdown mr-1 mb-1">
                                <button class="btn btn-outline-success">
                                    Báo cáo công tác y tế
                                </button>
                                <button type="button"
                                        class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <div class="dropdown-menu">
                                    <a target="_blank" class="dropdown-item"
                                       href="{{ route('province.pl02.district.send', ['province' => $province->id]) }}">
                                        Báo cáo PL02 phòng GD
                                    </a>
                                    <a target="_blank" class="dropdown-item"
                                       href="{{route('province.pl03.district.result', ['province' => $province->id])}}">
                                        Báo cáo PL03 phòng GD
                                    </a>
                                    <a target="_blank" class="dropdown-item"
                                       href="{{route('province.pl04.district.result', ['province' => $province->id])}}">
                                        Báo cáo PL04 phòng GD
                                    </a>
                                    <a target="_blank" class="dropdown-item"
                                       href="{{ route('province.pl02.thpt.send', ['province' => $province->id]) }}">
                                        Báo cáo PL02 THPT
                                    </a>
                                    <a target="_blank" class="dropdown-item"
                                       href="{{route('province.pl04.thpt.result', ['province' => $province->id])}}">
                                        Báo cáo PL04 THPT
                                    </a>
                                </div>
                            </div> <!-- Báo cáo công tác y tế -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="component-swiper-centered-slides-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tổng hợp thống kê</h4>
            </div>
            <div class="card-content">
                <div class="card-body pt-0">
                    <div class="swiper-centered-slides-2 swiper-container p-1">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide rounded py-1 px-1 d-flex">
                                <div class="swiper-text" type="button" id="infectious-diseases-reports">
                                    <a target="_blank" href="#">Báo cáo bệnh truyền nhiễm</a>
                                </div>
                            </div>
                            <div class="swiper-slide rounded py-1 px-1 d-flex">
                                <div class="swiper-text" type="button" id="covid-reports">
                                    <a target="_blank" href="#">Báo cáo Covid</a>
                                </div>
                            </div>
                            <div class="swiper-slide rounded py-1 px-1 d-flex">
                                <div class="swiper-text" type="button" id="medical-declaration-report-district">
                                    <a class="text-danger" target="_blank" href="{{ route('province.medical_declaration_report.district', ['provinceId' => $province->id]) }}">Báo cáo khai báo y tế các Phòng GD</a>
                                </div>
                            </div>
                            <div class="swiper-slide rounded py-1 px-1 d-flex">
                                <div class="swiper-text" type="button" id="medical-declaration-report-thpt">
                                    <a class="text-danger" target="_blank" href="{{ route('province.medical_declaration_report.thpt', ['provinceId' => $province->id]) }}">Báo cáo khai báo y tế các trường THPT</a>
                                </div>
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
        var reportItem = {
            "/portal/province/pl02/district/send?provinceId={{$province->id}}": {name: "Báo cáo PL02"},
            "/portal/province/pl03/district/send?districtId=&provinceId={{$province->id}}": {name: "Báo cáo PL03"},
            "/portal/province/pl04/district/send?districtId=&provinceId={{$province->id}}": {name: "Báo cáo PL04"},
        }


        $.contextMenu({
            selector: '#phong-reports',
            trigger: "left",
            callback: function (key, options) {
                window.location.replace(key);
            },
            items: reportItem
        });

        $.contextMenu({
            selector: '#thpt-reports',
            trigger: "left",
            callback: function (key, options) {
                window.location.replace(key);
            },
            items: {
                "/portal/province/pl02/thpt/send?provinceId={{$province->id}}": {name: "Báo cáo PL02"},
                "/portal/province/pl04/thpt/send?provinceId={{$province->id}}": {name: "Báo cáo PL04"},
            }
        });
        $.contextMenu({
            selector: '#infectious-diseases-reports',
            trigger: "left",
            callback: function (key, options) {
                window.location.replace(key);
            },
            items: {
                "{{ route('province.infectious_diseases_statistics.district', ['province' => $province->id]) }}": {name: "BC bênh truyền nhiễm theo Phòng GD"},
                "{{route('province.infectious_diseases_statistics.thpt', ['province' => $province->id])}}": {name: "BC bệnh truyền nhiễm THPT"},
            }
        });

        $.contextMenu({
            selector: '#covid-reports',
            trigger: "left",
            callback: function (key, options) {
                window.location.replace(key);
            },
            items: {
                "{{ route('province.covid.district', ['provinceId' => $province->id]) }}": {name: "Báo cáo Covid từ các Phòng GD"},
                "{{route('province.covid.thpt', ['provinceId' => $province->id])}}": {name: "Báo cáo Covid từ các trường THPT"},
            }
        });

    </script>

@endpush