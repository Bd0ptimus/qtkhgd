@extends('layouts/contentLayoutMaster')

@php
    $tableHeadings = [
        ['headerName' => 'STT', 'field' => 'stt', "pinned"=> "left", "width" => 70],
        ['headerName' => 'Mã Học sinh', 'field' => 'student_code', "pinned"=> "left", "width" => 160],
        ['headerName' => 'Họ tên', 'field' => 'fullname', "pinned"=> "left", "width" => 150],
        ['headerName' => 'Lớp', 'field' => 'class', "pinned"=> "left", "width" => 100],
        ['headerName' => 'Ngày sinh', 'field' => 'dob'],
        ['headerName' => 'Giơi tính', 'field' => 'gender'],
        ['headerName' => 'Dân tộc', 'field' => 'ethnic'],
        ['headerName' => 'Tôn giáo', 'field' => 'religion'],
        ['headerName' => 'Quốc tịch', 'field' => 'nationality'],
        ['headerName' => 'Địa chỉ', 'field' => 'address'],
    ];
@endphp

@section('title', $title)
@section('vendor-style')
    {{-- Vendor Css files --}}
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/ag-grid/ag-grid.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/ag-grid/ag-theme-material.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/aggrid.css')) }}">
@endsection
<style type="text/css">
    .select2-container {
        width: 100% !important;
    }
</style>
@section('main')
    {{-- Statistics card section start --}}
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex justify-content-between mb-1">
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle"
                                            type="button"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        Tùy biến lưới
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <ul class="list-unstyled list-scrollable m-0">
                                            @foreach($tableHeadings as $tableHeading)
                                                <li>
                                                    <fieldset>
                                                        <div class="dropdown-item vs-checkbox-con vs-checkbox-primary">
                                                            <input type="checkbox" checked
                                                                   class="toggle-aggrid-vis"
                                                                   data-column="{{$tableHeading['field']}}">
                                                            <span class="vs-checkbox">
                                                            <span class="vs-checkbox--check"><i
                                                                        class="vs-icon feather icon-check m-0"></i></span>
                                                            </span>
                                                            <span class="">{{$tableHeading['headerName']}}</span>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-primary waves-effect waves-light" type="button"><i
                                                        class="feather icon-search"></i></button>
                                        </div>
                                        <input type="text" class="form-control"
                                               placeholder="{{ trans('admin.search') }}"
                                               oninput="onFilterTextBoxChanged(this)"
                                               aria-label="Amount">
                                    </div>
                                    <div class="text-nowrap ml-1">
                                        <a type="button"
                                           class="btn btn-flat btn-success"
                                           href="{{ route('admin.school.create_student', ['school_id' => $school->id]) }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            Thêm học sinh
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <form class="d-flex flex-wrap w-50" method="GET"
                                      action="{{ route('admin.school.view_student_list', ['id' => $school->id]) }}">
                                    <div class="mr-1 w-50">
                                        <select class="custom-select form-control required select2"
                                                name="class" data-placeholder="Chọn lớp học">
                                            <option value="all">Tất cả</option>
                                            @foreach($school->classes as $class)
                                                <option value="{{$class->id}}" {{ strval($class->id) === strval($request_class) ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <button class="btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                                        {{ trans('admin.apply') }}
                                    </button>
                                </form>
                                <div class="text-nowrap ml-1">
                                    <a type="button"
                                       class="btn btn-flat btn-warning"
                                       href="{{ route('admin.school.export_student', array_merge(['id' => $school->id], request()->query())) }}">
                                        Xuất excel
                                    </a>
                                    <a type="button"
                                       class="btn btn-flat btn-success ml-1"
                                       href="{{ route('admin.school.import_student', ['id' => $school->id]) }}">
                                        Import excel
                                    </a>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex flex-wrap">
                                        <button class="btn btn-danger ag-grid-export-btn waves-effect waves-light mr-1 btn-delete-multiple">
                                            {{ trans('admin.delete') }}
                                        </button>
                                        @if(Admin::user()->isAdministrator() && !empty($request_class))
                                            <button data-url="{{ route('admin.student.delete_all_by_class', ['id' => $school->id, 'class_id' => $request_class ]) }}" id="deleteAllStudents" class="btn btn-danger ag-grid-export-btn waves-effect waves-light mr-1 btn-delete-multiple">
                                                Xoá tất cả học sinh
                                            </button>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-end w-50">
                                        <div class="mr-1 w-50">
                                            <select class="custom-select form-control required select2"
                                                    name="class_transfer" data-placeholder="Chọn lớp">
                                                <option></option>
                                                @foreach($school->classes as $class)
                                                    <option value="{{$class->id}}" {{ strval($class->id) === strval($request_class) ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button class="btn btn-primary ag-grid-export-btn waves-effect waves-light"
                                                id="btn-edit-class">
                                            Chuyển Lớp
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-block">
                                <div class="ag-grid-page-size">
                                    <span>Page size:</span>
                                    <select id="aggrid-page-size">
                                        <option value="10" selected>10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                    </select>
                                </div>
                                <div id="grid-data" class="aggrid ag-theme-material"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- // Statistics Card section end--}}
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/ag-grid/ag-grid-community.min.noStyle.js')) }}"></script>

@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{ asset('js/helper.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2({
                allowClear: true,
            })
        });
    </script>

    <script>
        @php
            $rowData = [];
            foreach($school->students as $index => $student) {
                $data = [];
                $data['id'] = $student->id;
                $data['stt'] = $index + 1;
                $data['student_code'] = $student->student_code;
                $data['fullname'] = $student->fullname;
                $data['class'] = !empty($student->class) ? $student->class->class_name : ($student->class_id == 0 ? 'Tốt Nghiệp' : null) ;
                $data['dob'] = $student->dob;
                $data['gender'] = $student->gender;
                $data['ethnic'] = $student->ethnic;
                $data['religion'] = $student->religion;
                $data['nationality'] = $student->nationality;
                $data['address'] = $student->address;
                $data['action'] = [
                    'route_view' => route('admin.student.view',['id' => $student->id]),
                    'route_edit' => route('admin.student.edit',['id' => $student->id]),
                    'id' => $student->id
                ];
                $rowData[] = $data;
            }
        @endphp

        var columnDefs = @json($tableHeadings);
        columnDefs.push({
            headerName: "Thao tác",
            field: "action",
            pinned: 'right',
            width: 170,
            cellRenderer: function (params) {
                return '<a type="button" id="viewStudent"  href="' + params.value.route_view + '" class="btn btn-flat btn-success waves-effect waves-light mr-1"><span title="Xem thông tin"><i class="fa fa-eye" aria-hidden="true"></i></span></a>' +
                    '<a type="button"  href="' + params.value.route_edit + '" class="btn btn-flat btn-success waves-effect waves-light mr-1"><span title="Sửa"><i  class="fa fa-edit" aria-hidden="true"></i></span></a>' +
                    '<a type="button" class="btn-delete-student btn btn-flat btn-danger waves-effect waves-light" href="#" data-id="' + params.value.id + '"><span title="Xóa"><i  class="fa fa-trash" aria-hidden="true"></i></span></a>'
            }
        })

        columnDefs = columnDefs.map((columnDef) => {
            if (['student_code', 'fullname', 'class'].includes(columnDef.field)) {
                columnDef.cellRenderer = function (params) {
                    return `<strong>${params.value}</strong>`;
                }
            }

            return columnDef;
        });

        var gridOptions = {
            columnDefs: columnDefs,
            rowData: @json($rowData),
            pagination: true,
            paginationPageSize: 10,
            domLayout: 'autoHeight',
            rowSelection: 'multiple',
            rowMultiSelectWithClick: true,
            getRowNodeId: d => {
                return d.id;
            },
            defaultColDef: {
                wrapText: true,
                autoHeight: true,
                sortable: true,
                unSortIcon: true
            },
        }

        function onFilterTextBoxChanged(e) {
            gridOptions.api.setQuickFilter(e.value);
        }

        $(document).on('change', '#aggrid-page-size', function () {
            gridOptions.api.paginationSetPageSize(Number(this.value));
        })

        $(document).ready(function () {
            var gridDiv = document.querySelector('#grid-data');
            new agGrid.Grid(gridDiv, gridOptions);

            $(document).on('click', '.toggle-aggrid-vis', function () {
                // Toggle the visibility
                gridOptions.columnApi.setColumnVisible($(this).attr('data-column'), $(this).prop('checked'));
            });

            $(document).on('click', '.btn-delete-student', function (e) {
                e.preventDefault();
                var rowNode = gridOptions.api.getRowNode(($(this).data('id')));
                deleteItems(gridOptions, [rowNode], '{{ route('admin.student.delete') }}');
            });

            $(document).on('click', '.btn-delete-multiple', function (e) {
                e.preventDefault();
                var selectedRows = gridOptions.api.getSelectedNodes();
                deleteItems(gridOptions, selectedRows, '{{ route('admin.student.delete') }}');
            });
        });

        $(document).on('click', '#btn-edit-class', function () {
            var classId = $('.select2[name="class_transfer"]').find(':selected').val();
            var className = $('.select2[name="class_transfer"]').find(':selected').text();
            var selectedRows = gridOptions.api.getSelectedNodes();
            var selectedNameString = '';
            var selectedStudentIds = [];
            var itemsToUpdate = [];

            if (!className) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                Toast.fire({
                    type: 'error',
                    title: 'Xin hãy lớp!!'
                })
            } else if (selectedRows.length <= 0) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                Toast.fire({
                    type: 'error',
                    title: 'Xin hãy chọn học sinh để chuyển lớp!!'
                })
            } else {
                selectedRows.forEach(function (selectedRow, index) {
                    var data = selectedRow.data;
                    if (index > 0) {
                        selectedNameString += ', ';
                    }
                    selectedNameString += data.fullname + '(' + data.student_code + ')';
                    selectedStudentIds.push(data.id);

                    data.school_branch = className;
                    itemsToUpdate.push(data);
                });

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: true,
                })
                swalWithBootstrapButtons.fire({
                    title: 'Bạn có chắc chắn cập nhật những học sinh này không?',
                    html:
                        'Những học sinh: ' + '<b>' + selectedNameString + '</b>' +
                        ' sẽ được chuyển vào điểm trường ' + className,
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Có, hãy cập nhật nó!',
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: 'Không, hủy bỏ!',
                    reverseButtons: true,

                    preConfirm: function () {
                        return new Promise(function (resolve) {
                            $.ajax({
                                method: 'post',
                                url: '{{ route('admin.school.assign_class_student') }}',
                                data: {
                                    classId,
                                    selectedStudentIds,
                                    _token: '{{ csrf_token() }}',
                                },
                                success: function (data) {
                                    if (data.error == 1) {
                                        swalWithBootstrapButtons.fire(
                                            'Cancelled',
                                            data.msg,
                                            'error'
                                        )
                                    } else {
                                        resolve(data);
                                    }

                                }
                            });
                        });
                    }

                }).then((result) => {
                    if (result.value) {
                        gridOptions.api.applyTransaction({update: itemsToUpdate});
                        swalWithBootstrapButtons.fire(
                            'Updated!',
                            'Item has been updated.',
                            'success'
                        )
                    }
                })
            }
        });

        $(document).on('click', '#deleteAllStudents', function() {
            let confirmDelete = confirm('Bạn có chắc chắn muốn xoá toàn bộ học sinh của lớp học này?');
            if(confirmDelete) {
                $.ajax({
                    method: 'POST',
                    url: $(this).data('url'),
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });
    </script>
@endsection

