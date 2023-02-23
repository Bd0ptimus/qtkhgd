@extends('layouts/contentLayoutMaster')

@php
    $title = 'Quản lý trường '.$school->school_name;
    $breadcrumbs = [
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
    ];
@endphp
@section('title', $title)

@section('vendor-style')
    {{-- Vendor Css files --}}
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/swiper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jquery.contextMenu.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/swiper.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/context-menu.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/toastr.css')) }}">
@endsection

@section('main')

{{-- Statistics card section start --}}
<section id="statistics-card">

  
</section>

{{-- // Statistics Card section end--}}

<section id="component-swiper-centered-slides-2">
  <div class="card">

    <!-- Nộp kế hoạch. duyệt kế hoạch -->
    <div class="card-header">
      <h4 class="card-title">Tổng quan</h4>
    </div>
    <div class="card-content">
      <div class="card-body pt-0">
        <div class="swiper-centered-slides-2 swiper-container p-1">
          <div class="swiper-wrapper">
            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{ route('school.target.index', ['id' => $school->id])}}">Chỉ tiêu năm học</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cấu hình thông tin  chung -->
    <div class="card-header">
      <h4 class="card-title">Cấu hình thông tin</h4>
    </div>
    <div class="card-content">
      <div class="card-body pt-0">
        <div class="swiper-centered-slides-2 swiper-container p-1">
          <div class="swiper-wrapper">
            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                <div class="swiper-text"><a href="{{route('admin.school.chuanhoa', ['id' => $school->id])}}">Chuẩn hoá dữ liệu</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                <div class="swiper-text"><a target="_blank"href="{{route('admin.school.view_branch_list', ['id' => $school->id])}}">Điểm trường</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{route('admin.school.view_class_list', ['id' => $school->id])}}">Lớp học</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{ route('admin.school.view_student_list', [ 'id' => $school->id ])}}">DS Học Sinh</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{ route('admin.school.import_student', [ 'id' => $school->id ])}}">Import Học Sinh</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{ route('admin.school.import_student_smas', [ 'id' => $school->id ])}}">Import Từ PM-S</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{route('admin.school.view_staff_list', ['id' => $school->id])}}">Nhân viên</a></div>
            </div>
           
            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{route('admin.school.linking_staff', ['id' => $school->id])}}">Giáo viên liên kết</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                <div class="swiper-text"><a target="_blank"href="{{route('admin.school.users', ['id' => $school->id])}}">Danh sách người dùng</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cấu hình chuyên môn -->
    <div class="card-header">
      <h4 class="card-title">Cấu hình nghiệp vụ chuyên môn</h4>
    </div>
    <div class="card-content">
      <div class="card-body pt-0">
        
        <div class="swiper-centered-slides-2 swiper-container p-1">
          <div class="swiper-wrapper">

            <div class="swiper-slide rounded py-1 px-1 d-flex">
                <div class="swiper-text" type="button" id="subject-management">
                    <a target="_blank" href="#">Quản lý môn học</a>
                </div>
            </div>

            <div class="swiper-slide rounded py-1 px-1 d-flex">
                <div class="swiper-text" type="button" id="regular-group-management">
                    <a target="_blank" href="#">Quản lý Tổ chuyên môn, khối học, môn học</a>
                </div>
            </div>

            <div class="swiper-slide rounded py-1 px-1 d-flex">
                <div class="swiper-text" type="button" id="assignment">
                    <a target="_blank" href="#">Phân công giảng dạy</a>
                </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Cấu hình chuyên môn -->
    <div class="card-header">
      <h4 class="card-title">Công cụ lên kế hoạch và quản trị kế hoạch giáo dục</h4>
    </div>
    <div class="card-content">
      <div class="card-body pt-0">
        <div class="swiper-centered-slides-2 swiper-container p-1">
          <div class="swiper-wrapper">
            
            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                
                <div class="swiper-text"><a target="_blank"href="{{ route('school.timetable.index', ['id' => $school->id])}}">Thời khoá biểu</a></div>
            </div>
            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
            
              <div class="swiper-text"><a target="_blank"href="{{ route('school.school_plan.index', ['id' => $school->id]) }}">Kế hoạch giáo dục nhà trường</a></div>
            </div>

            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              @if(count($school->regularGroups) > 0)
                <div class="swiper-text"><a target="_blank"href="{{route('school.regular_group.plan.index', ['id' => $school->id, 'rgId' => $school->regularGroups[0]->id])}}">Kế hoạch giáo dục tổ chuyên môn</a></div>
              @else
                <div class="swiper-text"><a target="_blank"href="{{route('school.regular_group.index', ['id' => $school->id])}}">Kế hoạch giáo dục tổ chuyên môn</a></div>
              @endif
            </div>

            
            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
               @if(count($school->staffs) > 0)
                <div class="swiper-text"><a target="_blank"href="{{route('school.staff.plans', ['school_id' => $school->id, 'staffId' => $school->staffs[0]->id])}}">Kế hoạch giáo dục của giáo viên</a></div>
              @else
                <div class="swiper-text"><a target="_blank"href="{{route('admin.school.view_staff_list', ['id' => $school->id])}}">Kế hoạch giáo dục tổ chuyên môn</a></div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Nộp kế hoạch. duyệt kế hoạch -->
    <!-- Cấu hình chuyên môn -->
    <div class="card-header">
      <h4 class="card-title">Nộp và duyệt kế hoạch</h4>
    </div>
    <div class="card-content">
      <div class="card-body pt-0">
        <div class="swiper-centered-slides-2 swiper-container p-1">
          <div class="swiper-wrapper">
            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
              <div class="swiper-text"><a target="_blank"href="{{ route('school.review_group_plan', ['id' => $school->id])}}">Phê duyệt kế hoạch tổ chuyên môn</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
@section('vendor-script')
        <!-- vendor files -->
        <script src="{{ asset(mix('vendors/js/extensions/swiper.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/jquery.contextMenu.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/jquery.ui.position.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection
@section('page-script')
        <!-- Page js files -->
        <script src="{{ asset(mix('js/scripts/extensions/swiper.js')) }}"></script>
        <script src="{{ asset(mix('js/scripts/extensions/context-menu.js')) }}"></script>
@endsection

@push('scripts')

<script type="text/javascript">
    $.contextMenu({
        selector: '#subject-management',
        trigger: "left",
        callback: function (key, options) {
            window.open(key, '_blank');
        },
        items: {
        "{{route('school.subject.index', ['id' => $school->id])}}": { name: "Danh sách môn học" },
        "{{route('school.subject.subject_by_grade', ['id' => $school->id])}}": { name: "Thống kê môn học theo khối" },
      }
    });
    
    $.contextMenu({
        selector: '#regular-group-management',
        trigger: "left",
        callback: function (key, options) {
            window.open(key, '_blank');
        },
        items: {
        "{{route('school.regular_group.index', ['id' => $school->id])}}": { name: "Quản lý tổ chuyên môn" },
        "{{route('school.staff.manage_teacher_grade_and_subject', ['school_id' => $school->id])}}" : {name: 'Quản lý khối học, môn học cho giáo viên'},
        "{{route('school.regular_group.assign_leader', ['id' => $school->id])}}": { name: "Phân quyền tổ trưởng, tổ phó" },
      }
    });
    
    $.contextMenu({
        selector: '#assignment',
        trigger: "left",
        callback: function (key, options) {
            window.open(key, '_blank');
        },
        items: {
        "{{route('school.teaching_assignment.homeroom_teacher', ['id' => $school->id])}}": { name: "Phân công giáo viên Chủ nhiệm" },
        "{{route('school.teaching_assignment.class_subjects', ['id' => $school->id])}}": { name: "Phân công phụ trách môn học" },
      }
    });

</script>

@endpush