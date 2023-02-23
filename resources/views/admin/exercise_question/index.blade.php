@extends('layouts.contentLayoutMaster')
@php
    $grades = GRADES;
    $schoolLevels = SCHOOL_TYPES;
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => $title_description ?? 'Danh sách đề kiểm tra'],
    ];
@endphp
@section('title', $title_description ?? 'Danh sách đề kiểm tra')
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <style>
        .exercise-question-content {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dataTables_filter, #exercise-question_info, #exercise-question_paginate {
            display: none;
        }
    </style>
@endsection

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body card-dashboard form-group form-filter">
                        <div class='row align-items-center'>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Cấp học</label>
                                    <select class="form-control parent select2 filter-level" style="width: 100%;"
                                        name="level">
                                        @if(!Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_TO_TRUONG, ROLE_HIEU_TRUONG]))
                                            <option value="">Tất cả</option>
                                            @foreach ($schoolLevels as $key => $name)
                                                <option value="{{ $key }}"
                                                    @if (isset($level) && $key == $level) selected @endif>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach ($schoolLevels as $key => $name)
                                                @if($key == $level) <option value="{{ $key }}" selected> {{ $name }}</option> @endif
                                            @endforeach
                                        @endif
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Khối học</label>
                                    <select class="form-control parent select2 filter-grade" style="width: 100%;"
                                        name="grade">
                                        <option value="">Tất cả</option>
                                        @if(!Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_TO_TRUONG, ROLE_HIEU_TRUONG]))
                                            @foreach ($grades as $key => $grade)
                                                <option value="{{ $key }}"
                                                        @if ($key==$keyGradeChoose) selected @endif>
                                                        {{ $grade }}
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach ($grades as $key => $grade)
                                                @if(in_array($key , $keyGrade)) <option value="{{ $key }}" @if($key==$keyGradeChoose) selected @endif> {{ $grade }}</option> @endif                                               
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Môn học</label>
                                    <select class="form-control parent select2 filter-subject" style="width: 100%;"
                                        name="subject">
                                        <option value="">Tất cả</option>
                                        @if(!Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_TO_TRUONG, ROLE_HIEU_TRUONG]))
                                            @foreach ($subjects as $key => $subject)
                                                <option value="{{ $subject->id }}"
                                                    @if ($subject->id==$subjectIdChoose) selected @endif>
                                                    {{ $subject->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach ($subjects as $key => $subject)
                                                @if(in_array($subject->id,$subjectId)) <option value="{{ $subject->id }}" @if($subject->id==$subjectIdChoose) selected @endif> {{ $subject->name }}</option> @endif                                               
                                            @endforeach
                                        @endif



                                    </select>
                                </div>
                            </div>
                            @if(Admin::user()->isRole('administrator'))
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Bộ sách</label>
                                    <select class="form-control parent select2 filter-assemblage" style="width: 100%;"
                                        name="grade">
                                        <option value="">Tất cả</option>
                                        @foreach ($assemblages as $assemblage)
                                            <option value="{{ $assemblage }}" @if ($assemblage==$selectedAssemblage) selected @endif>
                                            {{ $assemblage  }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Cộng tác viên</label>
                                    <select class="form-control parent select2 filter-collaborator-category" style="width: 100%;" name="collaborator">
                                        <option value="">Tất cả</option>
                                        @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}" @if ($collaborator->id == $selectedCollaborator) selected @endif>
                                            {{ $collaborator->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-2">
                                <div class="form-group mb-0">
                                    <label for=""></label>
                                    <button type="button" class="main-action btn btn-primary reset-filter">
                                        <i class="feather icon-refresh-cw"></i> Bỏ lọc
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-4 justify-content-end d-flex">
                                <div class="d-flex">
                                    <input type="text" name="search"
                                           id="search"
                                           placeholder="{{ trans('admin.search') }}"
                                           value="{{ old('search',$search ?? '')}}"
                                           class="form-control title"/>
                                    <button type="submit" style="width: 50%;" class="btn btn-primary ml-1 input-search">{{ trans('admin.search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex">
                                <div class="btn-group text-nowrap">
                                    <a type="button" @if(!$permission) hidden @endif
                                        class="main-action btn btn-flat btn-success"
                                        href="{{ route('exercise_question.create') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm đề kiểm tra
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="exercise-question">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên đề kiểm tra</th>
                                        <th scope="col">Tên bộ sách</th>
                                        <th scope="col">Khối học</th>
                                        <th scope="col">Môn học</th>
                                        <!-- <th scope="col">Nội dung</th> -->
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($exerciseQuestions as $key => $exerciseQuestion)
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $exerciseQuestion->title}}</td>
                                            <td>{{ $exerciseQuestion->assemblage}}</td>
                                            <td>{{ mapGradeName($exerciseQuestion->grade) }}</td>
                                            <td>{{ $exerciseQuestion->subject->name }}</td>
                                            <!-- <td class="exercise-question-content"><p>{!! $exerciseQuestion->content !!}</p></td> -->
                                            <td style="width: 25%;">
                                                <a style="margin-top: 3px" href="#" class="btn btn-flat btn-success btn-datatable"
                                                    data-toggle="modal" data-target="#modalContent{{$exerciseQuestion->id}}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem nội dung
                                                </a> 
                                                <a type="button" class="main-action btn btn-flat btn-success btn-datatable" @if(!$permission) hidden @endif
                                                   href="{{ route('exercise_question.edit', ['id' => $exerciseQuestion->id]) }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                </a>
                                                <a type="button" class="main-action btn btn-flat btn-danger delete-item btn-datatable" @if(!$permission) hidden @endif
                                                   data-url="{{route('exercise_question.delete', ['id' => $exerciseQuestion->id])}}"
                                                   href="#">
                                                    <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá</span>
                                                </a>
                                                <a type="button" class="btn btn-flat btn-default download-item btn-datatable" target="_blank"
                                                   href="{{ route('exercise_question.download', ['id' => $exerciseQuestion->id]) }}">
                                                    <span title="Download"><i class="fa fa-download" aria-hidden="true"></i> Download</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {{ $exerciseQuestions->appends(request()->query())->links()}}
                                </div>
                                
                            </div>

                            @include('admin.exercise_question.modal_contents')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Scroll - horizontal and vertical table -->
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2({
                allowClear: true
            });
            var limit = 25
            var url = new URL(window.location.href);
            if (url.searchParams.get('limit')) limit = url.searchParams.get('limit')

            $.fn.dataTable.ext.errMode = 'throw';
            $('#exercise-question').DataTable({
                pageLength: limit,
                search: true
            });

            var table = $('#homework-sheet').DataTable();
            $('#search').on( 'keyup', function () {
                table.search(this.value)
                    .draw();
            } );
            $('#exercise-question').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá đề kiểm tra này?');
                if(confirmDelete) {
                    let element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            element.parents('tr').remove();
                            alert('Đã xoá đề kiểm tra');
                        }
                    });
                }
            });

            //update parameter when change filter
            function updateParameter(urlCurrent, key, value) {
                let url = new URL(urlCurrent);
                let search_params = url.searchParams;

                // new value of "key" is set to "value"
                search_params.set(key, value);
                // fetch data 
                search_params.set('page', '1');

                // change the search property of the main url
                url.search = search_params.toString();

                return url.toString();
            }

            $('.reset-filter').click(function () {
                window.location.href = window.location.href.substring(0, window.location.href.indexOf('?'))
            });

            $('.filter-level').change(function () {
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'level', optionSelected.val())
            });

            $('.filter-grade').change(function () {
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'grade', optionSelected.val())
            });

            $('.filter-subject').change(function () {
                window.location.href = updateParameter(window.location.href, 'subjectId', $(this).val())
            });

            $('.filter-assemblage').change(function() {
                window.location.href = updateParameter(window.location.href, 'selectedAssemblage', $(this).val())
            });

            $('.filter-collaborator-category').change(function() {
                window.location.href = updateParameter(window.location.href, 'selectedCollaborator', $(this).val())
            });

            $('.input-search').click(function () {
                window.location.href = updateParameter(window.location.href, 'search', $('#search').val())
            });

            $('.dataTables_length select').change(function () {
                window.location.href = updateParameter(window.location.href, 'limit', $(this).val())
            })
        });
    </script>
@endsection
