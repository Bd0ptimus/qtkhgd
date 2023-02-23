@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách bài giảng</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="GET">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select class="form-control" name="month_year">
                                                    <option value="">Hiển thị toàn bộ</option>
                                                    @foreach ($monthYears as $monthYear)
                                                        <option value="{{ $monthYear }}" {{ request('month_year') == $monthYear ? 'selected' : '' }}>{{ $monthYear }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4">
                                                <button class="btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                                                    {{ trans('admin.apply') }}
                                                </button>
                                            </div>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex">
                                <div class="text-nowrap">
                                    <a type="button"
                                        class="btn btn-flat btn-success"
                                        href="{{ $createRouting }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm bài giảng
                                    </a>
                                    
                                    <a type="button"
                                        class="btn btn-flat btn-info"
                                        href="{{ route('teacher_weekly_lesson.export', [
                                            'month_year=' . request('month_year'),
                                        ]) }}">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                        Tải danh sách bài giảng
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="weekly-lesson">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên bài giảng</th>
                                        <th scope="col">Chuyên mục</th>
                                        <th scope="col">Ngày bắt đầu theo kế hoạch</th>
                                        <th scope="col">Ngày kết thúc theo kế hoạch năm </th>
                                        <th scope="col">Ngày bắt đầu thực tế </th>
                                        <th scope="col">Ngày kết thúc thực tế </th>
                                        <th scope="col">Đánh gía tiến độ theo năm</th>
                                        <th scope="col">Đánh giá tiến độ theo tuần </th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($teacherWeeklyLessons as $key => $teacherWeeklyLesson)
                                        <tr>
                                            <td scope="row" class="font-weight-bold">{{ $key + 1}}</td>
                                            <td>{{ $teacherWeeklyLesson->teacherLesson->ten_bai_hoc }}</td>
                                            <td>{{ $teacherWeeklyLesson->teacherLesson->bai_hoc }}</td>
                                            <td>{{ $teacherWeeklyLesson->teacherLesson->start_date }}</td>
                                            <td>{{ $teacherWeeklyLesson->teacherLesson->end_date }}</td>
                                            <td>{{ $teacherWeeklyLesson->start_date }}</td>
                                            <td>{{ $teacherWeeklyLesson->end_date }}</td>
                                            <td>{{ $teacherWeeklyLesson->getProgressByYear() }}</td>
                                            <td>{{ $teacherWeeklyLesson->getProgressByWeek() }}</td>
                                            <td>
                                                 <button style="margin-top: 3px" type="button"  data-toggle="modal" data-target="#exampleModal-{{ $teacherWeeklyLesson->id }}"
                                                    name=""
                                                    class="btn btn-flat btn-success">
                                                    <i class="fa fa-gear" aria-hidden="true"></i>Tiến độ
                                                </button>
        
                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal-{{ $teacherWeeklyLesson->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <form method="POST" action="{{ route('teacher_weekly_lesson.update-progress') }}">
                                                    @method('PATCH')
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Cập nhật tiến độ</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @foreach ($teacherWeeklyLesson->teacherWeeklyLessonProgresses as $progresses)
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                    <input type="hidden" name="teacher_weekly_lesson_id" value="{{ $teacherWeeklyLesson->id  }}">
                                                                    <input type="checkbox" class="form-check-input"
                                                                    name="progresses[{{ $progresses->id }}]" {{ $progresses->is_taught ? 'checked' : ''}}>
                                                                    {{ $progresses->schoolClass->class_name }}
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                        </div>
                                                    </div>
                                                    </form>
                                                    </div>
                                                </div>
                                                <a style="margin-top: 3px" type="button" 
                                                    class="btn btn-flat btn-info"
                                                    href="{{ route('school.staff.plan.edit', [
                                                        'school_id' => $schoolId, 
                                                        'staffId' => $staffId,
                                                        'rgId' => $teacherWeeklyLesson->teacherLesson->plan->regular_group_id, 
                                                        'planId' => $teacherWeeklyLesson->teacherLesson->plan->id]) }}"
                                                    >
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-success"
                                                    href="{{ route('teacher_weekly_lesson.edit', [
                                                                        'weeklyLessonId' => $teacherWeeklyLesson->id
                                                    ]) }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                </a>
                                                <a style="margin-top: 3px" type="button"
                                                    class="btn btn-flat btn-danger delete-item"
                                                    data-url="{{ route('teacher_weekly_lesson.destroy', [
                                                          'weeklyLessonId' => $teacherWeeklyLesson->id
                                                    ]) }}" href="#">
                                                    <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2({
                allowClear: true
            });

            $('#weekly-lesson').DataTable({
                pageLength: 25,
                searching: false
            });


            $('#weekly-lesson').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá môn học này?');
                if(confirmDelete) {
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            element.parents('tr').remove();
                            alert('Đã xoá thành công!');
                        },
                        error: function() {
                            alert('Không xóa được!');
                        }
                    });
                }
            });
        });

    </script>
@endsection
