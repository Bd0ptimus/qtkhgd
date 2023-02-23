@extends('layouts.contentLayoutMaster')

@php
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
        ['name' => 'Danh sách điểm trường theo trường', 'link' => route('admin.school.view_branch_list', ['id' => $school->id])],
    ];
@endphp

@section('title', $title)

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/plugins/forms/validation/form-validation.css')}}">
@endpush

@section('main')
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <form method="POST"
                                  action="{{ $routing }}"
                                  class="form">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('branch_name') ? ' error' : '' }}">
                                                <label for="branch_name">Tên điểm trường</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Tên điểm trường" name="branch_name"
                                                       value="{{ old('branch_name', $branch['branch_name'] ?? '') }}"
                                                >
                                                @if ($errors->has('branch_name'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('branch_name') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('branch_email') ? ' error' : '' }}">
                                                <label for="branch_email">{{ trans('admin.email') }}</label>
                                                <input type="email" class="form-control"
                                                       placeholder="{{ trans('admin.email') }}" name="branch_email"
                                                       value="{{ old('branch_email', $branch['branch_email'] ?? '') }}"
                                                />
                                                @if ($errors->has('branch_email'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('branch_email') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('branch_address') ? ' error' : '' }}">
                                                <label for="branch_address">{{ trans('admin.address') }}</label>
                                                <input type="text" class="form-control"
                                                       placeholder="{{ trans('admin.address') }}" name="branch_address"
                                                       value="{{ old('branch_address', $branch['branch_address'] ?? '') }}"
                                                >
                                                @if ($errors->has('branch_address'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('branch_address') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('branch_phone') ? ' error' : '' }}">
                                                <label for="branch_phone">{{ trans('admin.phone') }}</label>
                                                <input type="text" class="form-control"
                                                       placeholder="{{ trans('admin.phone') }}" name="branch_phone"
                                                       value="{{ old('branch_phone', $branch['branch_phone'] ?? '') }}"
                                                >
                                                @if ($errors->has('branch_phone'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('branch_phone') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                <input type="hidden" value="0"
                                                                       name="is_main_branch">
                                                                <input type="checkbox" name="is_main_branch"
                                                                       {{ 1 === old('is_main_branch', $branch['is_main_branch'] ?? 0) ? 'checked' : '' }}
                                                                       value="1">
                                                                <span class="vs-checkbox">
                                                                  <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                  </span>
                                                                </span>
                                                                <span class="">Nhánh chính</span>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                </ul>
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
    <!--/ Scroll - horizontal and vertical table -->
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endpush