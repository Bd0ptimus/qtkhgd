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
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-2  control-label">Tên tổ chuyên môn</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="name" name="name"
                                               value="{{ old('name',$regularGroup['name']??'')}}"
                                               class="form-control name" placeholder=""/>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-sm-2  control-label">Mô tả</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" name="description"
                                               value="{{ old('description',$regularGroup['description']??'') }}"
                                               class="form-control slug" placeholder=""/>
                                    </div>
                                    @if ($errors->has('description'))
                                        <span class="help-block text-danger">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('description') }}
                                            </span>
                                    @endif
                                </div>
                            </div>

                            {{-- select school-level --}}
                            <div class="form-group {{ $errors->has('school_level') ? ' has-error' : '' }}">
                                <label for="subject" class="col-sm-2  control-label">Các cấp học</label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm subject select2" data-placeholder="Cấp học"
                                            style="width: 100%;" name="school_level">
                                        <option value=""></option>
                                        @foreach (App\Models\School::SCHOOL_TYPES as $value => $name)
                                            <option value="{{ $value }}" {{ old('school_level', $regularGroup['school_level'] ?? '') == $value ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('school_level'))
                                        <span class="help-block text-danger">
                                                {{ $errors->first('school_level') }}
                                            </span>
                                    @endif
                                </div>
                            </div>

                            {{-- select subject --}}
                            <div class="form-group  {{ $errors->has('subjects') ? ' has-error' : '' }}">
                                @php
                                    $listSubject = [];
                                    $old_subject = old('subjects',(isset($regularGroup) ? $regularGroup->groupSubjects->pluck('subject_id')->toArray():''));
                                        if(is_array($old_subject)){
                                            foreach($old_subject as $value){
                                                $listSubject[] = (int)$value;
                                            }
                                        }
                                @endphp
                                <label for="subject" class="col-sm-2  control-label">Các môn học của tổ</label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm subject select2" multiple="multiple"
                                            data-placeholder="Môn học" style="width: 100%;" name="subjects[]">
                                        <option value=""></option>
                                        @foreach (App\Models\Subject::get() as $index => $subject)
                                            <option value="{{ $subject->id }}" {{ (count($listSubject) && in_array($subject->id, $listSubject))?'selected':'' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('subjects'))
                                        <span class="help-block text-danger">
                                            {{ $errors->first('subjects') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- //select subject --}}

                            {{-- select grades --}}
                            <div class="form-group  {{ $errors->has('grades') ? ' has-error' : '' }}">
                                @php
                                    $listGrades = [];
                                    $oldGrades = old('grades',(isset($regularGroup) ? $regularGroup->groupGrades->pluck('grade')->toArray():''));
                                        if(is_array($oldGrades)){
                                            foreach($oldGrades as $value){
                                                $listGrades[] = (int)$value;
                                            }
                                        }
                                @endphp
                                <label for="subject" class="col-sm-2  control-label">Các khối của tổ</label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm subject select2" multiple="multiple"
                                            data-placeholder="Khối học" style="width: 100%;" name="grades[]">
                                        <option value=""></option>
                                        @foreach ($grades as $index => $grade)
                                            <option value="{{ $index }}" {{ (count($listGrades) && in_array($index, $listGrades))?'selected':'' }}>{{ $grade}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('grades'))
                                        <span class="help-block text-danger">
                                            {{ $errors->first('grades') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- //select grades --}}
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        @csrf
                        <div class="col-md-2"></div>
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
