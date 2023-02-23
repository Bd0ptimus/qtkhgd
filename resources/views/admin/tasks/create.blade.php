@extends('layouts.contentLayoutMaster')

@php
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => "Danh sách nhiệm vụ", 'link' => route('tasks.index')],
        ['name' => 'Tạo mới nhiệm vụ'],
    ];
    $title = "Tạo mới nhiệm vụ";
    $taskPriority = PRIORITY;
    $priorityValue = PRIORITY_VALUE;
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
        <form action="{{route('tasks.store')}}" method="POST" role="form" class="form-create">
            @csrf
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label for="title">Tiêu đề<sup class="text-danger">*</sup></label>
                        <input type="text" name="title" class="form-control" id="title">
                        @if ($errors->has('title'))
                            <span class="help-block text-danger">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('title') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="desc">Sự miêu tả<sup class="text-danger">*</sup></label>
                        <textarea id="task-create-description" name="description" class="form-control description" rows="20">{!! old('description') !!}</textarea>
                        @if ($errors->has('description'))
                            <span class="help-block text-danger">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('description') }}
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        @livewire('components.task.check-list-create')
                    </div>

                    <div class="form-group">
                        @livewire('components.task.attachment')
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="assignee" class="d-block">Người được chỉ định</label>
                        <select id="assignee_id" name="assignee_ids[]" multiple="multiple"
                                class="form-control parent select2">
                            @foreach ($users as $key => $item)
                                <option value="{{ $item['id'] }}">{!! $item['name'] !!}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('assignee_ids'))
                            <span class="help-block text-danger">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('assignee_ids') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="assignee" class="d-block">Người giám sát</label>
                        <select id="follower_id" name="follower_ids[]" multiple="multiple"
                                class="form-control parent select2">
                            @foreach ($users as $key => $item)
                                <option value="{{ $item['id'] }}">{!! $item['name'] !!}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority" class="d-block">Sự ưu tiên<sup class="text-danger">*</sup></label>
                        <select id="priority" name="priority" class="form-control parent select2">
                            @foreach ($taskPriority as $key => $item)
                                <option value="{{ $item }}">{!! $key !!}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('priority'))
                            <span class="help-block text-danger">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('priority') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="start-date" class="d-block">Ngày bắt đầu<sup class="text-danger">*</sup></label>
                        <input type="text" name="start_date" class="form-control" id="start-date">
                        @if ($errors->has('start_date'))
                            <span class="help-block text-danger">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('start_date') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="due-date" class="d-block">Hạn hoàn thành</label>
                        <input type="text" name="due_date" class="form-control" id="due-date">
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <a href="{{route('tasks.index')}}" class="btn btn-danger btn-md">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary btn-md">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu
                    </button>
                </div>
            </div>
        </form>
        <div class='task-error' style='display:none'></div>
        <div class='task-success' style='display:none'></div>
    </section>
    <!--/ Scroll - horizontal and vertical table -->
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();

            window.addEventListener('showFlashMessage', event => {
                if(event.detail.status) {
                    $('.task-success').text(event.detail.message);
                    $('.task-success').stop().fadeIn(400).delay(3000).fadeOut(400); //fade out after 3 seconds
                } else {
                    $('.task-error').text(event.detail.message);
                    $('.task-error').stop().fadeIn(400).delay(3000).fadeOut(400); //fade out after 3 seconds
                }
            })
        });
    </script>
@endpush
@section('page-script')
    {{-- Page js files --}}
    <script>
        Dropzone.options.myDropzone = {
            url: "{{route('files.import')}}",
            autoProcessQueue: false,
            paramName: "file",
            clickable: true,
            maxFilesize: 5, //in mb
            addRemoveLinks: true,
            dictDefaultMessage: "Tải file tại đây",
            init: function () {
                this.on("sending", function (file, xhr, formData) {
                    console.log("sending file");
                });
                this.on("success", function (file, responseText) {
                    console.log('great success');
                });
                this.on("addedfile", function (file) {
                    console.log('file added');
                });
            }
        };
    </script>
@endsection