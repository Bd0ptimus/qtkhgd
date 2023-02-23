@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = 'Kế hoạch giáo dục của trường';
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => 'Danh sách kế hoạch', 'link' => route('school.regular_group.plan.index', ['id' => $school->id, 'rgId' => $regularGroup->id])],
            ['name' => $title],
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard" style="margin: auto; text-align:center">
                            {{-- Accordion with margin start --}}
                            <section id="accordion-with-margin">
                                <form method="POST">
                                    @csrf
                                    <input type="hidden" name="regular_group_id" value="{{ $regularGroup->id }}">
                                    <div class="row">

                                        <div class="col-sm-4 offset-sm-2">
                                            <select class="form-control" name="grade">
                                               <!--  @foreach($regularGroup->groupGrades as $groupGrade)
                                                    <option value="{{ $groupGrade->grade}}">{{ GRADES[$groupGrade->grade] }}</option>
                                                @endforeach -->
                                                @foreach($school->getSchoolGrades() as $grade)
                                                    <option value="{{ $grade}}">{{ GRADES[$grade] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-control" name="subject">
                                                @foreach($regularGroup->subjects as $subject)
                                                    <option value="{{ $subject->id}}">{{ $subject->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="card card-school-plan collapse-icon accordion-icon-rotate">
                                                <div class="card-body">
                                                    <textarea sytle="" class='form-control description' name='content' value="{{ old('content', $groupPlan->content ?? $content ) }}">{{ old('content', $groupPlan->content ?? $content ) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(Admin::user()->inRoles([ROLE_GIAO_VIEN, ROLE_ADMIN, ROLE_SCHOOL_MANAGER]))
                                    <div class="d-flex col-md-4 offset-md-4">
                                        <div class="text-nowrap ml-1">
                                            <button type="submit" class="btn btn-success">
                                                Lưu kế hoạch
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                </form>
                            </section>
                            {{-- Accordion with margin end --}}
                            
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

            $('#shool-timetable').DataTable();

            $('#shool-timetable').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá thời khoá biểu này này?');
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
                            alert('Đã xoá Thời khoá biểu');
                        }
                    });
                }
            });
        });
    </script>
@endsection
