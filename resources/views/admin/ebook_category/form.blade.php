@extends('layouts.contentLayoutMaster')

@php
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => "Danh sách Ebook", 'link' => route('ebook-categories.index')],
        ['name' => $title_description ?? ''],
    ];
    $schoolLevels = SCHOOL_TYPES;
@endphp

@section('title', $title_description ?? '')

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/plugins/forms/validation/form-validation.css')}}">
@endpush

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="fields-group">

                            <div class="row">
                                <!-- Tên loại sách -->
                                <div class="col-sm-6 {{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Tên loại sách<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" name="name"
                                               value="{{ old('name',$ebook['name']??'')}}"
                                               class="form-control name" placeholder="Tên loại sách"/>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <br>
                    <div class="box-footer">
                        @csrf
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="btn-group pull-left">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
@endpush
