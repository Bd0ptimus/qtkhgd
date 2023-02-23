@extends('layouts.contentLayoutMaster')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ $title }}</h2>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="{{ $routing }}" method="post" accept-charset="UTF-8" class="form-horizontal">
                      @csrf
                      @method($method)
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="form-group {{ $errors->has('grade') ? ' has-error' : '' }}">
                                <label for="grade" class="col-sm-2  control-label">Khối</label>
                                <div class="col-sm-8">
                                    <select required class="form-control input-sm" data-placeholder="Khối" name="grade">
                                        <option value=""></option>
                                        @foreach ($grades as $index => $grade)
                                            <option value="{{ $index }}" {{ isset($weeklyLesson) &&  $weeklyLesson->grade === $index ? 'selected' : '' }}>{{ $grade }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('grade'))
                                        <span class="help-block">
                                            {{ $errors->first('grade') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('subject') ? ' has-error' : '' }}">
                                <label for="subject" class="col-sm-2  control-label">Môn học</label>
                                <div class="col-sm-8">
                                    <select required class="form-control input-sm" data-subjects="{{ json_encode($subjects) }}" data-subject-id="{{ isset($weeklyLesson) ? $weeklyLesson->subject_id : '' }}" data-placeholder="Môn học" name="subject">
                                        <option value=""></option>
                                    </select>
                                    @if ($errors->has('subject'))
                                        <span class="help-block">
                                            {{ $errors->first('subject') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('lesson') ? ' has-error' : '' }}">
                                <label for="lesson" class="col-sm-2  control-label">Chọn bài giảng</label>
                                <div class="col-sm-8">
                                    <select required class="form-control input-sm select2" data-placeholder="Chọn bài giảng" name="lesson" data-lesson-id="{{ isset($weeklyLesson) ? $weeklyLesson->teacherLesson->id : '' }}">
                                        <option value=""></option>
                                    </select>
                                    @if ($errors->has('lesson'))
                                        <span class="help-block">
                                            {{ $errors->first('lesson') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-2  control-label">Tên bài giảng</label>
                                <div class="col-sm-8">
                                <input readonly id="lesson_name" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-2  control-label">Chủ đề</label>
                                <div class="col-sm-8">
                                <input readonly id="lesson_topic" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-2  control-label">Ngày bắt đầu theo kế hoạch</label>
                                <div class="col-sm-8">
                                <input readonly id="start_date_by_plan" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="col-sm-2  control-label">Ngày kết thúc theo kế hoạch</label>
                                <div class="col-sm-8">
                                <input readonly id="end_date_by_plan" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('start_date') ? ' has-error' : '' }}">
                                <label for="start_date" class="col-sm-2  control-label">Ngày thực tế sẽ giảng dạy</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input autocomplete="off" type="text" name="start_date" class="form-control date" value="{{ isset($weeklyLesson) ? $weeklyLesson->start_date : '' }}"/>
                                    </div>
                                    @if ($errors->has('start_date'))
                                        <span class="help-block">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('start_date') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('end_date') ? ' has-error' : '' }}">
                                <label for="end_date" class="col-sm-2  control-label">Ngày thực tế sẽ kết thúc giảng dạy</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input autocomplete="off" type="text" name="end_date" class="form-control date" value="{{ isset($weeklyLesson) ? $weeklyLesson->end_date : '' }}" />
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('end_date') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
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
    <script src="{{ asset('admin/AdminLTE/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.vi.min')}}"></script>
    {{-- switch --}}
    <script src="{{ asset('admin/plugin/bootstrap-switch.min.js')}}"></script>

    <script type="text/javascript">
        $("[name='top'],[name='status']").bootstrapSwitch();
    </script>

    <script type="text/javascript">
        function initEdit() {
            const grade = $('select[name="grade"]').val();
            if (grade) {
                const subjects = $('select[name="subject"]').data('subjects');
                const subjectId = $('select[name="subject"]').data('subject-id');
                let html = '<option value=""></option>';
                subjects[grade].forEach(subject => {
                    const selected = subject.id === subjectId ? 'selected' : '';
                    html += `<option value="${subject.id}" ${selected}>${subject.name}</option>`;
                });
                $('select[name="subject"]').html(html);
                const subject = $('select[name="subject"]').val();
                if ($('select[name="subject"]').val()) {
                    $.ajax({
                        method: 'GET',
                        url: "{{ route('teacher_weekly_lesson.lessons') }}",
                        data: {
                            grade: grade,
                            subject: subject,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (data) {
                            const lessons = JSON.parse(data);
                            const lessonId= $('select[name="lesson"]').data('lesson-id');
                            let html = '<option value=""></option>';
                            lessons.forEach(lesson => {
                                const lessonJson = JSON.stringify(lesson);
                                const selected = lesson.id === lessonId ? 'selected' : '';
                                html += `<option data-lesson='${lessonJson}' ${selected} value="${lesson.id}">${lesson.ten_bai_hoc}</option>`
                            });
                            $('select[name="lesson"]').html(html);
                            const lesson = $('select[name="lesson"] option:selected').data('lesson');
                            $('#lesson_name').val(lesson.ten_bai_hoc);
                            $('#lesson_topic').val(lesson.bai_hoc);
                            $('#start_date_by_plan').val(lesson.start_date);
                            $('#end_date_by_plan').val(lesson.end_date);
                        }
                    });
                }
            }
        }
        $(document).ready(function () {
            $('.select2').select2();
            $('select[name="lesson"]').on('select2:select', function (e) {
                const lesson = $('select[name="lesson"] option:selected').data('lesson');
                $('#lesson_name').val(lesson.ten_bai_hoc);
                $('#lesson_topic').val(lesson.bai_hoc);
                $('#start_date_by_plan').val(lesson.start_date);
                $('#end_date_by_plan').val(lesson.end_date);
            });
            $('select[name="grade"]').on('change', function(e) {
                const grade = $(this).val();
                if (!grade) {
                    $('select[name="subject"]').html('');
                    $('select[name="lesson"]').html('');
                    $('select[name="lesson"]').val(null).trigger('change');
                    return;
                }
                const subjects = $('select[name="subject"]').data('subjects');
                let html = '<option value=""></option>';
                subjects[grade].forEach(subject => {
                    html += `<option value="${subject.id}">${subject.name}</option>`;
                });
                $('select[name="subject"]').html(html);
            });

            $('select[name="subject"]').on('change', function(e) {
                const grade = $('select[name="grade"]').val();
                const subject = $(this).val();
                if (grade && subject) {
                    $.ajax({
                        method: 'GET',
                        url: "{{ route('teacher_weekly_lesson.lessons') }}",
                        data: {
                            grade: grade,
                            subject: subject,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (data) {
                            const lessons = JSON.parse(data);
                            let html = '<option value=""></option>';
                            lessons.forEach(lesson => {
                                const lessonJson = JSON.stringify(lesson);
                                html += `<option data-lesson='${lessonJson}' value="${lesson.id}">${lesson.ten_bai_hoc}</option>`
                            });
                            $('select[name="lesson"]').html(html);
                        }
                    });
                }
            });
            $('.date').datepicker({
                format: 'dd-mm-yyyy',
                language: 'vi'
            });
            initEdit();
        });
    </script>
@endpush
