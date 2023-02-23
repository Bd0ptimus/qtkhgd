@extends('layouts.contentLayoutMaster')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ $title_description??'' }}</h2>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">

                    <div class="box-body">
                        <div class="fields-group">

                            <div class="form-group   {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-2  control-label">Tên môn học</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="name" name="name"
                                               value="{{ old('name',$subject['name']??'')}}" class="form-control name"
                                               placeholder=""/>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group   {{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-sm-2  control-label">Mô tả</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="" name="description"
                                               value="{{ old('description',$subject['description']??'') }}"
                                               class="form-control slug" placeholder=""/>
                                    </div>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('description') }}
                                            </span>
                                    @endif
                                </div>
                            </div>

                            {{-- select grade --}}
                            <div class="form-group  {{ $errors->has('grades') ? ' has-error' : '' }}">
                                @php
                                    $listGrade = [];
                                    $old_grade = old('grades',(isset($subject) ? $subject->grades->pluck('grade')->toArray():''));
                                        if(is_array($old_grade)){
                                            foreach($old_grade as $value){
                                                $listGrade[] = (int)$value;
                                            }
                                        }
                                @endphp
                                <label for="grade" class="col-sm-2  control-label">Khối</label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm grade select2" multiple="multiple"
                                            data-placeholder="Khối" style="width: 100%;" name="grades[]">
                                        <option value=""></option>
                                        @foreach (App\Models\SchoolClass::GRADES as $k => $v)
                                            <option value="{{ $k }}" {{ (count($listGrade) && in_array($k, $listGrade))?'selected':'' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('grades'))
                                        <span class="help-block text-danger">
                                             {{ $errors->first('grades') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        @csrf
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-8">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                            </div>

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-footer -->
                </form>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">

    {{-- switch --}}
    <link rel="stylesheet" href="{{ asset('admin/plugin/bootstrap-switch.min.css')}}">
@endpush

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    {{-- switch --}}
    <script src="{{ asset('admin/plugin/bootstrap-switch.min.js')}}"></script>

    <script type="text/javascript">
        $("[name='top'],[name='status']").bootstrapSwitch();
    </script>

    <script type="text/javascript">

        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endpush
