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
        $title = 'Phân công phụ trách môn học';
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
                    
                        <div class="d-flex justify-content-between mb-1">
                            <form class="d-flex flex-wrap w-50" method="GET" action="{{ route('school.teaching_assignment.class_subjects', ['id' => $school->id]) }}">
                                <div class="mr-1 w-50">
                                    <select class="custom-select form-control required select2"
                                            name="class" data-placeholder="Chọn lớp học">
                                        @foreach($school->classes as $class)
                                            <option value="{{$class->id}}" {{ strval($class->id) === strval($selectedClass->id) ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <button class="btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                                    {{ trans('admin.apply') }}
                                </button>
                            </form>
                            
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
                                        <th scope="col">Tên lớp</th>
                                        <th scope="col">Tên môn học</th>
                                        <th scope="col">Giáo viên chỉ định</th>
                                        <th scope="col">Số tiết một tuần</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($selectedClass->classSubjects as $index => $classSubject)
                                        <tr>
                                            <input type='hidden' name='classSubjects[{{$classSubject->id}}][id]' value="{{ $classSubject->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $selectedClass->class_name }}</td>
                                            <td>{{ $classSubject->subject->name }} </td>
                                            <td>
                                                <select class='form-control' name='classSubjects[{{$classSubject->id}}][staff_id]'>
                                                    <option value=''>Chọn giáo viên</option>
                                                    @foreach($classSubject->staffSubjects as $staffSubject)
                                                        @if($staffSubject->staff && in_array($selectedClass->grade, $staffSubject->staff->staffGrades->pluck('grade')->toArray()))
                                                             <option {{ $classSubject->staff ? ($classSubject->staff->id == $staffSubject->staff->id ? 'selected' : '') : ''}} value="{{ $staffSubject->staff->id }}">{{ $staffSubject->staff->fullname }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input class='form-control' name='classSubjects[{{$classSubject->id}}][lesson_per_week]' value="{{ $classSubject->lesson_per_week }}"></td>
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
@endpush

