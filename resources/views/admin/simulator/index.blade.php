@extends('layouts.contentLayoutMaster')
@php
    $grades = GRADES;
@endphp
@section('title', $title_description ?? 'Danh sách bài mô phỏng')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <style>
        .homework-sheet-content {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dataTables_filter {
            display: none;
        }
    </style>
@endsection

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body card-dashboard form-group form-filter">
                        <div class='row align-items-center'>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Cấp học</label>
                                    <select class="form-control parent select2 filter-level"
                                            style="width: 100%;"
                                            name="level">
                                        <option value="">Tất cả</option>
                                        @foreach ($schoolLevelsFilter as $key => $name)
                                            <option value="{{ $key }}" @if(isset($schoolLevelSelected) && $key == $schoolLevelSelected) selected @endif>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Khối học</label>
                                    <select class="form-control parent select2 filter-grade"
                                            style="width: 100%;"
                                            name="grade">
                                        <option value="">Tất cả</option>
                                        @foreach ($gradesFilter as $key => $grade)
                                            <option value="{{ $grade }}"  @if(isset($gradeSelected) && $grade == $gradeSelected) selected @endif>
                                                {{ mapGradeName($grade) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Môn học</label>
                                    <select class="form-control parent select2 filter-subject"
                                            style="width: 100%;"
                                            name="subject">
                                        <option value="">Tất cả</option>
                                        @foreach ($subjectsFilter as $key => $subject)
                                            <option value="{{ $subject->id }}" @if(isset($subjectSelected) && $subject->id == $subjectSelected) selected @endif>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group mb-0">
                                    <label for=""></label>
                                    <button type="button" class="btn btn-primary reset-filter">
                                        <i class="feather icon-refresh-cw"></i> Bỏ lọc
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-4 d-flex justify-content-end">
                                <div class="d-flex">
                                    <input type="text" name="search"
                                           id="search"
                                           placeholder="nhập tên bài giảng"
                                           value="{{ old('search',$search ?? '')}}"
                                           class="form-control search"/>
                                    <button type="button" style="width: 50%;" class="btn btn-primary ml-1 input-search">{{ trans('admin.search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex">
                                <div class="btn-group text-nowrap">
                                    <a type="button" @if (!Admin::user()->inRoles([ROLE_ADMIN,ROLE_CM])) hidden @endif
                                        class="main-action btn btn-flat btn-success"
                                        href="{{ route('simulator.create') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm mô phỏng
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="simulator-table">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Khối học</th>
                                        <th scope="col">Môn học</th>
                                        <th scope="col">Tên trình mô phỏng</th>
                                        <th scope="col">Bài học</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($simulators as $index => $simulator)
                                        <tr>
                                            <th scope="row">{{ $index+1}}</th>
                                            <td>
                                                @foreach ($simulator->simulatorGrades as $simulatorGrade)
                                                    <p>
                                                        {{mapGradeName($simulatorGrade->grade)}}
                                                    </p>
                                                @endforeach
                                            </td>

                                            {{-- <td>{{ mapGradeName($simulator['grade']) }}</td> --}}
                                            <td>{{ $simulator->subject->name }}</td>
                                            <td>{{ $simulator->name_simulator}}</td>
                                            <td>{{ $simulator->related_lesson}}</td>
                                            <td>
                                                <a style="margin-top: 3px" target="_blank" href='{{ route("simulator.view",['simulatorId'=>$simulator->id])}}' class="btn btn-flat btn-success btn-datatable">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem nội dung
                                                </a>

                                                @if (Admin::user()->inRoles([ROLE_ADMIN,ROLE_CM]))
                                                    <a style="margin-top: 3px" target="_blank" href='{{ route("simulator.edit",['simulatorId'=>$simulator->id])}}' class="btn btn-flat btn-success btn-datatable">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                    </a>

                                                    <a style="margin-top: 3px" target="_blank" href='{{ route('simulator.delete',['simulatorId'=>$simulator->id])}}' class="btn btn-flat btn-success btn-datatable btn-danger">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>Xóa
                                                    </a>
                                                @endif
                                               
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    
                                </table>
                                <div class="d-flex justify-content-end">
                                    {{$simulators->appends(request()->query())->links()}}
                                </div>
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
            $.fn.dataTable.ext.errMode = 'throw';

            // $('#simulator-table').DataTable({
            //     pageLength: 25,
            //     search: false
            // });

            //update parameter when change filter
            function updateParameter(urlCurrent, key, value) {
                let url = new URL(urlCurrent);
                let search_params = url.searchParams;

                // new value of "key" is set to "value"
                search_params.set(key, value);
                search_params.set('page', '1');

                // change the search property of the main url
                url.search = search_params.toString();

                return url.toString();
            }

            $('.reset-filter').click(function () {
                window.location.href = window.location.href.substring(0, window.location.href.indexOf('?'))
            });

            $('.filter-level').change(function () {
                console.log('level');
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'level', optionSelected.val())
            });

            $('.filter-grade').change(function () {
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'grade', optionSelected.val())
            });

            $('.filter-subject').change(function () {
                window.location.href = updateParameter(window.location.href, 'subjectId', $(this).val())
            });

            $('.input-search').click(function () {
                window.location.href = updateParameter(window.location.href, 'search', $('#search').val())
            });
            
            $('.dataTables_length select').change(function() {
                window.location.href = updateParameter(window.location.href, 'limit', $(this).val())
            });
            
            
        });
    </script>
@endsection
