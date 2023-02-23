@extends('layouts/contentLayoutMaster')

@section('title', "Sở GD $province->name")

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('css/plugins/forms/validation/form-validation.css')}}">
@endpush

@section('main')

    <!-- Form wizard with step validation section start -->
    <section id="validation">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <form method="POST"
                                  action="{{ route('admin.province.post_add_thpt_school', ['id' => $province->id]) }}"
                                  class="form">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="district">Quận huyện</label>
                                                <select class="custom-select form-control required select2 filter-district"
                                                        name="district">
                                                    @foreach($province->districts as $district)
                                                        <option value="{{ $district->id }}"  @if($district->id == $selectedDistrict->id) selected @endif>{!! $district->name !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="ward_id">Phường xã</label>
                                                <select class="custom-select form-control required select2"
                                                        name="ward_id">
                                                    @foreach($wards as $ward)
                                                        <option {{ strval($ward->id) === strval(old('ward')) ? 'selected' : '' }} value="{{ $ward->id }}">{{$ward->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="school_name">Tên trường</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Tên trường" name="school_name"
                                                       value="{{ old('school_name') }}"
                                                >
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="school_email">{{ trans('admin.email') }}</label>
                                                <input type="email" class="form-control"
                                                       placeholder="{{ trans('admin.email') }}" name="school_email"
                                                       value="{{ old('school_email') }}"
                                                >
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="school_phone">{{ trans('admin.phone') }}</label>
                                                <input type="text" class="form-control"
                                                       placeholder="{{ trans('admin.phone') }}" name="school_phone"
                                                       value="{{ old('school_phone') }}"
                                                >
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="school_address">{{ trans('admin.address') }}</label>
                                                <input type="text" class="form-control"
                                                       placeholder="{{ trans('admin.address') }}" name="school_address"
                                                       value="{{ old('school_address') }}"
                                                >
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="school_type">Loại trường</label>
                                                <select class="custom-select form-control required select2"
                                                        name="school_type">
                                                    @foreach($data['school_type'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('school_type')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit"
                                                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light">@lang('admin.submit')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Form wizard with step validation section end -->

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script type="text/javascript">

$(document).ready(function() {

    
    let searchParams = new URLSearchParams(window.location.search);

    var district = searchParams.get('district');


    if(district != null) {
        $('select[name="district"]').val(district);
    }

    $('.filter-district').on('change', function(){
        var url = "{!! route('admin.province.add_thpt_school', ['id' => $province->id ]) !!}";

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
@endsection

