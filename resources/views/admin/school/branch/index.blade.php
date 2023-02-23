@extends('layouts/contentLayoutMaster')

@php
    $title = 'Danh sách các điểm trường thuộc ' . $school->school_name;
    $breadcrumbs = [
        ['name' => $title],
    ];
    $tableHeadings = [
        ['headerName' => 'STT', 'field' => 'stt', "pinned"=> "left", "width" => 70],
        ['headerName' => 'Mã trường', 'field' => 'school_code', "pinned"=> "left", "width" => 120],
        ['headerName' => 'Tên điểm trường', 'field' => 'branch_name', "pinned"=> "left", "width" => 220],
        ['headerName' => 'Địa chỉ', 'field' => 'branch_address', "width" => 300],
        ['headerName' => 'Email', 'field' => 'branch_email'],
        ['headerName' => 'Số điện thoại', 'field' => 'branch_phone'],
        ['headerName' => 'Là điểm trường chính', 'field' => 'is_main_branch'],
    ];
@endphp

@section('title', $title)
@section('vendor-style')
    {{-- Vendor Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/ag-grid/ag-grid.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/ag-grid/ag-theme-material.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/aggrid.css')) }}">
@endsection
@section('main')
    {{-- Statistics card section start --}}
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
                                        <a type="button"
                                           class="main-action btn btn-flat btn-success"
                                           href="{{ route('admin.school.add_branch', ['id' => $school->id]) }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            Thêm điểm trường
                                        </a>
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
    <script src="{{ asset('js/helper.js')}}"></script>
    <script>
        @php
            $rowData = [];
            foreach($school->branches as $index => $branchSchool) {
                $data = [];
                $data['id'] = $branchSchool->id;
                $data['stt'] = $index + 1;
                $data['school_code'] = $branchSchool->school->school_code;
                $data['branch_name'] = $branchSchool->branch_name;
                $data['branch_address'] = $branchSchool->branch_address.' - '.$school->ward->name;
                $data['branch_email'] = $branchSchool->branch_email;
                $data['branch_phone'] = $branchSchool->branch_phone;
                $data['is_main_branch'] = $branchSchool->is_main_branch;
                $data['action'] = [
                    'route_edit_branch' => route('admin.school.edit_branch',['id' => $branchSchool->id]),
                    'id' => $branchSchool->id
                ];
                $rowData[] = $data;
            }
        @endphp

        var columnDefs = @json($tableHeadings);
        columnDefs.push({
            headerName: "Thao tác",
            field: "action",
            pinned: 'right',
            width: 120,
            cellRenderer: function (params) {
                return '<a type="button" class="main-action btn btn-flat btn-success waves-effect waves-light" href="' + params.value.route_edit_branch + '"><i class="fa fa-edit" aria-hidden="true"></i></a> <a type="button" class="main-action btn-delete-item btn btn-flat btn-danger waves-effect waves-light" href="#" data-id="' + params.value.id + '"><i class="fa fa-trash" aria-hidden="true"></i></a>'
            }
        })

        columnDefs = columnDefs.map((columnDef) => {
            if (columnDef.field === 'is_main_branch') {
                columnDef.cellRenderer = function (params) {
                    return !!params.value ? '<i class="feather icon-check"/>' : '<i class="feather icon-x"/>';
                }
            }

            if (columnDef.field === 'school_code' || columnDef.field === 'branch_name') {
                columnDef.cellRenderer = function (params) {
                    return `<strong>${params.value}</strong>`;
                }
            }

            return columnDef;
        })

        var gridOptions = {
            columnDefs: columnDefs,
            rowData: @json($rowData),
            pagination: true,
            paginationPageSize: 10,
            domLayout: 'autoHeight',
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

            $(document).on('click', '.btn-delete-item', function (e) {
                if (checkIfAccountIsDemo()) return false;
                e.preventDefault();
                var rowNode = gridOptions.api.getRowNode(($(this).data('id')));
                deleteItems(gridOptions, [rowNode], '{{ route('admin.school.delete_branch') }}');
            });
        });
    </script>
@endsection

