@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection
@push('scripts')
<script>
    var subjectLessons = {};
</script>
@endpush
                                    
@section('main')
    @php 
        $title = 'Kế hoạch giáo dục tổ chuyên môn';
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => 'Danh sách kế hoạch tổ', 'link' => route('school.regular_group.plan.index', ['id' => $school->id, 'rgId' => $regularGroup->id])],
            ['name' => $title]
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
                               
                                <!-- <input type="hidden" name="school_id" value="{{ $school->id }}"> -->
                                <div class="row">
                                    <div class="col-sm-12">
                                    <input type="hidden" name="regular_group_id" value="{{ $regularGroup->id }}">
                                    <div class="card card-school-plan collapse-icon accordion-icon-rotate">
                                        <div class="card-header">
                                        <strong class="card-title"></strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="accordion" id="accordionExample">
                                                <div class="row">
                                                    <div class="col-sm-4 offset-sm-4">
                                                        <select class="form-control" name="grade">
                                                            @foreach($regularGroup->groupGrades as $groupGrade)
                                                                <option value="{{ $groupGrade->grade}}">{{ GRADES[$groupGrade->grade] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="collapse-margin">
                                                    <div class="card-header" id="headingOne" data-toggle="collapse" role="button" data-target="#collapseOne"
                                                        aria-expanded="false" aria-controls="collapseOne">
                                                        <span class="lead collapse-title">
                                                        I. Căn cứ xây dựng kế hoạch
                                                        </span>
                                                    </div>
                                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <textarea class='form-control description' name='can_cu_xay_dung' value="{{ old('can_cu_xay_dung', $groupPlan->can_cu_xay_dung ?? '' ) }}">{{ old('can_cu_xay_dung', $groupPlan->can_cu_xay_dung ?? '' ) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="collapse-margin">
                                                    <div class="card-header" id="headingTwo" data-toggle="collapse" role="button" data-target="#collapseTwo"
                                                        aria-expanded="false" aria-controls="collapseTwo">
                                                        <span class="lead collapse-title">
                                                        II. Điều kiện thực hiện chương trình năm học
                                                        </span>
                                                    </div>
                                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <textarea class='form-control description' name='dieu_kien_thuc_hien' value="{{ old('dieu_kien_thuc_hien', $groupPlan->dieu_kien_thuc_hien ?? '' ) }}">{{ old('dieu_kien_thuc_hien', $groupPlan->dieu_kien_thuc_hien ?? '' ) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="collapse-margin">
                                                    <div class="card-header" id="headingFour" data-toggle="collapse" role="button" data-target="#collapse5"
                                                        aria-expanded="false" aria-controls="collapseFour">
                                                        <span class="lead collapse-title">
                                                        III. Kế hoạch dạy học các môn học, hoạt động giáo dục
                                                        </span>
                                                    </div>
                                                    <div id="collapse5" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-striped text-nowrap table-plan">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">Môn học</th>
                                                                            <th scope="col">Kế hoạch dạy học các môn học, hoạt động giáo dục</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($subjects as $subject) 
                                                                            <tr>
                                                                                <td>{{ $subject->name }}</td>
                                                                                @php 
                                                                                    $hasPlan = false;
                                                                                    $subjectPlan = isset($groupPlan) ? $groupPlan->subjectPlans->filter(function($item) use ($subject){
                                                                                        return $item->subject_id == $subject->id;
                                                                                    })->first() : null; 
                                                                                    if($subjectPlan) $hasPlan = true;
                                                                                @endphp
                                                                                <td><a class="btn btn-success btn-datatable" data-toggle="modal" data-target="#modalSubjectPlan{{$subject->id}}">{{ $hasPlan ? "Sửa kế hoạch môn học" : "Thêm kế hoạch môn học" }}</a></td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="collapse-margin">
                                                    <div class="card-header" id="headingFour" data-toggle="collapse" role="button" data-target="#collapse6"
                                                        aria-expanded="false" aria-controls="collapseFour">
                                                        <span class="lead collapse-title">
                                                        IV. Tổ chức thực hiện
                                                        </span>
                                                    </div>
                                                    <div id="collapse6" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <textarea class='form-control description' name='to_chuc_thuc_hien' value="{{ old('to_chuc_thuc_hien', $groupPlan->to_chuc_thuc_hien ?? '' ) }}">{{ old('to_chuc_thuc_hien', $groupPlan->to_chuc_thuc_hien ?? "1. Giáo viên( Giáo viên phụ trách môn học, giáo viên chủ nhiệm)<br/>2. Tổ trưởng<br/>3. Tổng phụ trách đội" ) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                @foreach($subjects as $subject)
                                    <div class="modal fade" id="modalSubjectPlan{{$subject->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Kế hoạch môn {{ $subject->name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped text-nowrap table-plan" id="tableSubjectPlan{{$subject->id}}">
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="2">STT</th>
                                                                <th scope="col" rowspan="2">Tuần, tháng</th>
                                                                <th scope="col" colspan="3">Chương trình và sách giáo khoa</th>
                                                                <th scope="col" rowspan="2">Nội dung điều chỉnh, bổ sung(nếu có)</th>
                                                                <th scope="col" rowspan="2">Ghi chú</th>
                                                                <th scope="col" rowspan="2">Hành động</th>
                                                            </tr>

                                                            <tr>
                                                                <th scope="col">Chủ đề/Mạch nội dung</th>
                                                                <th scope="col">Tên bài học</th>
                                                                <th scope="col">Tiết học/thời lượng</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($groupPlan) && count($groupPlan->subjectPlans) > 0)
                                                                @php $subjectPlan = $groupPlan->subjectPlans->filter(function($item) use ($subject){
                                                                    return $item->subject_id == $subject->id;
                                                                })->first(); @endphp
                                                                @if($subjectPlan)
                                                                    
                                                                    @php 
                                                                        $subjectPlanItems = (array) json_decode($subjectPlan->content);
                                                                        $subjectPlanItems = array_values($subjectPlanItems);
                                                                        $counter = count($subjectPlanItems) + 1;
                                                                    @endphp

                                                                    @foreach($subjectPlanItems as $index => $lesson)
                                                                        <tr>
                                                                            <td>{{$index + 1 }}</td>
                                                                            @foreach(['tuan_thang', 'chu_de', 'ten_bai_hoc', 'so_tiet', 'noi_dung_dieu_chinh', 'ghi_chu'] as $field)
                                                                                <td><textarea name="subjectPlans[{{$subject->id}}][{{$index}}][{{$field}}]" value="{{$lesson->$field ?? ''}}">{{ $lesson->$field ?? '' }}</textarea></td>
                                                                            @endforeach
                                                                            <td><a class="delete-row btn btn-danger">Xoá</a></td>
                                                                        </tr>
                                                                    @endforeach

                                                                @endif
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    <a class='btn btn-success' id="addRow{{$subject->id}}">Thêm dòng</a>
                                                    
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">Lưu</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    @push('scripts')
                                        <script>
                                            subjectLessons['table'+"{{$subject->id}}"] = $('#tableSubjectPlan{{$subject->id}}').DataTable({
                                                "searching": false,
                                                "lengthChange": false,
                                                "paging": false
                                            });
                                            subjectLessons['table'+"{{$subject->id}}"+'counter'] = parseInt("{{ isset($counter) ? $counter : 1}}");

                                            $('#modalSubjectPlan{{$subject->id}}').on('click', '#addRow{{$subject->id}}', function (e) {
                                                e.preventDefault();
                                               
                                                var newRow = `<tr>` +
                                                    `<td>${subjectLessons['table'+"{{$subject->id}}"+'counter']}</td>`+
                                                    `<td><textarea name="subjectPlans[{{$subject->id}}][${subjectLessons['table'+"{{$subject->id}}"+'counter']}][tuan_thang]" value=""></textarea></td>`+
                                                    `<td><textarea name="subjectPlans[{{$subject->id}}][${subjectLessons['table'+"{{$subject->id}}"+'counter']}][chu_de]" value=""></textarea></td>`+
                                                    `<td><textarea name="subjectPlans[{{$subject->id}}][${subjectLessons['table'+"{{$subject->id}}"+'counter']}][ten_bai_hoc]" value=""></textarea></td>`+
                                                    `<td><textarea name="subjectPlans[{{$subject->id}}][${subjectLessons['table'+"{{$subject->id}}"+'counter']}][so_tiet]" value=""></textarea></td>`+
                                                    `<td><textarea name="subjectPlans[{{$subject->id}}][${subjectLessons['table'+"{{$subject->id}}"+'counter']}][noi_dung_dieu_chinh]" value=""></textarea></td>`+
                                                    `<td><textarea name="subjectPlans[{{$subject->id}}][${subjectLessons['table'+"{{$subject->id}}"+'counter']}][ghi_chu]" value=""></textarea></td>`+
                                                    `<td><a class="delete-row btn btn-danger">Xoá</a></td>`+
                                                    `</tr>`;
                                            
                                                $('#tableSubjectPlan{{$subject->id}} tbody').append(newRow);
                                                
                                                subjectLessons['table'+"{{$subject->id}}"+'counter'] += 1;
                                            });

                                            $('#modalSubjectPlan{{$subject->id}}').on('click', '.delete-row', function(){
                                                $(this).parents('tr').remove();
                                            });

                                        </script>
                                    @endpush
                                @endforeach

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