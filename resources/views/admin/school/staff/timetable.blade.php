@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = 'Xem thời khoá biểu';
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $title],
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @if($timetable)
                    <div class="card-header">
                        <h4 class="card-title">Thời khoá biểu {{ $staff->fullname}} từ ngày {{ $timetable->from_date }}</h4>
                        <a href="{{ route('school.staff.export_timetable', ['school_id' => $school->id, 'staffId' => $staff->id ]) }}" 
                            class="btn btn-primary"
                        >Tải thời khóa biểu</a>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration timetable" id="shool-regular-group">
                                    <thead>
                                    <tr>
                                        <th scope="col">Buổi</th>
                                        <th scope="col">Tiết</th>
                                        <th scope="col">Thứ 2</th>
                                        <th scope="col">Thứ 3</th>
                                        <th scope="col">Thứ 4</th>
                                        <th scope="col">Thứ 5</th>
                                        <th scope="col">Thứ 6</th>
                                        <th scope="col">Thứ 7</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                   
                                        @foreach([1,2,3,4,5,6,7,8,9] as $index)
                                        <tr>
                                            @if($index == 1)
                                             
                                                <td rowspan="5">Buổi sáng</td>
                                            @endif
                                
                                            @if($index == 6)
                                                <td rowspan="4">Buổi chiều</td>
                                            @endif
                                            
                                            <td style="text-align: left">{{ $index }} </td>
                                            @foreach(['mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $date)
                                                @if($date == 'mon' && $index == 1)
                                                    <td><strong>Chào cờ</strong></td>
                                                @else
                                                    <td>
                                                       @php 
                                                            $classLesson = $timetable->classLessons->filter(function ($item) use ($date,$index) {
                                                                return $item->slot == $date.'_'.$index;
                                                            })->first();
                                                            if($classLesson) echo $classLesson->classSubject->subject->name." - ".$classLesson->classSubject->class->class_name;
                                                       @endphp
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                                
                            </div>
                        </div>
                    </div>
                    @else
                        <h4>Hiện chưa có thời khoá biểu. Vui lòng quay lại sau.</h4>
                    @endif
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

            $('#shool-regular-group').DataTable();

            $('#shool-regular-group').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá tổ chuyên môn này?');
                if(confirmDelete) {
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            element.parents('tr').remove();
                            alert('Đã xoá Tổ chuyên môn');
                        }
                    });
                }
            });
        });
    </script>
@endsection
