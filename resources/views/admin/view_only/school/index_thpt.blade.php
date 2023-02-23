@extends('layouts/contentLayoutMaster')

@php
    $title = 'Danh sách các đơn vị trường học';
    $breadcrumbs = [
        ['name' => $title],
    ];
    $tableHeadings = [
        ['headerName' => 'STT', 'field' => 'stt', "pinned"=> "left", "width" => 70],
        ['headerName' => 'Mã trường', 'field' => 'school_code', "pinned"=> "left", "width" => 120],
        ['headerName' => 'Tên trường', 'field' => 'school_name', "pinned"=> "left", "width" => 150],
        ['headerName' => 'Cấp', 'field' => 'school_type'],
        ['headerName' => 'Địa chỉ', 'field' => 'address'],
        ['headerName' => 'Phòng Giáo Dục', 'field' => 'district'],
        ['headerName' => 'Sở Giáo Dục', 'field' => 'province'],
        ['headerName' => 'Email', 'field' => 'school_email'],
        ['headerName' => 'Số điện thoại', 'field' => 'school_phone'],
        ['headerName' => 'Số điểm trường', 'field' => 'branches'],
        ['headerName' => 'Số lớp học', 'field' => 'classes']
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
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
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
                                </div>
                            </div>
                            <div class="form-group   {{ $errors->has('pay_type') ? ' has-error' : '' }}">
                                <div class='row'>
                                    <div class="col-sm-3">
                                        <select class="form-control parent select2 filter-province" style="width: 100%;"
                                                {{ !Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM]) ? 'disabled' : '' }}
                                                name="province">
                                            <option value=""><a href="#">Tất cả</a></option>
                                            @foreach ($provinces as $key => $province)
                                                <option value="{{ $province->id }}"
                                                        @if($province->id == $provinceId) selected @endif>
                                                    {!! $province->name !!}
                                                </option>
                                            @endforeach
                                        </select>
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
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        @php
            $rowData = [];
            foreach($schools as $index => $school) {
                $data = [];
                $data['id'] = $school->id;
                $data['has_data'] = $school->hasData();
                $data['stt'] = $index + 1;
                $data['school_code'] = $school->school_code;
                $data['school_name'] = $school->school_name;
                $data['school_type'] = $school->getSchoolType();
                $data['address'] = $school->school_address;
                $data['district'] = $school->district->name;
                $data['province'] = $school->district->province->name;
                $data['school_email'] = $school->school_email;
                $data['school_phone'] = $school->school_phone;
                $data['branches'] = count($school->branches);
                $data['classes'] = count($school->classes);
                $data['action'] = [
                    'school' => route('admin.school.manage',['id' => $school->id]),
                    'route_edit' => route('admin.province.edit_thpt_school',['id' => $school->district->province->id, 'school_id' => $school->id]),
                    'id' => $school->id
                ];
                $rowData[] = $data;
            }
        @endphp

        var columnDefs = @json($tableHeadings);
        columnDefs.push({
            headerName: "Thao tác",
            field: "action",
            pinned: 'right',
            width: 160,
            cellRenderer: function (params) {
                return '<a type="button" style="margin: 2px" class="btn btn-flat btn-info waves-effect waves-light" href="' + params.value.school + '"><span title="Quản lý trường"><i class="fa fa-bars" aria-hidden="true"></i></span></a>' +
                    '<a type="button"  style="margin: 2px" href="' + params.value.route_edit + '" class="btn btn-flat btn-success waves-effect waves-light"><span title="Sửa"><i  class="fa fa-edit" aria-hidden="true"></i></span></a>' +
                    '<a type="button" style="margin: 2px" class="btn-delete-school btn btn-flat btn-danger" href="#" data-id="' + params.value.id + '"><span title="Xóa"><i  class="fa fa-trash" aria-hidden="true"></i></span></a>'
            }
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
            enableRangeSelection: true,
            defaultColDef: {
                wrapText: true,
                autoHeight: true,
                sortable: true,
                unSortIcon: true
            },
        }

        $(document).on('change', '#aggrid-page-size', function () {
            gridOptions.api.paginationSetPageSize(Number(this.value));
        })

        function onFilterTextBoxChanged(e) {
            gridOptions.api.setQuickFilter(e.value);
        }

        $(document).ready(function () {
            var gridDiv = document.querySelector('#grid-data');
            new agGrid.Grid(gridDiv, gridOptions);

            $(document).on('click', '.toggle-aggrid-vis', function () {
                // Toggle the visibility
                gridOptions.columnApi.setColumnVisible($(this).attr('data-column'), $(this).prop('checked'));
            });

            $(document).on('click', '.btn-delete-school', function (e) {
                e.preventDefault();
                var rowNode = gridOptions.api.getRowNode(($(this).data('id')));
                console.log(rowNode.data.has_data);
                e.preventDefault();
                const id = $(this).data('id');
                console.log(id);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: true,
                })
                if (rowNode.data.has_data) {
                    swalWithBootstrapButtons.fire({
                        icon: 'error',
                        title: 'Cảnh báo',
                        text: 'Trường đang có dữ liệu. vui lòng kiểm tra lại!',
                        timer: 2000,
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    })
                } else {
                    swalWithBootstrapButtons.fire({
                        title: 'Bạn có chắc chắn muốn xoá ?',
                        text: "",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Tôi chắc chắn',
                        confirmButtonColor: "#DD6B55",
                        cancelButtonText: 'Không!',
                        reverseButtons: true,

                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                $.ajax({
                                    method: 'post',
                                    url: '{{ route("admin.school.delete_school") }}',
                                    data: {
                                        id: id,
                                        _token: '{{ csrf_token() }}',
                                    },
                                    success: function (data) {
                                        if (data.error == 1) {
                                            swalWithBootstrapButtons.fire(
                                                'Đã huỷ',
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
                            swalWithBootstrapButtons.fire(
                                'Đã xoá!',
                                'Thông tin đã bị xoá.',
                                'success'
                            );
                            location.reload()
                        }
                    })

                }

            });
        });

        $('.filter-province').change(function () {
            let optionSelected = $(this).find("option:selected");
            let url = '';
            if(optionSelected.val()) {
                url = updateParameter(window.location.href, 'provinceId', optionSelected.val());
            } else {
                // provinceIdDefault
                let provinceId = {{$provinceIdDefault}}
                url = updateParameter(window.location.href, 'provinceId', provinceId);
            }
            window.location.href = url;
        });

    </script>
@endsection

