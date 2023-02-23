@extends('layouts.contentLayoutMaster')

@php
$breadcrumbs = [['name' => trans('admin.home'), 'link' => route('admin.home')], 
                ['name' => 'Danh sách Bài mô phỏng', 'link' => route('simulator.index')], 
                ['name' => $title_description ?? '']];
$grades = GRADES;
@endphp

@section('title', $title_description ?? '')

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/forms/validation/form-validation.css') }}">
@endpush

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <!-- form start -->
                <form>
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="row">
                                <div class="col-md-4 border-right-dark">
                                    <h4>Thông tin bài giảng</h4>
                                    <div class="info-lesson-teacher">
                                        <ul>
                                            <li style="line-height: 30px;"><b>Tên bài mô phỏng: </b>{{ $simulator->name_simulator }}
                                            </li>
                                            <li style="line-height: 30px;"><b>Tên bài học liên quan:
                                                </b>{{ $simulator->related_lesson??'' }}</li>
                                            <li style="line-height: 30px;"><b>Môn học:
                                                </b>{{ $simulator->subject->name }}
                                            </li>
                                            <li style="line-height: 30px;">
                                                <b>Khối học: </b><br>
                                                @foreach ($simulator->simulatorGrades as $simulatorGrade)
                                                    <p>{{$grades[$simulatorGrade->grade]}}</p>
                                                @endforeach
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label for="name" class="control-label">Hướng dẫn</label>
                                    <div class="input-group border border-dark rounded p-1">
                                        {!! $simulator['user_guide'] ?? '' !!}
                                    </div>                                 
                                </div>
                            </div> 
                            <div class="row my-3">
                                <div class="embed-responsive embed-responsive-21by9 border border-dark">
                                    <iframe class="embed-responsive-item" @if($simulator['url_simulator']) src={{asset($simulator['url_simulator'])}} @endif allowfullscreen>
                    
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </form>
                
            </div>
        </div>
    </div>
    
@endsection