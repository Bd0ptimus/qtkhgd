@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('main')
    @php
        $title = 'Duyệt kế hoạch bài giảng';
        $breadcrumbs = [['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])], ['name' => $title]];
    @endphp
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Duyệt kế hoạch bài giảng của giáo viên - {{ $regularGroup->name }}</div>
                    </div>
                    <div class="card-content">

                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="lessonSubmitedTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">Tên giáo viên</th>
                                            <th scope="col">Ngày tạo</th>
                                            @if ($school->school_type == 6)
                                                <th scope="col">Tháng</th>
                                            @else
                                                <th scope="col">Môn học</th>
                                            @endif
                                            <th scope="col">Trạng thái duyệt</th>
                                            <th scope="col">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($teacherLessons as $key => $teacherLesson)
                                            <tr>
                                                <th scope="row">{{ $key + 1 }}</th>
                                                <th scope="row">{{ $teacherLesson->plan->staff->fullname ?? '' }}</th>
                                                <td>{{ $teacherLesson->plan->created_at }}</td>
                                                @if ($school->school_type == 6)
                                                    <td>Tháng {{ $teacherLesson->plan->month }}</td>
                                                @else
                                                    <td>{{ $teacherLesson->plan->subject->name ?? '' }}</td>
                                                @endif
                                                <td>{{ PLAN_STATUSES[$teacherLesson->status] }}</td>
                                                <td>
                                                    <a href="#"
                                                        class="btn btn-flat btn-info btn-datatable show-modal-lesson"
                                                        data-url="{{ route('ajax_get_teacher_lesson_by_id', ['id' => $teacherLesson->id, 'view' => true]) }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>Xem bài giảng
                                                    </a>
                                                    <a style="margin-top: 3px" href="#"
                                                        class="btn btn-flat btn-success btn-datatable" data-toggle="modal"
                                                        data-target="#modalHistory{{ $teacherLesson->id }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>Lịch sử
                                                    </a>

                                                    @if ($teacherLesson->status !== PLAN_APPROVED)
                                                        <a style="margin-top: 3px" href="#"
                                                            class="btn btn-flat btn-warning btn-datatable"
                                                            data-toggle="modal"
                                                            data-target="#modalAddNote{{ $teacherLesson->id }}">
                                                            <i class="fa fa-pencil" aria-hidden="true"></i>Nhận xét
                                                        </a>
                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="btn btn-flat btn-success"
                                                            href="{{ route('school.staff.plan.lesson_approve', ['school_id' => $school->id, 'staffId' => $teacherLesson->plan->staff->id, 'rgId' => $teacherLesson->plan->regular_group_id,'lessonId' => $teacherLesson->id])}}">
                                                            <i class="fa fa-check" aria-hidden="true"></i>Duyệt kế hoạch
                                                        </a>

                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="btn btn-flat btn-danger"
                                                            href="{{ route('school.staff.plan.lesson_deny', ['school_id' => $school->id, 'staffId' => $teacherLesson->plan->staff->id, 'rgId' => $teacherLesson->plan->regular_group_id,'lessonId' => $teacherLesson->id])}}">
                                                            <i class="fa fa-close" aria-hidden="true"></i>Chưa duyệt
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @foreach ($teacherLessons as $key => $teacherLesson)
                                    <div class="modal fade" id="modalHistory{{ $teacherLesson->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Lịch sử</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table
                                                            class="table table-bordered table-striped text-nowrap table-plan">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Thời gian</th>
                                                                    <th scope="col">Nội dung</th>
                                                                    <th scope="col">Trạng thái kế hoạch</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($teacherLesson->histories as $history)
                                                                    <tr>
                                                                        <td>{{ $history->created_at }}</td>
                                                                        <td>{{ $history->notes }}</td>
                                                                        <td>{{ PLAN_STATUSES[$history->status] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary"
                                                        data-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="modalAddNote{{ $teacherLesson->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Thêm nhận xét</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form method="POST"
                                                    action="{{ route('school.staff.teacher_lesson.add_review', ['school_id' => $school->id, 'staffId' => $teacherLesson->plan->staff_id, 'rgId' => $teacherLesson->plan->regular_group_id, 'lessonId' => $teacherLesson->id]) }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <textarea class='form-control' name='notes'></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Nhận xét</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div id="lessonContent"></div>
                            </div>
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
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
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

            $('#lessonSubmitedTable').DataTable();

            $('#lessonSubmitedTable').on('click', '.show-modal-lesson', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).data('url'),
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('#lessonContent').empty().append(res.view);
                        $('#modalLessonContent').modal('show');
                    }
                });
            });

        });
    </script>
@endsection
