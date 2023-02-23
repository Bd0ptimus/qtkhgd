@extends('layouts.contentLayoutMaster')

@php
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
        ['name' => 'Danh sách lớp theo trường', 'link' => route('admin.school.view_class_list', ['id' => $school->id])],
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
                                            <div class="form-group">
                                                <label for="grade">Khối</label>
                                                <select class="custom-select form-control required select2"
                                                        name="grade">
                                                    @foreach($data['grades'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('grade', $class['grade'] ?? '')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('class_name') ? ' error' : '' }}">
                                                <label for="class_name">Lớp</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Lớp" name="class_name"
                                                       value="{{ old('class_name', $class['class_name'] ?? '') }}"
                                                >
                                                @if ($errors->has('class_name'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('class_name') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="school_branch_id">Điểm trường</label>
                                                <select class="custom-select form-control required select2"
                                                        name="school_branch_id">
                                                    @foreach($school->branches as $branch)
                                                        <option {{ strval($branch->id) === strval(old('school_branch_id',$class['school_branch_id'] ?? '')) ? 'selected' : '' }} value="{{$branch->id}}">{{ $branch->branch_name }}</option>
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