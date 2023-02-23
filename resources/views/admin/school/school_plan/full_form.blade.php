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
        $title = 'Kế hoạch giáo dục của trường';
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => 'Danh sách kế hoạch', 'link' => route('school.school_plan.index', ['id' => $school->id])],
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
                            @if (isset($create))
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="type_create">Chọn cách tạo kế hoạch</label>
                                        <select class="form-control" name="type_create" id="type_create">
                                            <option value="upload">Tải tệp</option>
                                            <option value="form">Nhập biểu mẫu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <form id="upload" action="{{ route('school.school_plan.upload', ['id' => $school->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="file" name="file" class="form-control"/>
                                        @if ($errors->has('file'))
                                            <p class="alert alert-danger mt-2">
                                                {{$errors->first('file')}}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success">Upload</button>
                                    </div>
                                </div>
                            </form>
                            @endif
                            <form id="form" method="POST" style="{{ isset($create) ? 'display: none;' : '' }}">
                                @csrf
                                <input type="hidden" name="school_id" value="{{ $school->id }}">
                                @if(Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_ADMIN, ROLE_SCHOOL_MANAGER]))
                                    <div class="d-flex col-md-4 offset-md-4">
                                        <div class="text-nowrap ml-1">
                                            <button type="submit" class="btn btn-success">
                                                Lưu kế hoạch
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card card-school-plan collapse-icon accordion-icon-rotate">
                                            <div class="card-header">
                                            <strong class="card-title"></strong>
                                            </div>
                                            <div class="card-body">
                                                <div class="accordion" id="accordionExample">
                                                    <div class="collapse-margin">
                                                    <div class="card-header" id="headingOne" data-toggle="collapse" role="button" data-target="#collapseOne"
                                                        aria-expanded="false" aria-controls="collapseOne">
                                                        <span class="lead collapse-title">
                                                        I. Căn cứ xây dựng kế hoạch
                                                        </span>
                                                    </div>

                                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='can_cu_1' value="{{ old('can_cu_1', $schoolPlan->can_cu_1 ?? '' ) }}">{{ old('can_cu_1', $schoolPlan->can_cu_1 ?? '' ) }}</textarea>
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
                                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                        
                                                        <div class="card-body">
                                                            <strong>1. Đặc điểm tình hình kinh tế, văn hóa, xã hội địa phương</strong>
                                                            <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='dac_diem_ktvhxh_21' value="{{ old('dac_diem_ktvhxh_21', $schoolPlan->dac_diem_ktvhxh_21 ?? '' ) }}">{{ old('dac_diem_ktvhxh_21', $schoolPlan->dac_diem_ktvhxh_21 ?? '' ) }}</textarea>
                                                            
                                                            <strong>2. Đặc điểm tình hình nhà trường năm học</strong>
                                                            <p>2.1. Đặc điểm học sinh của trường</p>
                                                            <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='dac_diem_hocsinh_221' value="{{ old('dac_diem_hocsinh_221', $schoolPlan->dac_diem_hocsinh_221 ?? '' ) }}">{{ old('dac_diem_hocsinh_221', $schoolPlan->dac_diem_hocsinh_221 ?? '' ) }}</textarea>
                                                            <p>2.2. Tình hình đội ngũ giáo viên, nhân viên, cán bộ quản lý</p>
                                                            <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='tinh_hinh_nhan_vien_222' value="{{ old('tinh_hinh_nhan_vien_222', $schoolPlan->tinh_hinh_nhan_vien_222 ?? '' ) }}">{{ old('tinh_hinh_nhan_vien_222', $schoolPlan->tinh_hinh_nhan_vien_222 ?? '' ) }}</textarea>
                                                            <p>2.3. Cơ sở vật chất, thiết bị dạy học; điểm trường, lớp ghép; cơ sở vật chất thực hiện bán trú, nội trú</p>
                                                            <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='co_so_vat_chat_23' value="{{ old('co_so_vat_chat_23', $schoolPlan->co_so_vat_chat_23 ?? '' ) }}">{{ old('co_so_vat_chat_23', $schoolPlan->co_so_vat_chat_23 ?? '' ) }}</textarea>
                                                        </div>
                                                    
                                                    </div>
                                                    </div>
                                                    <div class="collapse-margin">
                                                    <div class="card-header" id="headingThree" data-toggle="collapse" role="button"
                                                        data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                        <span class="lead collapse-title">
                                                        III. Mục tiêu giáo dục năm học
                                                        </span>
                                                    </div>
                                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <strong>1. Mục tiêu chung</strong>
                                                            <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='mtnh_chung_31' value="{{ old('mtnh_chung_31', $schoolPlan->mtnh_chung_31 ?? '' ) }}">{{ old('mtnh_chung_31', $schoolPlan->mtnh_chung_31 ?? '' ) }}</textarea>
                                                            
                                                            <strong>2. Chỉ tiêu cụ thể</strong>
                                                            <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='mtnh_cu_the_32' value="{{ old('mtnh_cu_the_32', $schoolPlan->mtnh_cu_the_32 ?? '' ) }}">{{ old('mtnh_cu_the_32', $schoolPlan->mtnh_cu_the_32 ?? '' ) }}</textarea>
                                                            
                                                        </div>
                                                    </div>
                                                    </div>
                                                    
                                                    <div class="collapse-margin">
                                                        <div class="card-header" id="headingFour" data-toggle="collapse" role="button" data-target="#collapseFour"
                                                            aria-expanded="false" aria-controls="collapseFour">
                                                            <span class="lead collapse-title">
                                                            IV. Tổ chức các môn học và hoạt động giáo dục trong năm học
                                                            </span>
                                                        </div>
                                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <strong>1. Phân phối thời lượng các môn học và hoạt động giáo dục</strong>
                                                                <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='phan_phoi_thoi_luong_41' value="{{ old('phan_phoi_thoi_luong_41', $schoolPlan->phan_phoi_thoi_luong_41 ?? '' ) }}">{{ old('phan_phoi_thoi_luong_41', $schoolPlan->phan_phoi_thoi_luong_41 ?? '' ) }}</textarea>
                                                                
                                                                <strong>2. Các hoạt động giáo dục tập thể và theo nhu cầu người học</strong><br>
                                                                <strong>2.1. Các hoạt động giáo dục tập thể thực hiện trong năm học</strong>
                                                                <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='hd_tap_the_421' value="{{ old('hd_tap_the_421', $schoolPlan->hd_tap_the_421 ?? '' ) }}">{{ old('hd_tap_the_421', $schoolPlan->hd_tap_the_421 ?? '' ) }}</textarea>
                                                                
                                                                <strong>2.2. Tổ chức hoạt động cho học sinh sau giờ học chính thức trong ngày, theo nhu cầu người học và trong thời gian bán trú tại trường</strong>
                                                                <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='hd_ngoai_gio_422' value="{{ old('hd_ngoai_gio_422', $schoolPlan->hd_ngoai_gio_422 ?? '' ) }}">{{ old('hd_ngoai_gio_422', $schoolPlan->hd_ngoai_gio_422 ?? '' ) }}</textarea>
                                                                
                                                                <strong>3. Tổ chức thực hiện kế hoạch giáo dục đối với các điểm trường</strong>
                                                                <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='to_chuc_thuc_hien_diem_truong_43' value="{{ old('to_chuc_thuc_hien_diem_truong_43', $schoolPlan->to_chuc_thuc_hien_diem_truong_43 ?? '' ) }}">{{ old('to_chuc_thuc_hien_diem_truong_43', $schoolPlan->to_chuc_thuc_hien_diem_truong_43 ?? '' ) }}</textarea>
                                                                
                                                                <strong>4. Khung thời gian thực hiện chương trình năm học và kế hoạch dạy học các môn học. hoạt động giáo dục</strong>
                                                                <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='khung_thoi_gian_44' value="{{ old('khung_thoi_gian_44', $schoolPlan->khung_thoi_gian_44 ?? '' ) }}">{{ old('khung_thoi_gian_44', $schoolPlan->khung_thoi_gian_44 ?? $defaultValues['khung_thoi_gian_44'] ) }}</textarea>
                                                                @foreach([1,2,3,4,5] as $grade)
                                                                    @php 
                                                                    
                                                                    if(isset($schoolPlan)) {
                                                                        $gradeDetail = $schoolPlan->gradeDetails->filter(function($item) use ($grade) {
                                                                            return $item->grade == $grade;
                                                                        })->first();
                                                                    }
                                                                    @endphp
                                                                    <strong>4.{{$grade}}. Đối với khối lớp {{$grade}}</strong><br/>
                                                                    <strong>a, Thời gian tổ chức các hoạt động giáo dục theo tuần/tháng trong năm học và số lượng tiết học các môn học, hoạt động giáo dục thực hiện theo tuần trong năm học</strong>
                                                                        <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='gradeDetails[{{$grade}}][thoi_gian_to_chuc_theo_tuan]' value="{{ old('gradeDetails[$grade][thoi_gian_to_chuc_theo_tuan]', $gradeDetail->thoi_gian_to_chuc_theo_tuan ?? $defaultValues['thoi_gian_to_chuc_theo_tuan'] ) }}">{{ old('gradeDetails[$grade][thoi_gian_to_chuc_theo_tuan]', $gradeDetail->thoi_gian_to_chuc_theo_tuan ?? $defaultValues['thoi_gian_to_chuc_theo_tuan'] ) }}</textarea>
                                                                    
                                                                        <strong>b, Kế hoạch dạy học các môn học, hoạt động giáo dục khối lớp {{$grade}}</strong>
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
                                                                                                    if(isset($gradeDetail) && count((array)json_decode($gradeDetail->ke_hoach_cac_mon)) > 0) {
                                                                                                        $subjectPlanContent = null;
                                                                                                        $gradeDetailObject = json_decode($gradeDetail->ke_hoach_cac_mon);
                                                                                                        foreach($gradeDetailObject as $subjectId => $content) {
                                                                                                            if($subjectId == $subject->id) $subjectPlanContent = $gradeDetailObject->{$subjectId};
                                                                                                        }
                                                                                                        if($subjectPlanContent) {
                                                                                                            foreach($subjectPlanContent as $index => $lesson) {
                                                                                                                foreach($lesson as $key => $value) {
                                                                                                                    if($value) $hasPlan = true;
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                @endphp
                                                                                                <td><a class="btn btn-success btn-datatable" data-toggle="modal" data-target="#modalSubjectPlan-{{$grade}}-{{$subject->id}}">{{ $hasPlan ? "Sửa kế hoạch môn học" : "Thêm kế hoạch môn học" }}</a></td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>

                                                                            @foreach($subjects as $subject)
                                                                            <div class="modal fade" id="modalSubjectPlan-{{$grade}}-{{$subject->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                                                            <table class="table table-bordered table-striped text-nowrap table-plan" id="tableSubjectPlan-{{$grade}}-{{$subject->id}}">
                                                                                                <thead>
                                                                                                    <tr>
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
                                                                                                    @if(isset($gradeDetail) && count((array)json_decode($gradeDetail->ke_hoach_cac_mon)) > 0)
                                                                                                        @php 
                                                                                                            $subjectPlanContent = null;
                                                                                                            $gradeDetailObject = json_decode($gradeDetail->ke_hoach_cac_mon);
                                                                                                            foreach($gradeDetailObject as $subjectId => $content) {
                                                                                                                if($subjectId == $subject->id) $subjectPlanContent = $gradeDetailObject->{$subjectId};
                                                                                                            }
                                                                                                        @endphp
                                                                                                        @if($subjectPlanContent)
                                                                                                            @php $counter = count((array) $subjectPlanContent) + 1; @endphp
                                                                                                            @foreach($subjectPlanContent as $index => $lesson)
                                                                                                                <tr>
                                                                                                                    @foreach(['tuan_thang', 'chu_de', 'ten_bai_hoc', 'so_tiet', 'noi_dung_dieu_chinh', 'ghi_chu'] as $field)
                                                                                                                        <td><textarea name="gradeDetails[{{$grade}}][ke_hoach_cac_mon][{{$subject->id}}][{{$index}}][{{$field}}]" value="{{$lesson->$field}}">{{ $lesson->$field }}</textarea></td>
                                                                                                                    @endforeach
                                                                                                                    <td><a class="delete-row btn btn-danger">Xoá</a></td>
                                                                                                                </tr>
                                                                                                            @endforeach
                                                                                                        @endif
                                                                                                    @endif
                                                                                                </tbody>
                                                                                            </table>
                                                                                            <a class='btn btn-success' id="addRow-{{$grade}}-{{$subject->id}}">Thêm dòng</a>
                                                                                            
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
                                                                                    subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"] = $('#tableSubjectPlan-{{$grade}}-{{$subject->id}}').DataTable({
                                                                                        "searching": false,
                                                                                        "lengthChange": false,
                                                                                        "paging": false,
                                                                                        
                                                                                    });
                                                                                    subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter'] = parseInt("{{ isset($counter) ? $counter : 1}}");


                                                                                    $('#modalSubjectPlan-{{$grade}}-{{$subject->id}}').on('click', '#addRow-{{$grade}}-{{$subject->id}}', function (e) {
                                                                                        e.preventDefault();
                                                                                        subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"].row.add([ // tuan_thang chu_de ten_bai_hoc so_tiet noi_dung_dieu_chinh ghi_chu
                                                                                            `<textarea name="gradeDetails[{{$grade}}][ke_hoach_cac_mon][{{$subject->id}}][${subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter']}][tuan_thang]" value=""></textarea>`,
                                                                                            `<textarea name="gradeDetails[{{$grade}}][ke_hoach_cac_mon][{{$subject->id}}][${subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter']}][chu_de]" value=""></textarea>`,
                                                                                            `<textarea name="gradeDetails[{{$grade}}][ke_hoach_cac_mon][{{$subject->id}}][${subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter']}][ten_bai_hoc]" value="">`,
                                                                                            `<textarea name="gradeDetails[{{$grade}}][ke_hoach_cac_mon][{{$subject->id}}][${subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter']}][so_tiet]" value=""></textarea>`,
                                                                                            `<textarea name="gradeDetails[{{$grade}}][ke_hoach_cac_mon][{{$subject->id}}][${subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter']}][noi_dung_dieu_chinh]" value=""></textarea>`,
                                                                                            `<textarea name="gradeDetails[{{$grade}}][ke_hoach_cac_mon][{{$subject->id}}][${subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter']}][ghi_chu]" value=""></textarea>`,
                                                                                            `<a class="delete-row btn btn-danger">Xoá</a>`,
                                                                                        ]).draw(false);
                                                                                        subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"+'counter'] += 1;
                                                                                    });

                                                                                    $('#modalSubjectPlan-{{$grade}}-{{$subject->id}}').on('click', '.delete-row', function(){
                                                                                        subjectLessons['table'+"-{{$grade}}-"+"{{$subject->id}}"].row( $(this).parents('tr') )
                                                                                        .remove()
                                                                                        .draw();
                                                                                    });
                                                                                    

                                                                                </script>
                                                                            @endpush
                                                                            @endforeach
                                                                        </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="collapse-margin">
                                                        <div class="card-header" id="headingFour" data-toggle="collapse" role="button" data-target="#collapse5"
                                                            aria-expanded="false" aria-controls="collapseFour">
                                                            <span class="lead collapse-title">
                                                            V. Giải pháp thực hiện
                                                            </span>
                                                        </div>
                                                        <div id="collapse5" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='giai_phap_thuc_hien_5' value="{{ old('giai_phap_thuc_hien_5', $schoolPlan->giai_phap_thuc_hien_5 ?? '' ) }}">{{ old('giai_phap_thuc_hien_5', $schoolPlan->giai_phap_thuc_hien_5 ?? '' ) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="collapse-margin">
                                                        <div class="card-header" id="headingFour" data-toggle="collapse" role="button" data-target="#collapse6"
                                                            aria-expanded="false" aria-controls="collapseFour">
                                                            <span class="lead collapse-title">
                                                            VI. Tổ chức thực hiện
                                                            </span>
                                                        </div>
                                                        <div id="collapse6" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <textarea {{ Admin::user()->inRoles([ROLE_GIAO_VIEN]) ? 'disabled' : '' }} class='form-control description' name='to_chuc_thuc_hien_6' value="{{ old('to_chuc_thuc_hien_6', $schoolPlan->to_chuc_thuc_hien_6 ?? '' ) }}">{{ old('to_chuc_thuc_hien_6', $schoolPlan->to_chuc_thuc_hien_6 ?? '' ) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_ADMIN, ROLE_SCHOOL_MANAGER]))
                                        <div class="d-flex col-md-4 offset-md-4">
                                            <div class="text-nowrap ml-1">
                                                <button type="submit" class="btn btn-success">
                                                    Lưu kế hoạch
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
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

            $('#type_create').on('change', function(e) {
                const type = $(this).val();
                $('#form').hide();
                $('#upload').hide();
                $('#' + type).show();
            });
        });
    </script>
@endsection
