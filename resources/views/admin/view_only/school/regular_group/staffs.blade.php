@extends('layouts/contentLayoutMaster')

@section('page-style')
    {{-- Page Css files --}}
    <link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.18.1/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.min.css" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.18.1/dist/extensions/group-by-v2/bootstrap-table-group-by.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <style>
        .mr10 {margin-right: 10px;}
    </style>
@endsection

@section('main')

@php 
    $title = "Danh sách thành viên tổ chuyên môn: {$regularGroup->name}";
    $breadcrumbs = [
        ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
        ['name' => $title],
    ];
@endphp 
<section id="dropdown-with-outline-btn">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <form method="post">
                            <div class="table-responsive">
                                <table class="table zero-configuration table-bordered table-striped text-nowrap" id="table" style="border-spacing: 1px">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên tổ chuyên môn</th>
                                        <th scope="col">Tên giáo viên</th>
                                        <th scope="col">Vai trò</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($regularGroup->groupStaffs as $index => $groupStaff)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $regularGroup->name }}</td>
                                            <td>{{ $groupStaff->staff->fullname ?? '' }}</td>
                                            <td>{{ GROUP_ROLES[$groupStaff->member_role] }}</td>
                                            <td><a href="{{ route('school.staff.plan.index', ['school_id' => $school->id, 'staffId'  => $groupStaff->staff->id, 'rgId' => $regularGroup->id]) }}">Xem kế hoạch bài giảng</a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page-script')
    {{-- vendor files --}}
    <script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.18.1/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.18.1/dist/extensions/group-by-v2/bootstrap-table-group-by.min.js"></script>
    <script src="{{ asset(mix('js/scripts/modal/components-modal.js')) }}"></script>
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
@endsection

@push('scripts')
<script src="{{ asset(mix('js/scripts/popover/popover.js')) }}"></script>
<script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2()
    });
</script>
@endpush

