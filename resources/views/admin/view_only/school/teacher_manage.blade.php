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

        <!-- Cấu hình chuyên môn -->
        <div class="card-header">
            <h4 class="card-title">Công cụ lên kế hoạch và quản trị kế hoạch giáo dục</h4>
        </div>
        <div class="card-content">
            <div class="card-body pt-0">
                <div class="swiper-centered-slides-2 swiper-container p-1">
                    <div class="swiper-wrapper">

                        <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                            <div class="swiper-text"><a target="_blank"
                                    href="{{ route('school.staff.timetable', ['school_id' => $school->id, 'staffId' => Admin::user()->staffDetail->id])}}">Thời khoá
                                    biểu</a></div>
                        </div>
                        <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">

                            <div class="swiper-text"><a target="_blank"
                                    href="{{ route('school.school_plan.index', ['id' => $school->id]) }}">Kế hoạch giáo
                                    dục nhà trường</a></div>
                        </div>

                        <div class="swiper-slide rounded py-1 px-1 d-flex">
                            <div class="swiper-text" type="button" id="rg-management">
                                <a target="_blank" href="#">Tổ chuyên môn</a>
                            </div>
                        </div>

                        <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                            <div class="swiper-text"><a target="_blank" href="{{ count($staffGroups) > 0 ? route('school.staff.plan.index', ['school_id' => $school->id, 'rgId' => $staffGroups[0]->id, 'staffId' => Admin::user()->staffDetail->id]) : route('school.staff.regular_group', ['school_id' => $school->id, 'staffId' => Admin::user()->staffDetail->id ]) }}">Kế hoạch giáo dục của giáo viên</a></div>
                        </div>

                        <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                            <div class="swiper-text"><a target="_blank" href="{{ count($staffGroups) > 0 ? route('school.staff.teacher_lesson.index', ['school_id' => $school->id, 'staffId' => Admin::user()->staffDetail->id]) : route('school.staff.regular_group', ['school_id' => $school->id, 'staffId' => Admin::user()->staffDetail->id ]) }}">Bài giảng</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($staff->manageGroup)
            <div class="card-header">
                <h4 class="card-title">Quản lý tổ chuyên môn - {{ $staff->manageGroup->regularGroup->name }}</h4>
            </div>
            <div class="card-content">
                <div class="card-body pt-0">
                    <div class="swiper-centered-slides-2 swiper-container p-1">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('school.regular_group.review_teacher_plans', ['id' => $school->id, 'rgId' => $staff->manageGroup->regular_group_id]) }}">Duyệt kế hoạch giáo dục của giáo viên</a></div>
                            </div>

                            <div class="swiper-slide rounded swiper-shadow py-1 px-1 d-flex">
                                <div class="swiper-text"><a target="_blank" href="{{ route('school.regular_group.teacher_plans', ['id' => $school->id, 'rgId' => $staff->manageGroup->regular_group_id]) }}">Kế hoạch giáo dục của giáo viên</a></div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        @endif
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
    selector: '#rg-management',
    trigger: "left",
    callback: function(key, options) {
        window.open(key, '_blank');
    },
    items: {
        "{{route('school.staff.regular_group', ['school_id' => $school->id, 'staffId' => Admin::user()->staffDetail->id ])}}": {
            name: "Danh sách tổ chuyên môn"
        },
        "{{ count($staffGroups) > 0 ? route('school.regular_group.plan.index', ['id' => $school->id, 'rgId' => $staffGroups[0]->id]) : route('school.staff.regular_group', ['school_id' => $school->id, 'staffId' => Admin::user()->staffDetail->id ])}}": {
            name: "Kế hoạch giáo dục tổ chuyên môn"
        },
    }
});
</script>

@endpush