@extends('layouts/contentLayoutMaster')
@php
$schoolLevels = SCHOOL_TYPES;
@endphp
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
    $title = 'Kế hoạch giáo dục tổ chuyên môn các trường';
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('district.manage', ['id' => $district->id])],
            ['name' => $title],
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row align-items-center">
            <div class="col-sm-2 small-dropdown">
                <div class="form-group">
                    <label for="formGroupExampleInput2">Cấp học</label>
                    <select class="form-control parent select2" id="filter-level" style="width: 100%;"
                        name="level">
                        <option value="">Tất cả</option>
                         @foreach ($schoolLevels as $key => $name)
                            <option value="{{ $key }}"
                                @if (isset($levelFilter) && $key == $levelFilter) selected @endif>
                                {{ $name }}
                            </option>
                        @endforeach
                        
                    </select>
                </div>
            </div>

            <div class="col-sm-2 small-dropdown">
                <div class="form-group">
                    <label for="formGroupExampleInput2">Trường</label>
                    <select class="form-control parent select2" id="filter-school" style="width: 100%;"
                        name="schoolFilter">
                        <option value="">Tất cả</option>
                         @foreach ($districtSchools as $key => $school)
                            <option value="{{ $school->id }}"
                                @if (isset($schoolFilter) && $school->id == $schoolFilter) selected @endif>
                                {{ $school->school_name }}
                            </option>
                        @endforeach
                        
                    </select>
                </div>
            </div>

            <div class="col-sm-2 small-dropdown">
                <div class="form-group">
                    <label for="formGroupExampleInput2">Môn học</label>
                    <select class="form-control parent select2" id="filter-subject" style="width: 100%;"
                        name="subjectFilter">
                        <option value="">Tất cả</option>
                         @foreach ($subjectGrades as $key => $subject)
                            <option value="{{ $subject->id }}"
                                @if (isset($subjectFilter) && $subject->id == $subjectFilter) selected @endif>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                        
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    
                    <div class="card-content">
                        
                        <div class="card-body card-dashboard">
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-sm-12">
                                   
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <form class="" method="GET" >
                                        
                                        
                                    </form>
                                </div>
                               
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-regular-group">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Trường</th>
                                        <th scope="col">Tổ</th>
                                        <th scope="col">Ngày tạo</th>
                                        <th scope="col">Khối lớp</th>
                                       
                                        <th scope="col">Tháng</th>
                                    
                                        <th scope="col">Môn học</th>
                                       
                                        <th scope="col">Trạng thái duyệt</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($plans as $key => $groupPlan)
                                        
                                        <tr>
                                            <th scope="row">#{{ $groupPlan->id }}</th>
                                            <th scope="row">{{ $groupPlan->group->school->school_name }}</th>
                                            <th scope="row">{{ $groupPlan->group->name }}</th>
                                            <td>{{ $groupPlan->created_at }}</td>
                                            <td>{{ $groupPlan->grade ? GRADES[$groupPlan->grade] : '' }}</td>
                                            <td> {{ $groupPlan->month ? "Tháng ". $groupPlan->month : ''}}</td>
                                            <td>{{ $groupPlan->planSubject->name ?? ''}}</td>
                                            <td>{{ PLAN_STATUSES[$groupPlan->status]}}</td>
                                            <td>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info btn-datatable"
                                                    href="{{ route('school.regular_group.plan.edit', ['id' => $groupPlan->group->school->id, 'rgId' => $groupPlan->regular_group_id, 'planId' => $groupPlan->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                </a>
                                                <a style="margin-top: 3px" href="#" class="btn btn-flat btn-success btn-datatable"
                                                    data-toggle="modal" data-target="#modalHistory{{$groupPlan->id}}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Lịch sử
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-warning btn-datatable"
                                                    href="{{ route('school.regular_group.plan.download', ['id' => $groupPlan->group->school->id, 'rgId' => $groupPlan->regular_group_id, 'planId' => $groupPlan->id]) }}">
                                                    <i class="fa fa-book" aria-hidden="true"></i>Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @foreach($plans as $key => $groupPlan)
                                    <div class="modal fade" id="modalHistory{{$groupPlan->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Lịch sử</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped text-nowrap table-plan">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Thời gian</th>
                                                                <th scope="col">Nội dung</th>
                                                                <th scope="col">Trạng thái kế hoạch</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($groupPlan->histories as $history)
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
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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

            $('#shool-regular-group').DataTable();

            $('#shool-regular-group').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá kế hoạch tổ chuyên môn này?');
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
                            alert('Đã xoá kế hoạch Tổ chuyên môn');
                        }
                    });
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
            if(key =='level'){
                search_params.set('school', '');
                search_params.set('subject', '');

            }

            // change the search property of the main url
            url.search = search_params.toString();

            return url.toString();
        }


        $('#filter-level').change(function() {
            let optionSelected = $(this).find("option:selected");
            window.location.href = updateParameter(window.location.href, 'level', optionSelected.val())
        });


        $('#filter-school').change(function() {
            let optionSelected = $(this).find("option:selected");
            window.location.href = updateParameter(window.location.href, 'school', optionSelected.val())
        });

        $('#filter-subject').change(function() {
            let optionSelected = $(this).find("option:selected");
            window.location.href = updateParameter(window.location.href, 'subject', optionSelected.val())
        });
    </script>
@endsection
