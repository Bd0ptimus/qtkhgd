@extends('layouts.contentLayoutMaster')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ $title_description??'' }}</h2>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                    <div class="box-body">
                        <div class="fields-group">

                            <!-- Target details -->
                            <div class="row">
                                <input type="hidden" name="school_id" value="{{ $school->id }}">    
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Danh sách các tiêu chí</h4>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body card-dashboard">
                                                <div class="table-responsive">
                                                    <table class="table zero-configuration" id="shool-subject">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">STT</th>
                                                            <th scope="col">Tên Tiêu chí</th>
                                                            <th scope="col">Trọng số</th>
                                                            <th scope="col">Hành động</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($target->mainPoints as $key => $mainPoint)
                                                            <tr>
                                                                <td scope="row" class="font-weight-bold">{{$key+1}}</td>
                                                                <td class="font-weight-bold">{{ $mainPoint->content}}</td>
                                                                <td class="font-weight-bold">{{ $mainPoint->index_point}}%</td>
                                                                    <td>   
                                                                        <a style="margin-top: 3px; text-decoration: none; color:white;" type="button"
                                                                            name="result"
                                                                            class="btn btn-datatable btn-success"
                                                                            onclick="handleAssign({{$mainPoint->id}}, '{{route('school.target.assign.point', ['id' => $school->id, 'pointId' => $mainPoint->id])}}')"
                                                                            data-toggle="modal" 
                                                                            >
                                                                            <i class="fa fa-pencil" aria-hidden="true"></i>Giao tiêu chí
                                                                        </a>
                                                                        <a style="margin-top: 3px; text-decoration: none; color:white;" type="button"
                                                                            name="result"
                                                                            onclick="handleShowStatisticModal({{$mainPoint->id}}, '{{ route('school.target.get.sub.point', ['id' => $school->id, 'pointId' => $mainPoint->id]) }}')"
                                                                            class="btn btn-datatable btn-info update-item">
                                                                            <i class="fa fa-pencil" aria-hidden="true"></i>Thống kê
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
                            <br/>
                           
                            {{-- start modal --}}
                            <form method="post" accept-charset="UTF-8" data-url="{{$urlAssign}}" class="form-horizontal"
                            id="form-main" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="point_id" id="mainPoint-id" value="">
                                <div class="modal fade bd-example-modal-lg" id="modal-assign-point" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Giao tiêu chí</h5>
                                                <button type="button" class="close" data-dismiss="modal" data-target="#modalLessonSampleDetail" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    {{-- content modal --}}
                                                    <div class="option-radio d-flex" id="radio-assign">
                                                        <h4 style="padding-right: 45px;">Giao nhiệm vụ</h4>
                                                        <div class="form-check" style="padding-right:5px;">
                                                            <label class="form-check-label">
                                                                <input type="radio" class="form-check-input" id="radio-staff" value="1" name="optradio">Cho giáo viên
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input type="radio" class="form-check-input" id="radio-class" value="2" name="optradio">Cho lớp học
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <br/>
                                                    <div id="assign-point-staff" class="d-none">
                                                        <input type="checkbox" id="selectAll"/> <strong>Chọn toàn bộ giáo viên</strong>
                                                        @php
                                                            $checkAssignWithClass = false;
                                                        @endphp
                                                        @foreach($school->staffs as $staff)
                                                        @php 
                                                        $filtered = collect($staffPoints)->filter(function ($value, $key) use($staff){
                                                            return $value->staff_id == $staff->id && !is_null($value->class_id) && !is_null($value->subject_id);
                                                        }); 
                                                        if(count($filtered)) {
                                                           $checkAssignWithClass = true;
                                                           break;
                                                        }
                                                        @endphp
                                                       @endforeach
                                                        <input type="checkbox" name="assignWithClass" {{$checkAssignWithClass ? "checked" : ""}} /> <strong>Phân bố cho từng lớp học mà giáo viên giảng dạy</strong>
                                                       <hr>
                                                       <!-- List Staff -->
                                                       @php $check @endphp
                                                       <div class="row">
                                                           @foreach($school->staffs as $staff)
                                                               @php $filtered = collect($staffPoints)->filter(function ($value, $key) use($staff){
                                                                   return $value->staff_id == $staff->id;
                                                               }); @endphp
                                                                 
                                                               <div class="col-md-3" style="padding-bottom: 5px;">
                                                                    <div style="display:inline-block;">
                                                                        <input @if(count($filtered)) checked @endif class="assign-checkbox" id="assign-staff{{$staff->id}}" name="assigns[{{$staff->id}}]" type="checkbox" data-name="{{$staff->fullname}}" value="{{ $staff->id }}"/>
                                                                        <span>{{ $staff->fullname }}</span>
                                                                    </div>
                                                               </div>
                                                           @endforeach
                                                       </div>
                                                    </div>
                                                    {{-- Giao cho lớp học --}}
                                                    <div id="assign-point-class" class="d-none">
                                                       <div class="row">
                                                           @foreach($school->classes as $class)
                                                               <div class="col-md-3" style="padding-bottom: 5px;">
                                                                    <div style="display:inline-block;">
                                                                        <input class="assign-checkbox-class" name="assign_classes[{{$class->id}}]" type="checkbox" value="{{ $class->homeroom_teacher }}"/>
                                                                        <span>{{ $class->class_name }}</span>
                                                                    </div>
                                                               </div>
                                                           @endforeach
                                                       </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" id="submit-assign-point{{$mainPoint->id}}" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                          {{-- end modal --}}

                          {{-- modal statistic --}}
                            <div class="modal fade bd-example-modal-lg" id="modal-statistic-point" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Thống kê</h5>
                                            <button type="button" class="close" data-dismiss="modal" data-target="#modalLessonSampleDetail" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-content">
                                                                <div class="card-body card-dashboard">
                                                                    <div class="table-responsive">
                                                                        <table class="table zero-configuration" id="shool-subject">
                                                                            <thead>
                                                                            <tr>
                                                                                <th scope="col">STT</th>
                                                                                <th scope="col">Giáo viên</th>
                                                                                <th scope="col">Lớp học</th>
                                                                                <th scope="col">Môn học</th>
                                                                                <th scope="col">Kết quả</th>
                                                                                <th scope="col">Trọng số</th>
                                                                                <th scope="col">Đánh giá kết quả</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody id="list-sub-points">
                                                                       
                                                                                
                                                                 
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          {{-- modal end statistic --}}
                        </div>
                    </div>
                    <!-- /.box-body -->
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

    {{-- switch --}}
    <script src="{{ asset('admin/plugin/bootstrap-switch.min.js')}}"></script>

    <script type="text/javascript">
        $("[name='top'],[name='status']").bootstrapSwitch();
    </script>

    <script type="text/javascript">
         const staffPoints  = <?php echo $staffPoints; ?>;
         const staffs  = <?php echo $school->staffs; ?>;
         console.log(staffs);
        $(document).ready(function () {
            $('.select2').select2();
            $('.select-target').on('change', function(e) {
                $.ajax({
                    method: 'get',
                    url : `{{ route('ajax_get_system_target_by_id') }}?id=${$(this).val()}`,
                    success: function (res) {
                        var data = JSON.parse(res);
                        $('input[name="title"]').val(data.title);
                        $('textarea[name="solution"]').val(data.solution);
                        $('textarea[name="description"]').val(data.description);
                        $('select[name="type"]').val(data.type).change();
                    }
                });
                
            });
           
            
            $('#selectAll').on('click', function(){
                $('.assign-checkbox').prop('checked', $(this).prop('checked'));
                if($(this).prop('checked')) {
                    $('.assign-checkbox:checked').parent().parent().find(".span-index-point").remove();
                    $('.assign-checkbox:checked').each(function(i, obj) {
                    let staff_id = $(this).val();
                        $(this).parent().parent().append(`<span class='span-index-point'><span style="padding-right: 5px;">-</span><input required type="number" name="index_point[${staff_id}]"  style="width:30px; height:30px;" class="index-point" /></span>`);
                    });
                }
                else {
                    $('.assign-checkbox').parent().parent().find(".span-index-point").remove();
                }
              
               
            });
            $('.assign-checkbox').on('click', function(){
                const is_check = $(this).is(":checked");
                const staff_id = $(this).val();
                if(is_check) {
                       $(this).parent().parent().append(`<span class='span-index-point'><span style="padding-right: 5px;">-</span><input required type="number" name="index_point[${staff_id}]"  style="width:30px; height:30px;" class="index-point" /></span>`);
                    }
                    else {
                        $(this).parent().parent().find(".span-index-point").remove();
                    }
            });

            $('.assign-checkbox-class').on('click', function(){
                const is_check = $(this).is(":checked");
                const staff_id = $(this).val();
                if(is_check) {
                       $(this).parent().parent().append(`<span class='span-index-point-class'><span style="padding-right: 5px;">-</span><input required type="number" name="index_point_class[${staff_id}]"  style="width:30px; height:30px;" class="index-point-class" /></span>`);
                    }
                    else {
                        $(this).parent().parent().find(".span-index-point-class").remove();
                    }
            });

            $("#form-main").on("keyup", ".index-point, .index-point-class", function() {
                $(this)[0].setAttribute("value", $(this).val());
            })
            $("input[name='optradio']").click(function(){
                if($('input:radio[name=optradio]:checked').val() == 1){
                    $('.span-index-point-class').remove();
                    $('input[type="checkbox"]').prop('checked', false);
                    $("#assign-point-staff").removeClass("d-none");
                    $("#assign-point-class").addClass("d-none");
                }
                else {
                    $('.span-index-point').remove();
                    $('input[type="checkbox"]').prop('checked', false);
                    $("#assign-point-staff").addClass("d-none");
                    $("#assign-point-class").removeClass("d-none");
                }
            });
            $("#form-main").submit(function( event ) {
                event.preventDefault();
                // const optradio = $('input[name="optradio"]:checked').val();
                // const indexPointElement = optradio == 1 ? $(".index-point") : $(".index-point-class");
                // if(!checkTotalInput(indexPointElement)) {
                //     alert("Tổng trọng số các tiêu chí cho giáo viên phải bằng 100%");
                //     return;
                // } 
                $.ajax({
                    type: 'post',
                    url: $(this).data("url"),
                    data: $(this).serialize(),
                    dataType:"json",
                    success: function (response) {
                        if(response.error == 0) {
                            $('#modal-assign-point').modal("hide");
                            alert(response.msg);
                        }
                       
                    }
                });

            });
        });

        function checkTotalInput(elements) {
            if(elements.length > 0) {
                let total = 0;
                $.each(elements, function (index, inputElement) { 
                     total += parseInt(inputElement.getAttribute("value"));
                });
                console.log(total);
                if(total == 100) return true;
                return false;
            }
            return true;
        }

        function handleAssign(point_id, url) {
            $("#assign-point-staff").addClass("d-none");
            $("#assign-point-class").addClass("d-none");
            $("#radio-staff").prop('checked', false);
            $("#radio-class").prop('checked', false);
            $('.span-index-point').remove();
            $('.span-index-point-class').remove();
            $('input[type="checkbox"]').prop('checked', false);
            $('input[name="assignWithClass"]').prop('checked', false);
            $('#mainPoint-id').val(point_id);
            $('#modal-assign-point').modal("show");
            $.ajax({
                type: "get",
                url: url,
                dataType:"json",
                success: function(response){
                    const staffPoints = response.staffPoints;
                    // hiển thị nút check assignWithClass
                    var checkAssignWithClass = staffPoints[0]?.class_id != null && staffPoints[0]?.subject_id != null;
                    var checkAssignPointStaff = staffPoints[0]?.class_id == null && staffPoints[0]?.subject_id == null;
                    var checkAssignClass = staffPoints[0]?.class_id != null && staffPoints[0]?.subject_id == null;
                    var arr_input = [];
                    if(checkAssignWithClass || checkAssignPointStaff) {
                        $("#assign-point-staff").removeClass("d-none");
                        $("#radio-staff").prop('checked', true);
                        var arr_input = [$(".assign-checkbox"), "", ""];
                    }
                    if(checkAssignClass) {
                        $("#assign-point-class").removeClass("d-none");
                        $("#radio-class").prop('checked', true);
                        var arr_input = [$(".assign-checkbox-class"), "-class", "_class"];
                    }
                    if(checkAssignWithClass)   $('input[name="assignWithClass"]').prop('checked', true);
                    // hiển thị những staff được assign
                    $.each(arr_input[0], function (index, element) { 
                        var staffPoint = staffPoints.find((point) => {
                            return parseInt(point.staff_id) == element.getAttribute("value");
                        });
                        if(staffPoint) {
                            console.log($(this));
                            element.checked = true;
                            $(this).parent().parent().append(`<span class='span-index-point${arr_input[1]}'><span style="padding-right: 5px;">-</span><input required type="number" name="index_point${arr_input[2]}[${staffPoint.staff_id}]" value="${staffPoint.index_point}" style="width:30px; height:30px;" class="index-point${arr_input[1]}" /></span>`);
                        }
                    });
                }
            });
        }
        function handleShowStatisticModal(point_id, url) {
            $("#modal-statistic-point").modal("show");
            $("#list-sub-points").empty();
            $.ajax({
                    method: 'get',
                    url : url,
                    success: function (res) {
                        const data = res.data;
                       if(data.length > 0) {
                        $.each(data, function (i, v) { 
                            $("#list-sub-points").append(`
                                <tr>
                                    <td class="font-weight-bold">${i+1}</td>
                                    <td class="font-weight-bold">${v.staff.fullname}</td>
                                    <td class="font-weight-bold">${v?.teacher_class?.class_name ?? ""}</td>
                                    <td class="font-weight-bold">${v?.teacher_subject?.name ?? ""}</td>
                                    <td class="font-weight-bold">${parseFloat(v.result).toFixed(1)}%</td>
                                    <td class="font-weight-bold">${v.index_point}%</td>
                                    <td class="font-weight-bold">${parseFloat(v.final_point).toFixed(1)}%</td>
                                </tr>
                            `)
                        });
                       } 
                       else {
                            $("#list-sub-points").append(`<tr><td colspan="7" style="text-align: center;">Không có dữ liệu</td></tr>`)
                       } 
                    }
                });
        }
    </script>
@endpush
