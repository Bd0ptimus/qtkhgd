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


<section id="dropdown-with-outline-btn">
    <div class="row">
        <div class="col-sm-12">
    
            
            <div class="card">
                <div class="card-content">
                    <div class="card-body card-dashboard">
                    
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
                                        <th scope="col">Giáo viên chủ nhiệm</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($school->classes as $index => $class)
                                        <tr>
                                            <input type='hidden' name='classes[{{$class->id}}][id]' value="{{ $class->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $class->class_name }}</td>
                                            <td>
                                                <select class="form-control col-md-6" name='classes[{{$class->id}}][homeroom_teacher]'>
                                                    <option>Chọn giáo viên</option>
                                                    @foreach($school->staffs as $staff)
                                                        @if($staff && in_array($class->grade, $staff->staffGrades->pluck('grade')->toArray()))
                                                            <option {{ $class->homeroom_teacher == $staff->id ? 'selected' : ''}} value="{{ $staff->id }}">{{ $staff->fullname }}</option>
                                                        @endif
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
@endpush

