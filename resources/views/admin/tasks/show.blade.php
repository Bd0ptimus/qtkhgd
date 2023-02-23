@extends('layouts/contentLayoutMaster')

@php
    $title = 'Danh sách nhiệm vụ';
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => $title, 'link' => route('tasks.index')],
        ['name' => $task->title],
    ];
    $lagEdit = $task->creator_id == $user_id ? true : false;
    $checklistIdStr = collect($task->checklists)->implode('id', ',');
    $checklistIdArr = explode(',', $checklistIdStr);
    $taskPriority = PRIORITY;
@endphp

@section('title', $title)
@section('vendor-style')
    {{-- Vendor Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/ag-grid/ag-theme-material.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection
@section('main')
    {{-- Statistics card section start --}}
    <section id="horizontal-vertical" class="task-show">
        <div class="card">
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="row">
                        <div class="col-md-12">
                            @if (session()->has('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">

                            @livewire('components.task.title', ['task' => $task])

                            @livewire('components.task.description', ['task' => $task])

                            @livewire('components.task.check-list', ['task' => $task])

                            @livewire('components.task.attachments', ['task' => $task])
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                @livewire('components.task.assignee', ['task' => $task, 'users' => $users])
                            </div>

                            <div class="form-group">
                                @livewire('components.task.follower', ['task' => $task, 'users' => $users])
                            </div>

                            <div class="form-group">
                                <label for="assignee" class="d-block">Người tạo: <a href="#">{{ $task->creator->name }}</a></label>
                            </div>

                            <div class="form-group">
                                @livewire('components.task.status', ['task' => $task, 'status' => $status])
                            </div>

                            <div class="form-group">
                                @livewire('components.task.priority', ['task' => $task, 'priority' => $taskPriority])
                            </div>
                        
                            <div class="form-group">
                                @livewire('components.task.start-date', ['task' => $task])
                            </div>

                            <div class="form-group">
                                @livewire('components.task.due-date', ['task' => $task])
                            </div>
                        </div>
                    </div>
                    @livewire('components.task.comment', ['taskId' => $task->id])
                </div>
            </div>
        </div>
        <div class='task-error' style='display:none'></div>
        <div class='task-success' style='display:none'></div>
    </section>
    {{-- // Statistics Card section end--}}
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/ag-grid/ag-grid-community.min.noStyle.js')) }}"></script>
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        //config select2
        $(document).ready(function () {
            $('.select2').select2()

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
    <script src="{{asset('admin/plugin/ckeditor/ckeditor.js')}}"></script>
@endsection
@section('page-script')
    <script type="text/javascript">
        window.livewire.on('alert_remove',() => {
            setTimeout(function(){ $(".alert").fadeOut('slow');
            }, 5000); // 5 secs
        });
    </script>
@endsection

