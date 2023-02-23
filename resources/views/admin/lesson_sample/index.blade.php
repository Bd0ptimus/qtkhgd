@extends('layouts.contentLayoutMaster')
@php
$grades = GRADES;
$schoolLevels = SCHOOL_TYPES;
$breadcrumbs = [['name' => trans('admin.home'), 'link' => route('admin.home')], ['name' => $title_description ?? 'Bài giảng mẫu']];
@endphp
@section('title', $title_description ?? 'Bài giảng mẫu')
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/global/css/css.css') }}">
    <style>
        .exercise-question-content {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dataTables_filter,
        #exercise-question_info,
        #exercise-question_paginate {
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
                                
                                <div class="col-sm-2 small-dropdown">
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
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Khối học</label>
                                        <select class="form-control parent select2 filter-grade" style="width: 100%;"
                                            name="grade">
                                            <option value="">Tất cả</option>
                                            @if(!Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_TO_TRUONG, ROLE_HIEU_TRUONG]))
                                                @foreach ($grades as $key => $grade)
                                                    <option value="{{ $key }}"
                                                            @if ( $key==$keyGradeChoose) selected @endif>
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
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Môn học</label>
                                        <select class="form-control parent select2 filter-subject" style="width: 100%;"
                                            name="subject">
                                            <option value="">Tất cả</option>
                                            @if(!Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_TO_TRUONG, ROLE_HIEU_TRUONG]))
                                                @foreach ($subjects as $key => $subject)
                                                    @if(empty($subjectId))
                                                        <option value="{{ $subject->id }}" @if ($subject->id==$subjectIdChoose) selected @endif>
                                                        {{ $subject->name }} </option>
                                                    @else
                                                        @if(in_array($subject->id,$subjectId))
                                                            <option value="{{ $subject->id }}" @if ($subject->id==$subjectIdChoose) selected @endif>
                                                                {{ $subject->name }} </option>
                                                        @endif
                                                    @endif
                                                    
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
                                <div class="col-sm-2 small-dropdown">
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
                                @endif
                                @if (Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]))
                                <div class="col-sm-2 small-dropdown">
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
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">File powerpoint</label>
                                        <select class="form-control parent select2 filter-powerpoint" style="width: 100%;" name="filter_powerpoint">
                                            <option value="">Tất cả</option>
                                            <option value="yes" @if ("yes" == $filterPowerpoint) selected @endif>Có file powerpoint</option>
                                            <option value="no" @if ("no" == $filterPowerpoint) selected @endif>Không có file powerpoint</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Thiết bị số</label>
                                        <select class="form-control parent select2 filter-digital-device" style="width: 100%;" name="filter_digital_device">
                                            <option value="">Tất cả</option>
                                            <option value="yes" @if ("yes" == $filterDigitalDevice) selected @endif>Có file thiết bị số</option>
                                            <option value="no" @if ("no" == $filterDigitalDevice) selected @endif>Không có file thiêt bị số</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Bài tập về nhà</label>
                                        <select class="form-control parent select2 filter-homesheet" style="width: 100%;" name="filter_homesheet">
                                            <option value="">Tất cả</option>
                                            <option value="yes" @if ("yes" == $filterHomesheet) selected @endif>Có phiếu bài tập</option>
                                            <option value="no" @if ("no" == $filterHomesheet) selected @endif>Không có phiếu bài tập</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Đề kiểm tra</label>
                                        <select class="form-control parent select2 filter-exercise" style="width: 100%;" name="filter_exercise">
                                            <option value="">Tất cả</option>
                                            <option value="yes" @if ("yes" == $filterExercise) selected @endif>Có đề kiểm tra</option>
                                            <option value="no" @if ("no" == $filterExercise) selected @endif>Không có đề kiểm tra</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Link mô phỏng</label>
                                        <select class="form-control parent select2 filter-diagram-simulator" style="width: 100%;" name="filter_diagram_simulator">
                                            <option value="">Tất cả</option>
                                            <option value="yes" @if ("yes" == $filterDiagramSimulator) selected @endif>Có link mô phỏng</option>
                                            <option value="no" @if ("no" == $filterDiagramSimulator) selected @endif>Không có link mô phỏng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Link trò chơi vận dụng</label>
                                        <select class="form-control parent select2 filter-game" style="width: 100%;" name="filter_game">
                                            <option value="">Tất cả</option>
                                            <option value="yes" @if ("yes" == $filterGame) selected @endif>Có link trò chơi</option>
                                            <option value="no" @if ("no" == $filterGame) selected @endif>Không có link trò chơi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2 small-dropdown">
                                    <div class="form-group mb-0">
                                        <label for=""></label>
                                        <button type="button" class="main-action btn btn-primary reset-filter">
                                            <i class="feather icon-refresh-cw"></i> Bỏ lọc
                                        </button>
                                    </div>
                                </div>
                            
                            <div class="col-sm-5 justify-content-end d-flex">
                                <div class="d-flex">
                                    <input type="text" name="search" id="search"
                                        placeholder="{{ trans('admin.search') }}" value="{{ old('search', $search ?? '') }}"
                                        class="form-control title" />
                                    <button type="button" style="width: 50%;"
                                        class="main-action btn btn-primary ml-1 input-search">{{ trans('admin.search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex">
                                <div class="btn-group text-nowrap">
                                    <a type="button" @if (!$permission) hidden @endif
                                        class="main-action btn btn-flat btn-success"
                                        href="{{ route('lesson_sample.create') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm Bài giảng mẫu
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="exercise-question">
                                    <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">Bộ sách</th>
                                            <th scope="col">Tên bài giảng</th>
                                            <th scope="col">Khối học</th>
                                            <th scope="col">Môn học</th>
                                            <th scope="col">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($lessonSamples as $key => $lessonSample)
                                            <tr>
                                                <th scope="row">{{ $key + 1 }}</th>
                                                <td>{{ $lessonSample->getAssemblage() }}</td>
                                                <td>{{ $lessonSample->title }}</td>
                                                <td>{{ mapGradeName($lessonSample->grade) }}</td>
                                                <td>{{ $lessonSample->subject->name }}</td>
                                                <!-- <td class="exercise-question-content"><p>{!! $lessonSample->content !!}</p></td> -->
                                                <td>
                                                    <a style="margin-top: 3px" href="#"
                                                        class="btn-view-lesson btn btn-flat btn-success btn-datatable"
                                                        data-url="{{ route('ajax_get_lesson_by_id', ['id' => $lessonSample->id,'view'=>1]) }}" id="{{$lessonSample->id}}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                    </a>
                                                    <a type="button"
                                                        class="main-action btn btn-flat btn-success btn-datatable"
                                                        @if (!$permission) hidden @endif
                                                        href="{{ route('lesson_sample.edit', ['id' => $lessonSample->id]) }}">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                    </a>
                                                    <a type="button"
                                                        class="main-action btn btn-flat btn-danger delete-item btn-datatable"
                                                        @if (!$permission) hidden @endif
                                                        data-url="{{ route('lesson_sample.delete', ['id' => $lessonSample->id]) }}"
                                                        href="#">
                                                        <span title="Xoá"><i class="fa fa-trash"
                                                                aria-hidden="true"></i>Xoá</span>
                                                    </a>
                                                    <!-- <a type="button" class="main-action btn btn-flat btn-success btn-datatable"
                                                        @if (!$permission) hidden @endif
                                                        href="{{ route('lesson_sample.lesson_content.index', ['id' => $lessonSample->id]) }}" id="{{$lessonSample->id}}">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Bổ sung nội dung
                                                    </a> -->
                                                    <a type="button"
                                                        class="btn btn-flat btn-default download-item btn-datatable"
                                                        target="_blank"
                                                        href="{{ route('lesson_sample.download', ['id' => $lessonSample->id]) }}">
                                                        <span title="Download"><i class="fa fa-download"
                                                                aria-hidden="true"></i>Tải bài giảng</span>
                                                    </a>
                                                    @if (count($lessonSample->attachments) > 0)
                                                        <a type="button" data-id = {{$lessonSample->id}} data-bs-toggle="modal"
                                                            class="btn btn-flat btn-default btn-datatable btn-file"
                                                            >
                                                            <span title="Xem"><i class="fa fa-download"
                                                                    aria-hidden="true"></i>Tải file đính kèm</span>
                                                                    @foreach ($lessonSample->attachments as $attachFile)
                                                                        <input type="hidden" class="attach-file-{{$lessonSample->id}}" name="{{$attachFile->name}}" value="{{ route('lesson_sample.download_attach_file', ['attachmentId' => $attachFile->id]) }}">
                                                                    @endforeach
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end">
                                    {{$lessonSamples->appends(request()->query())->links()}}
                            </div>

                            {{-- @include('admin.lesson_sample.modal_contents') --}}
                            <div id="lessonSampleContent"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Button trigger modal -->
        <div class="modal fade" id="show-file-attach" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Danh sách file đính kèm</h5>
                </div>
                <div class="modal-body" >
                    <ul id="list-attach-files">
                        
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="return $('#show-file-attach').modal('hide')" data-bs-dismiss="modal">Close</button>
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
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({
                allowClear: true
            });
            var limit = 25;

            var url = new URL(window.location.href);
            if (url.searchParams.get('limit'))
                limit = url.searchParams.get('limit')

            $.fn.dataTable.ext.errMode = 'throw';
            $('#exercise-question').DataTable({
                pageLength: limit,
                search: false
            });

            var table = $('#homework-sheet').DataTable();
            $('#search').on('keyup', function() {
                table.search(this.value)
                    .draw();
            });

            $('#exercise-question').on('click', '.btn-file', function(e) {
                e.preventDefault();
                $('#list-attach-files').html("");
                $("#show-file-attach").modal("show");
                const idLesson = $(this)[0].getAttribute("data-id");
                console.log($(".attach-file-"+idLesson));
                $($(".attach-file-"+idLesson)).each(function(index, input) {
                    $('#list-attach-files').append(
                        $('<li>').append(
                            $('<a>').attr('href',input.getAttribute("value")).attr('target',"_blank").append(
                                $('<span>').attr('class', 'tab').append(input.getAttribute("name"))
                    )));
                });
            });
            $('#exercise-question').on('click', '.delete-item', function(e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá bài giảng mẫu này?');
                if (confirmDelete) {
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

            $('#exercise-question').on('click', '.btn-view-lesson', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).data('url'),
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('#lessonSampleContent').empty().append(res.view);
                        $('#modalLessonSampleContent').modal('show');
                        // tinyMCE.activeEditor.setContent(data.content);
                    },
                    error: function (res) {
                        console.log(JSON.stringify(res));
                    }
                    
                });
            });

            //update parameter when change filter
            function updateParameter(urlCurrent, key, value) {
                let url = new URL(urlCurrent);
                let search_params = url.searchParams;

                // new value of "key" is set to "value"
                search_params.set(key, value);
                search_params.set('page', 1);

                // change the search property of the main url
                url.search = search_params.toString();

                return url.toString();
            }

            $('.reset-filter').click(function() {
                window.location.href = window.location.href.substring(0, window.location.href.indexOf('?'))
            });

            $('.filter-level').change(function() {
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'level', optionSelected.val())
            });

            $('.filter-grade').change(function() {
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'grade', optionSelected.val())
            });

            $('.filter-subject').change(function() {
                window.location.href = updateParameter(window.location.href, 'subjectId', $(this).val())
            });

            $('.filter-collaborator-category').change(function() {
                window.location.href = updateParameter(window.location.href, 'selectedCollaborator', $(this).val())
            });

            $('.filter-assemblage').change(function() {
                window.location.href = updateParameter(window.location.href, 'selectedAssemblage', $(this).val())
            });

            $('.filter-powerpoint').change(function() {
                window.location.href = updateParameter(window.location.href, 'filter_powerpoint', $(this).val())
            });

            $('.filter-digital-device').change(function() {
                window.location.href = updateParameter(window.location.href, 'filter_digital_device', $(this).val())
            });

            $('.filter-homesheet').change(function() {
                window.location.href = updateParameter(window.location.href, 'filter_homesheet', $(this).val())
            });

            $('.filter-exercise').change(function() {
                window.location.href = updateParameter(window.location.href, 'filter_exercise', $(this).val())
            });

            $('.filter-diagram-simulator').change(function() {
                window.location.href = updateParameter(window.location.href, 'filter_diagram_simulator', $(this).val())
            });

            $('.filter-game').change(function() {
                window.location.href = updateParameter(window.location.href, 'filter_game', $(this).val())
            });

            $('.input-search').click(function() {
                
                window.location.href = updateParameter(window.location.href, 'search', $('#search').val());
            });
            
            $('.dataTables_length select').change(function() {
                window.location.href = updateParameter(window.location.href, 'limit', $(this).val())
            })
        });

        
    </script>
@endsection
