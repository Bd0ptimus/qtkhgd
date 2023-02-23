@extends('layouts/contentLayoutMaster')

@php
    $title = 'Chuyên viên quản lý truờng';
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => 'Danh sách chuyên viên phòng giáo dục', 'link' => route('district.specialist_users', ['provinceId' => $provinceId, 'districtId' => $districtId])],
        ['name' => $title],
    ];
    $specialistSchoolId = [];
    if(isset($specialist) && isset($specialist->specialistSchool)) {
        $specialistSchoolId = $specialist->specialistSchool->pluck('id')->toArray();
    }

@endphp

@section('title', $title)
@section('vendor-style')
    {{-- Vendor Css files --}}
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection
@section('main')
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <form action="{{route('district.store.specialist_school')}}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="form-group">
                                    <div class='row'>
                                        <div class="col-sm-4">
                                            <label for="province" class="d-block">Tỉnh thành:</label>
                                            <select {{ !Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? 'disabled' : '' }}
                                                    class="form-control parent select2 filter-province"
                                                    style="width: 100%;">
                                                <option value="">Tất cả</option>
                                                @foreach ($provinces as $key => $province)
                                                    <option value="{{ $province->id }}"
                                                            @if($province->id == $provinceId) selected @endif>
                                                        {!! $province->name !!}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="district" class="d-block">Quận huyện:</label>
                                            <select {{ !Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? 'disabled' : '' }}
                                                    class="form-control parent select2 filter-district"
                                                    style="width: 100%;">
                                                <option value="">Tất cả</option>
                                                @foreach ($districts as $key => $district)
                                                    <option value="{{ $district->id }}"
                                                            @if($district->id == $districtId) selected @endif>{!! $district->name !!}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="user" class="d-block">Chuyên viên:</label>
                                            <select {{ !$districtId ? 'disabled' : '' }}
                                                    class="form-control parent select2 filter-user" style="width: 100%;"
                                                    name="specialist_id">
                                                <option value="">---Chọn chuyên viên---</option>
                                                @foreach ($users as $key => $user)
                                                    <option value="{{ $user->id }}"
                                                            @if($user->id == $specialistId) selected @endif>
                                                        {!! $user->name !!}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('specialist_id'))
                                                <label class="help-block text-danger">
                                                    <i class="fa fa-info-circle"></i> {{ $errors->first('specialist_id') }}
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-header">
                                @if($districtName)
                                    <h4 class="card-title">Danh sách các truờng thuộc
                                        <strong>{{ $districtName }}</strong></h4>
                                @endif
                            </div>
                            <div class="card-body card-dashboard">

                                <div class="form-group">
                                    <div class='row'>
                                        <div class="col-sm-12 mb-2">
                                            <label for="school" class="d-block">Truờng phụ trách:</label>
                                            <select {{ !$districtId ? 'disabled' : '' }}
                                                    class="form-control parent select2 filter-school"
                                                    style="width: 100%;" name="school_id[]" multiple="multiple">
                                                @foreach ($schools as $key => $school)
                                                    <option value="{{ $school->id }}"
                                                            @if(in_array($school->id, $schoolSelectedIds) && !in_array($school->id, $specialistSchoolId)) disabled @endif
                                                            @if(in_array($school->id, $specialistSchoolId)) selected @endif>
                                                        {!! $school->school_name !!}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('school_id'))
                                                <label class="help-block text-danger">
                                                    <i class="fa fa-info-circle"></i> {{ $errors->first('school_id') }}
                                                </label>
                                            @endif
                                        </div>
                                        <div class="col-sm-12 text-right">
                                            <button type="submit" class="btn btn-primary">Lưu</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $(document).ready(function () {
            $('.filter-province').change(function () {
                let newUrl = window.location.href.substring(0, window.location.href.indexOf('?'));
                let optionSelected = $(this).find("option:selected");
                let valueSelected = optionSelected.val();
                if (valueSelected == '') window.location.href = newUrl;
                let searchParams = new URLSearchParams(window.location.search)
                if (searchParams.has('provinceId')) {
                    window.location.href = newUrl + `?provinceId=${valueSelected}`;
                } else {
                    window.location.href = window.location.href + `?provinceId=${valueSelected}`;
                }
            });

            $('.filter-district').change(function () {
                let newUrl = window.location.href.substring(0, window.location.href.indexOf('&districtId'));
                let optionSelected = $(this).find("option:selected");
                let valueSelected = optionSelected.val();
                if (valueSelected == '') window.location.href = newUrl;
                let searchParams = new URLSearchParams(window.location.search)
                if (searchParams.has('districtId')) {
                    window.location.href = newUrl + `&districtId=${valueSelected}`;
                } else {
                    window.location.href = window.location.href + `&districtId=${valueSelected}`;
                }
            });

            $('.filter-user').change(function () {
                let newUrl = window.location.href.substring(0, window.location.href.indexOf('&specialistId'));
                let optionSelected = $(this).find("option:selected");
                let valueSelected = optionSelected.val();
                if (valueSelected == '') window.location.href = newUrl;
                let searchParams = new URLSearchParams(window.location.search)
                if (searchParams.has('specialistId')) {
                    window.location.href = newUrl + `&specialistId=${valueSelected}`;
                } else {
                    window.location.href = window.location.href + `&specialistId=${valueSelected}`;
                }
            });
        });
    </script>
@endsection