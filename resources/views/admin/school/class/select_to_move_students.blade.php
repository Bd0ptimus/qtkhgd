@extends('layouts.contentLayoutMaster')

@php
    $title = 'Danh sách lớp theo trường';
    $breadcrumbs = [
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
        ['name' => $title],
    ];
@endphp

@section('title', $title)

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <h2>Vui lòng tích chọn vào học sinh cần chuyển lớp, và chọn lớp cần chuyển tới sau đó nhấn Tiến Hành Chuyển Lớp</h2>
                            
                            <form method="get">
                                <div class='row'>
                                    <div class="col-md-3">
                                        <label for="class" class="control-label">Chọn lớp chuyển đi</label>
                                        <select class="form-control parent select2 filter-province" style="width: 100%;" name="class">
                                            @foreach ($school->classes as $key => $class)
                                                <option value="{{ $class->id }}"
                                                        @if($class->id == $selectedClass->id) selected @endif>
                                                    {!! $class->class_name !!}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3" style="margin-top:20px">
                                        <button type="submit" class="btn btn-flat btn-success" >
                                            Lấy DS học sinh
                                        </button>
                                    </div>
                                </div>
                            </form>
                           
                            <form method="post" id="transferStudentForm">
                                
                                <div class='row'>
                                    @csrf
                                    <input name="sourceClass" value="{{ $selectedClass->id}}" type="hidden">
                                    <div class="form-group col-md-3">
                                        <label for="date" class="control-label">Ngày chuyển lớp</label>
                                        <input required type="date" name="date" class="form-control filter-date" placeholder="">
                                    </div>
                                    <div class="col-md-3">
                                    <label for="targetClass" class="control-label">Lớp chuyển đến</label>
                                        <select class="form-control parent select2 filter-province" style="width: 100%;" name="targetClass">
                                            @foreach ($targetClasses as $key => $targetClass)
                                                <option value="{{ $targetClass->id }}">
                                                    {!! $targetClass->class_name !!}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3" style="margin-top:20px">
                                        <button type="submit" class="btn btn-flat btn-success" >
                                            Tiến hành chuyển lớp
                                        </button>
                                    </div>
                                </div>
                            
                                <div class="table-responsive">
                                    <table id="data-table" class="table nowrap">
                                        <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Họ và tên</th>
                                            <th>Ngày tháng năm sinh</th>
                                            <th>Giới tính</th>
                                            <th>Địa chỉ</th>
                                            <th>Lớp</th>
                                            <th><input class="checkall" type="checkbox"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($selectedClass->students as $key => $student)
                                            <tr>
                                                <th scope="row">{{ $key + 1}}</th>
                                                <td>{{ $student->fullname}}</td>
                                                <td>{{ $student->dob}}</td>
                                                <td>{{ $student->gender}}</td>
                                                <td>{{ $student->address}}</td>
                                                <td>{{ $selectedClass->class_name}}</td>
                                                <td><input name="students[{{ $student->id}}]" type="checkbox"></td>
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
            $('.select2').select2()
        });
    </script>

    <script>
        $('.checkall').on('change', function(e){
            var checked = ($(this).prop('checked'));
            $('input[type=checkbox]').prop('checked', checked);
        });
    </script>
@endsection
