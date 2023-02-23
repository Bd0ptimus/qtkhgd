@extends('layouts.contentLayoutMaster')

@php
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => "Danh sách Bài mô phỏng", 'link' => route('simulator.index')],
        ['name' => $title_description ?? ''],
    ];
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
                                
                                <!-- Tên bài giảng -->
                                <div class="col-sm-6 {{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title" class="control-label">Tên bài mô phỏng<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" name="title"
                                               value="{{ old('title',$simulator['name_simulator']??'')}}"
                                               class="form-control title" placeholder="Tên bài mô phỏng"/>
                                        
                                    </div>
                                    @if ($errors->has('title'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('title') }}
                                        </span>
                                    @endif
                                </div>

                                 <!-- Bài học liên quan -->
                                 <div class="col-sm-6">
                                    <label for="lesson" class="control-label">Bài học liên quan<sup class="text-danger"></sup></label>
                                    <div class="input-group">
                                        <input type="text" name="lesson"
                                               value="{{ old('title',$simulator['related_lesson']??'')}}"
                                               class="form-control title" placeholder="Bài học liên quan"/>
                                    </div>
                                </div>
                            </div>
                         
                            <div class="row">
                                <!-- Khối học -->
                                <div class="col-sm-4 {{ $errors->has('grades') ? ' has-error' : '' }}">
                                    <label for="grades" class="control-label">Khối học <sup class="text-danger">*</sup></label>
                                    <select class="form-control input-sm grade select2" id="group-select" onchange=""
                                            data-placeholder="Khối học" style="width: 100%;" name="grades[]" multiple data-mdb-filter="true">
                                        <option value=""></option>
                                        @foreach ($grades as $index => $grade)
                                            <option value="{{ $index }}" class="value-school-type"
                                                    {{ (isset($simulator) && in_array($index, $simulator->simulatorGrades->pluck('grade')->toArray()))?'selected':'' }}>
                                                {{ $grade }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('grades'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('grades') }}
                                        </span>
                                    @endif
                                </div>

                                 <!-- Môn học -->
                                 <div class="col-sm-4 {{ $errors->has('subject') ? ' has-error' : '' }}">
                                    <label for="subject" class="control-label">Môn học <sup class="text-danger">*</sup></label>
                                    <select class="form-control input-sm subject select2"
                                            data-placeholder="Môn học" style="width: 100%;" name="subject" id="subject-select">
                                        <option value=""></option>
                                        @foreach ($subjects as $index => $subject)
                                            <option value="{{ $subject->id }}" {{ (isset($simulator) && $simulator->subject_id == $subject->id)?'selected':'' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('subject'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('subject') }}
                                        </span>
                                    @endif
                                </div>


                                <div class="col-sm-4 {{ $errors->has('url') ? ' has-error' : '' }}">
                                    <label for="url" class="control-label">Đường dẫn đến simulator<sup class="text-danger"></sup></label>
                                    <div class="input-group">
                                        <input type="text" name="url"
                                               value="{{ old('url',$simulator['url_simulator']??'')}}"
                                               class="form-control title" simulator="Link simulator" placeholder="{{asset('')}}..."/>
                                    </div>
                                    @if ($errors->has('url'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('url') }}
                                        </span>
                                    @endif
                                    
                                </div>
                            </div>
                         
                            <div class="row {{ $errors->has('guide') ? ' has-error' : '' }}">
                               
                                <div class="col-sm-12">
                                    <label for="name" class="control-label">Hướng dẫn<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <textarea class="form-control content-exercise-question description" name="guide" rows="20">{{ old('guide',$simulator['user_guide'] ?? '')}}</textarea>
                                    </div>
                                    @if ($errors->has('guide'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('guide') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <br/>
                    <br/>
                    <div class="box-footer">
                        @csrf
                        <div class="col-md-12 d-flex justify-content-center">
                            <div class="btn-group">
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            $('#group-select').on('change', function(){
                var selected = $('#group-select').val();
                $.ajax({
                    type: 'POST',
                    header: "Content-type: text/plain",
                    data: {
                        data : selected,
                        _token: '{{ csrf_token() }}',
                    },
                    url: "{{ route('simulator.group-change') }}",
                    dataType: 'json',
                    success: function(res){
                        if(res['error'] == 0){
                            $('#subject-select').find('option').remove().end();
                            res['subjects'].forEach(function(e){
                                $('#subject-select').append(`<option value="${e['id']}"> ${e['name']} </option>`)
                            });
                        }
                    },
                    error: function(res){
                        console.log(res)
                    }
                });              
            })
        });

       
    </script>
@endpush
