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
    $title = "Phân công tổ trưởng, tổ phó - {$school->school_name}";
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
                        <div class="btn-group text-nowrap ml-1">
                            <a type="button"
                                class="btn btn-flat btn-success"
                                href="{{route('admin.school.chuanhoa', ['id' => $school->id])}}">
                                <i class="fa fa-check" aria-hidden="true"></i>
                                Chuẩn hoá dữ liệu
                            </a>
                        </div>

                        <form method="post">
                            @csrf
                            <div class="d-flex float-right">
                                <div class="text-nowrap ml-1">
                                    <button type="submit" class="btn btn-success">
                                        Lưu lại
                                    </button>
                                </div>
                            </div>
                           
                            <div class="table-responsive">
                                <table class="table zero-configuration table-bordered table-striped text-nowrap" id="table" style="border-spacing: 1px">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên tổ chuyên môn</th>
                                        <th scope="col">Tổng số thành viên</th>
                                        <th scope="col">Tổ trưởng</th>
                                        <th scope="col">Tổ phó</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($regularGroups as $index => $group)
                                        <tr>
                                            <input type='hidden' name='regularGroups[{{$group->id}}][id]' value="{{ $group->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ count($group->groupStaffs) }}</td>
                                            <td>
                                                <select class="form-control input-sm subject select2"  data-placeholder="Tổ trưởng" style="width: 100%;" name="regularGroups[{{$group->id}}][leader]" >
                                                    <option value="">Chọn tổ trưởng</option>
                                                    @foreach ($group->groupStaffs as $index => $groupStaff)
                                                        <option value="{{ $groupStaff->staff_id }}" {{ $group->leader && $groupStaff->staff_id == $group->leader->staff_id ? 'selected' : ''}}>{{ $groupStaff->staff->fullname ?? ''}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                @php
                                                    $items = [];
                                                    $oldItems = old('regularGroups[{{$group->id}}][deputies][]',$group->deputies->pluck('staff_id')->toArray());
                                                    if(is_array($oldItems)){
                                                        foreach($oldItems as $value){
                                                            $items[] = (int)$value;
                                                        }
                                                    }
                                                @endphp

                                                <select class="form-control input-sm subject select2"  multiple="multiple" data-placeholder="Danh sách các tổ phó" style="width: 100%;" name="regularGroups[{{$group->id}}][deputies][]" >
                                                    <option value="">Chọn tổ phó</option>
                                                    @foreach ($group->groupStaffs as $index => $groupStaff)
                                                        <option value="{{ $groupStaff->staff_id }}"  {{ (count($items) && in_array($groupStaff->staff_id, $items))?'selected':'' }}>{{ $groupStaff->staff->fullname ?? '' }}</option>
                                                    @endforeach
                                                </select>

                                            </td>
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

