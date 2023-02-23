@extends('layouts/contentLayoutMaster')
@php $province = $district->province @endphp
@section('title', "Phòng GD $district->name - $province->name")

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
                                  action="{{ route('admin.agency.districts.post_add_school', ['id' => $district->id]) }}"
                                  class="form">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('ward_id') ? ' error' : '' }}">
                                                <label for="ward_id">Phường xã</label>
                                                <select class="custom-select form-control required select2"
                                                        name="ward_id">
                                                    @foreach($district->wards as $ward)
                                                        <option {{ strval($ward->id) === strval(old('ward')) ? 'selected' : '' }} value="{{ $ward->id }}">{{$ward->name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('ward_id'))
                                                    <span class="help-block text-danger">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('ward_id') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('school_name') ? ' error' : '' }}">
                                                <label for="school_name">Tên trường</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Tên trường" name="school_name"
                                                       value="{{ old('school_name') }}"
                                                >
                                                @if ($errors->has('school_name'))
                                                    <span class="help-block text-danger">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('school_name') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('school_email') ? ' error' : '' }}">
                                                <label for="school_email">{{ trans('admin.email') }}</label>
                                                <input type="text" class="form-control"
                                                       placeholder="{{ trans('admin.email') }}" name="school_email"
                                                       value="{{ old('school_email') }}"
                                                >
                                                @if ($errors->has('school_email'))
                                                    <span class="help-block text-danger">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('school_email') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('school_phone') ? ' error' : '' }}">
                                                <label for="school_phone">{{ trans('admin.phone') }}</label>
                                                <input type="text" class="form-control"
                                                       placeholder="{{ trans('admin.phone') }}" name="school_phone"
                                                       value="{{ old('school_phone') }}"
                                                >
                                                @if ($errors->has('school_phone'))
                                                    <span class="help-block text-danger">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('school_phone') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('school_address') ? ' error' : '' }}">
                                                <label for="school_address">{{ trans('admin.address') }}</label>
                                                <input type="text" class="form-control"
                                                       placeholder="{{ trans('admin.address') }}" name="school_address"
                                                       value="{{ old('school_address') }}"
                                                >
                                                @if ($errors->has('school_address'))
                                                    <span class="help-block text-danger">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('school_address') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('school_type') ? ' error' : '' }}">
                                                <label for="school_type">Loại trường</label>
                                                <select class="custom-select form-control required select2"
                                                        name="school_type">
                                                    @foreach($data['school_type'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('school_type')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('school_type'))
                                                    <span class="help-block text-danger">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('school_type') }}
                                                    </span>
                                                @endif
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
@endsection

