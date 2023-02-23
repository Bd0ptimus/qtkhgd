@extends('layouts.contentLayoutMaster')
@php
    $grades = GRADES;
    $schoolLevels = SCHOOL_TYPES;
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => $title_description ?? 'Lên bài giảng'],
    ];
@endphp
@section('title', $title_description ?? 'Lên bài giảng')
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

        .dataTables_filter {
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
                                    <select class="form-control parent select2 filter-level"
                                            style="width: 100%;"
                                            name="level">
                                        <option value="">Tất cả</option>
                                        @foreach ($schoolLevels as $key => $name)
                                            <option value="{{ $key }}" @if(isset($level) && $key == $level) selected @endif>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Khối học</label>
                                    <select class="form-control parent select2 filter-grade"
                                            style="width: 100%;"
                                            name="grade">
                                        <option value="">Tất cả</option>
                                        @foreach ($grades as $key => $grade)
                                            <option value="{{ $key }}"  @if(isset($keyGrade) && $key == $keyGrade) selected @endif>
                                                {{ $grade }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Môn học</label>
                                    <select class="form-control parent select2 filter-subject"
                                            style="width: 100%;"
                                            name="subject">
                                        <option value="">Tất cả</option>
                                        @foreach ($subjects as $key => $subject)
                                            <option value="{{ $subject->id }}" @if(isset($subjectId) && $subject->id == $subjectId) selected @endif>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group mb-0">
                                    <label for=""></label>
                                    <button type="button" class="btn btn-primary reset-filter">
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
                                        class="btn btn-flat btn-success"
                                        href="{{ route('lesson_sample.create') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm Bài giảng
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="exercise-question">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên bài giảng</th>
                                        <th scope="col">File bài giảng</th>
                                        <th scope="col">Video thiết bị số</th>
                                        <th scope="col">Trò chơi vận dụng</th>
                                        <th scope="col">Mô hình mô phỏng</th>
                                        <th scope="col" colspan="3" class="text-center">Tài liệu tham khảo</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-center">Phiếu bài tập về nhà</th>
                                        <th class="text-center">Đề kiểm tra</th>
                                        <th class="text-center">Bài tập nâng cao</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($lessonSamples as $key => $lessonSample)
                                        <tr>
                                            <td scope="row"><b>{{ $key + 1}}</b></td>
                                            <td>{{ $lessonSample->title}}</td>
                                            <td>
                                                <?php $fileName = ['pdf', 'docx'] ?>
                                                @foreach($lessonSample->attachments as $attachment)
                                                        @if (in_array(pathinfo($attachment->path, PATHINFO_EXTENSION), $fileName))
                                                            <a type="button" target="_blank" class="btn btn-flat btn-default btn-datatable" href="{{ $attachment->path }}">
                                                                <div title="Xem"><i class="fa fa-download" aria-hidden="true"></i>Tải file {{ pathinfo($attachment->path, PATHINFO_EXTENSION) }}</div>
                                                            </a>
                                                        @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($lessonSample->attachments as $attachment)
                                                    @if (!in_array(pathinfo($attachment->path, PATHINFO_EXTENSION), $fileName))
                                                        <button type="button" class="btn__showVideo btn btn-flat btn-default btn-datatable" >
                                                            <span title="Xem"><i class="fa fa-video-camera" aria-hidden="true"></i>Xem video</span>
                                                        </button>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <a type="button" target="_blank" href="{{ route('lesson_sample.game', ['game' => $attachment->id]) }}" class="btn__showGame btn btn-flat btn-default btn-datatable" >
                                                    <span title="Xem"><i class="fa fa-video-camera" aria-hidden="true"></i>Chi tiết</span>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_blank" type="button" href="{{ route('lesson_sample.simulation', ['simulation' => $attachment->id]) }}" class="btn__showShimulation btn btn-flat btn-default btn-datatable" >
                                                    <span title="Xem"><i class="fa fa-video-camera" aria-hidden="true"></i>Chi tiết</span>
                                                </a>
                                            </td>
                                            {{-- Tai lieu tham khao --}}
                                            <td>
                                                <button class="btn btn-flat btn-success btn-datatable btn__showModal"
                                                   data-url="{{ route('lesson_sample.homework.sheet', ['id' => $lessonSample->id]) }}"
                                                   data-toggle="modal" data-value="{{$lessonSample->id}}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem danh sách
                                                </button>
                                            </td>
                                            <td>
                                                <button class="btn btn-flat btn-success btn-datatable btn__showModalExercise"
                                                   data-url="{{ route('lesson_sample.exercise.question', ['id' => $lessonSample->id]) }}" data-toggle="modal">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem danh sách
                                                </button>
                                            </td>
                                            <td>
                                                <a style="margin-top: 3px" href="#" class="btn btn-flat btn-success btn-datatable btn__showModalExercise"
                                                   data-url="{{ route('lesson_sample.homework.sheet', ['id' => $lessonSample->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem danh sách
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-flat btn-success btn-datatable btn__uploadFile">
                                                    <i class="fa fa-upload" aria-hidden="true"></i>Thêm video
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @include('admin.lesson_sample.modal_homework_sheet')
                            @include('admin.lesson_sample.modal_exercise_question')
                            @include('admin.lesson_sample.show_video')
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

            $.fn.dataTable.ext.errMode = 'throw';
            $('#exercise-question').DataTable({
                pageLength: 25,
                search: false
            });

            var table = $('#homework-sheet').DataTable();
            $('#search').on( 'keyup', function () {
                table.search(this.value)
                    .draw();
            } );

            $('#exercise-question').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá bài giảng mẫu này?');
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

            $('.input-search').click(function () {
                window.location.href = updateParameter(window.location.href, 'search', $('#search').val())
            });
        });
    </script>
    <script>
        $('.btn__showModal').on('click', function (e) {
            e.preventDefault();
            let url = this.getAttribute('data-url');
            $.ajax({
                url: url,
                method: 'GET',
                success: function(res) {
                    let input = $(".list-homework-sheet"), options = '';
                    input.empty();
                    // debugger;
                    res.forEach(function (val) {
                        options += "" +
                            "<input type='checkbox' name='homework[]' value='"+ val.id +"' id='"+val.id+"_input'>" +
                            "<label for='"+val.id+"_input'>"+ val.name +" - "+ val.assemblage +"</label>" +
                            "<br>";
                    })
                    input.append(options)

                    // show modal
                }
            });

            $('#modalHomeworkSheet').modal('show')
        })

        $('.btn__showModalExercise').on('click', function (e) {
            e.preventDefault();
            let url = this.getAttribute('data-url');
            $.ajax({
                url: url,
                method: 'GET',
                success: function(res) {
                    let input = $(".list-exercise-question"), options = '';
                    input.empty();
                    // debugger;
                    res.forEach(function (val) {
                        options += "" +
                            "<input type='checkbox' name='exercise[]' value='"+ val.id +"' id='"+val.id+"_input'>" +
                            "<label for='"+val.id+"_input'>"+ val.assemblage +" - "+ val.title +"</label>" +
                            "<br>";
                    })
                    input.append(options)

                    // show modal
                }
            });

            $('#modalExerciseQuestion').modal('show')
        })

        $('.btn__showVideo').on('click', function (e) {
            e.preventDefault();
            let url = this.getAttribute('data-src');

            let video = $(".video-lesson"), options = '';
            video.empty();
            // debugger;
            options += "<source src='"+ url +"' type='video/mp4'>";

            video.append(options)

            $('#modalShowVideo').modal('show')
        })

        $('.btn__uploadFile').on('click', function () {
            $('#fileUpload').click()
        })
    </script>
@endsection
