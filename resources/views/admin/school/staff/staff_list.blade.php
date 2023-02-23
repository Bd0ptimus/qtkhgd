@extends('layouts.contentLayoutMaster')

@php
    $tableHeadings = [
        ['headerName' => 'STT', 'field' => 'stt', "pinned"=> "left", "width" => 70],
        ['headerName' => 'Điểm trường', 'field' => 'school_branch'],
        ['headerName' => 'Mã Nhân Viên', 'field' => 'staff_code', "pinned"=> "left", "width" => 120],
        ['headerName' => 'Tên', 'field' => 'fullname', "pinned"=> "left", "width" => 200],
        ['headerName' => 'Ngày sinh', 'field' => 'dob'],
        ['headerName' => 'Giới tính', 'field' => 'gender'],
        ['headerName' => 'Dân tộc', 'field' => 'ethnic'],
        ['headerName' => 'Tôn giáo', 'field' => 'religion'],
        ['headerName' => 'Quốc tịch', 'field' => 'nationality'],
        ['headerName' => trans('admin.address'), 'field' => 'address'],
        ['headerName' => 'CMND', 'field' => 'identity_card'],
        ['headerName' => trans('admin.phone'), 'field' => 'phone_number'],
        ['headerName' => trans('admin.email'), 'field' => 'email'],
        ['headerName' => 'Trình độ chuyên môn', 'field' => 'qualification'],
        ['headerName' => 'Chức vụ', 'field' => 'position'],
        ['headerName' => 'Trạng thái làm việc', 'field' => 'status']
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

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex justify-content-between mb-1">
                                <div class="btn-group">
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
                                    <div class="btn-group text-nowrap ml-1">
                                        <a type="button" id="create_category" name='create_category'
                                           class="main-action btn btn-flat btn-success"
                                           href="{{ route('admin.school.import_staff', ['id' => $school->id]) }}">
                                            Import
                                        </a>
                                    </div>
                                    <div class="btn-group text-nowrap ml-1">
                                        <a type="button" id="create_category" name='create_category'
                                           class="btn btn-flat btn-warning"
                                           href="{{ route('admin.school.export_staffs', array_merge(['id' => $school->id], request()->query())) }}">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                            Xuất excel
                                        </a>
                                    </div>
                                    <div class="btn-group text-nowrap ml-1">
                                        <a type="button" id="create_category" name='create_category'
                                           class="main-action btn btn-flat btn-success"
                                           href="{{ route('admin.school.add_staff', ['id' => $school->id]) }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            Thêm nhân viên
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <form class="d-flex flex-wrap mb-1" method="GET"
                                  action="{{ route('admin.school.view_staff_list', ['id' => $school->id]) }}">
                                <div class="mr-1">
                                    <select class="custom-select form-control required select2"
                                            name="position" data-placeholder="Chọn vị trí">
                                        <option></option>
                                        @foreach($data['position'] as $value => $label)
                                            <option value="{{$value}}" {{ strval($value) === strval($position) ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="main-action btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                                    {{ trans('admin.apply') }}
                                </button>
                            </form>
                            <div class="mb-1">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex flex-wrap">
                                        <button class="main-action btn btn-danger ag-grid-export-btn waves-effect waves-light mr-1 btn-delete-multiple">
                                            {{ trans('admin.delete') }}
                                        </button>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-end w-50">
                                        <div class="mr-1 w-50">
                                            <select class="custom-select form-control required select2"
                                                    name="branch" data-placeholder="Chọn điểm trường">
                                                <option></option>
                                                @foreach($school->branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button class="main-action btn btn-primary ag-grid-export-btn waves-effect waves-light"
                                                id="btn-edit-branch">
                                            Chuyển DT
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

                {{-- Modal --}}
                <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel33" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel33">Chọn lớp</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <label>Chọn lớp quản lý</label>
                                    <div class="form-group">
                                        <input type="hidden" name="staff_id"/>
                                        <select required class="form-control" name='class_id'>
                                            @foreach($school->classes as $class)
                                                <option value="{{$class->id}}">{{ $class->class_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Giao quyền quản lý</button>
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
    <script src="{{ asset(mix('vendors/js/tables/ag-grid/ag-grid-community.min.noStyle.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{ asset('js/helper.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2({
                allowClear: true
            })
        });
    </script>

    <script>

        @php
            $rowData = [];
            foreach($school->staffs as $index => $staff) {
                $data = [];
                $data['id'] = $staff->id;
                $data['stt'] = $index + 1;
                $data['school_branch'] = $staff->schoolBranch ? $staff->schoolBranch->branch_name : '';
                $data['staff_code'] = $staff->staff_code;
                $data['fullname'] = $staff->fullname;
                $data['dob'] = $staff->dob;
                $data['gender'] = $staff->gender;
                $data['ethnic'] = $staff->ethnic;
                $data['religion'] = $staff->religion;
                $data['nationality'] = $staff->nationality;
                $data['address'] = $staff->address;
                $data['identity_card'] = $staff->identity_card;
                $data['phone_number'] = $staff->phone_number;
                $data['email'] = $staff->email;
                $data['qualification'] = $staff->qualification;
                $data['position'] = $staff->position;
                $data['responsible'] = $staff->responsible;
                $data['concurrently'] = $staff->concurrently;
                $data['professional_certificate'] = $staff->professional_certificate;
                $data['status'] = $staff->status;
                $data['action'] = [
                    'route_view' => route('admin.staff.view', ['id' => $staff->id]),
                    'route_edit' => route('admin.school.edit_staff', ['id' => $staff->id]),
                    'id' => $staff->id,
                    'position' => $staff->getOriginal('position')
                ];
                $rowData[] = $data;
            }
        @endphp

        var columnDefs = @json($tableHeadings);
        columnDefs.push({
            headerName: "Thao tác",
            field: "action",
            pinned: 'right',
            width: 200,
            cellRenderer: function (params) {
                let assignClass = (params.value.position == 3) ? '<a type="button" class="btn btn-flat btn-warning waves-effect waves-light assign-class mr-25" data-toggle="modal" data-target="#inlineForm" data-id="' + params.value.id + '"><i class="fa fa-plus" aria-hidden="true"></i></a>' : "";
                return '<a type="button" class="btn btn-flat btn-info waves-effect waves-light mr-25" href="' + params.value.route_view + '"><i class="fa fa-eye" aria-hidden="true"></i></a>'
                    + '<a type="button" class="btn btn-flat btn-success waves-effect waves-light mr-25" href="' + params.value.route_edit + '"><i class="fa fa-edit" aria-hidden="true"></i></a>'
                    + '<a type="button" class="btn-delete-item btn btn-flat btn-danger waves-effect waves-light" href="#" data-id="' + params.value.id + '"><i class="fa fa-trash" aria-hidden="true"></i></a>'
            }
        })

        columnDefs = columnDefs.map((columnDef) => {
            if (['responsible', 'concurrently', 'professional_certificate'].includes(columnDef.field)) {
                columnDef.cellRenderer = function (params) {
                    return !!params.value ? '<i class="feather icon-check"/>' : '<i class="feather icon-x"/>';
                }
            }

            return columnDef;
        });

        var gridOptions = {
            columnDefs: columnDefs,
            rowData: @json($rowData),
            pagination: true,
            paginationPageSize: 10,
            getRowNodeId: d => {
                return d.id;
            },
            domLayout: 'autoHeight',
            rowSelection: 'multiple',
            rowMultiSelectWithClick: true,
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

            $(document).on('click', '#btn-edit-branch', function () {
                var branchId = $('.select2[name="branch"]').find(':selected').val();
                var branchName = $('.select2[name="branch"]').find(':selected').text();
                var selectedRows = gridOptions.api.getSelectedNodes();
                var selectedNameString = '';
                var selectedStaffIds = [];
                var itemsToUpdate = [];

                if (!branchName) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    Toast.fire({
                        type: 'error',
                        title: 'Xin hãy chọn điểm trường!!'
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
                        title: 'Xin hãy chọn nhân viên để chuyển điểm trường!!'
                    })
                } else {
                    selectedRows.forEach(function (selectedRow, index) {
                        var data = selectedRow.data;
                        if (index > 0) {
                            selectedNameString += ', ';
                        }
                        selectedNameString += data.fullname + '(' + data.staff_code + ')';
                        selectedStaffIds.push(data.id);

                        data.school_branch = branchName;
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
                        title: 'Are you sure to update these item ?',
                        html:
                            'Những nhân viên: ' + '<b>' + selectedNameString + '</b>' +
                            ' sẽ được chuyển vào điểm trường ' + branchName,
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update it!',
                        confirmButtonColor: "#DD6B55",
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true,

                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                $.ajax({
                                    method: 'post',
                                    url: '{{ route('admin.school.staff.assign_branch') }}',
                                    data: {
                                        branchId,
                                        selectedStaffIds,
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

            $(document).on('click', '.btn-delete-item', function (e) {
                if (checkIfAccountIsDemo()) return false;
                e.preventDefault();
                var rowNode = gridOptions.api.getRowNode(($(this).data('id')));
                deleteItems(gridOptions, [rowNode], '{{ route('admin.school.delete_staff') }}');
            });

            $(document).on('click', '.btn-delete-multiple', function (e) {
                if (checkIfAccountIsDemo()) return false;
                e.preventDefault();
                var selectedRows = gridOptions.api.getSelectedNodes();
                deleteItems(gridOptions, selectedRows, '{{ route('admin.school.delete_staff') }}');
            });

            $(document).on('click', '.assign-class', function (e) {
                e.preventDefault();
                $('#inlineForm input[name="staff_id"]').val($(this).data('id'));
                $('#inlineForm').modal("show");
            });
        });
    </script>
@endsection
