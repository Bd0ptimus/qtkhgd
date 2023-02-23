@extends('layouts/contentLayoutMaster')

@php
    $title = 'Quản lý bảo hiểm y tế';
    $breadcrumbs = [
        ['name' => $title],
    ];
    $tableHeadings = [
        ['headerName' => 'STT', 'field' => 'stt', "pinned"=> "left", "width" => 70],
        ['headerName' => 'Mã trường', 'field' => 'school_code', "pinned"=> "left", "width" => 120, "cellStyle" => [  "font-weight" => "bold"]],
        ['headerName' => 'Tên trường', 'field' => 'school_name', "pinned"=> "left", "cellStyle" => [  "font-weight" => "bold"]],
        ['headerName' => 'Cấp', 'field' => 'school_type'],
        ['headerName' => 'Số học sinh', 'field' => 'total_student', "width" => 140],
        ['headerName' => 'Tổng học sinh đóng bảo hiểm y tế', 'field' => 'total_insurance', "width" => 300],
        ['headerName' => 'BHYT Tự nguyện', 'field' => 'total_bh_tunguyen'],
        ['headerName' => 'BHYT Diện chính sách', 'field' => 'total_bh_chinhsach'],
    ];
    $query_string = (str_replace(Request::url(), '', Request::fullUrl()));
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
                                </div>
                            </div>
                            <div class="btn-group pull-right " style="margin-right: 5px">
                                <div class="btn">
                                    <a type="button" id="" name=''
                                       class="btn btn-flat btn-warning"
                                       href="{{ route('district.export_manage_insurance').$query_string }}">
                                        Xuất excel
                                    </a>
                                </div>
                            </div>
                            <div class="form-group   {{ $errors->has('pay_type') ? ' has-error' : '' }}">
                                <div class='row'>
                                    <div class="col-sm-3">
                                        <select disabled class="form-control parent select2 filter-province"
                                                style="width: 100%;"
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
                                    <div class="col-sm-3">
                                        <select {{ Admin::user()->inRoles(['tuyen-ttyt-district', 'phong-gd']) ? 'disabled' : '' }} class="form-control parent select2 filter-district"
                                                style="width: 100%;"
                                                name="district">
                                            <option value=""><a href="#">Tất cả</a></option>
                                            @foreach ($districts as $key => $district)
                                                <option value="{{ $district->id }}"
                                                        @if($district->id == $districtId) selected @endif>{!! $district->name !!}</option>
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
                $data['total_student'] = $school->students->count();
                $data['total_insurance'] = $school->total_insurance;
                $data['total_bh_tunguyen'] = $school->total_bh_tunguyen;
                $data['total_bh_chinhsach'] = $school->total_bh_chinhsach;
                $data['action'] = [
                    'school' => route('admin.school.user_activity',['school_id' => $school->id]),
                    'route_edit' => route('admin.agency.districts.edit_school',['id' => $school->district_id, 'school_id' => $school->id]),
                    'id' => $school->id
                ];
                $rowData[] = $data;
            }
        @endphp

        var columnDefs = @json($tableHeadings);


        var gridOptions = {
            columnDefs: columnDefs,
            rowData: @json($rowData),
            pagination: true,
            paginationPageSize: 10,
            domLayout: 'autoHeight',
            getRowNodeId: d => {
                return d.id;
            },
            pinnedBottomRowData: [{
                school_name: 'Tổng số liệu',
                total_student: {{ $total_student }},
                total_insurance: {{ $total_insurance }},
                total_bh_tunguyen:{{ $total_bh_tunguyen }},
                total_bh_chinhsach:{{ $total_bh_chinhsach }},
            }],
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
                return false;
                var rowNode = gridOptions.api.getRowNode(($(this).data('id')));
                e.preventDefault();
                const id = $(this).data('id');
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
            let newUrl = window.location.href.substring(0, window.location.href.indexOf('?'));
            let optionSelected = $(this).find("option:selected");
            let valueSelected = optionSelected.val();
            if (valueSelected == '') window.location.href = newUrl;
            let searchParams = new URLSearchParams(window.location.search)
            if (searchParams.has('provinceId')) {
                window.location.href = newUrl + `?provinceId=${valueSelected}`;
            } else {
                window.location.href = window.location.href + `?provinceId=${valueSelected}`;
            }
        });

        $('.filter-district').change(function () {
            let newUrl = window.location.href.substring(0, window.location.href.indexOf('&'));
            let optionSelected = $(this).find("option:selected");
            let valueSelected = optionSelected.val();
            if (valueSelected == '') window.location.href = newUrl;
            let searchParams = new URLSearchParams(window.location.search)
            if (searchParams.has('districtId')) {
                window.location.href = newUrl + `&districtId=${valueSelected}`;
            } else {
                window.location.href = window.location.href + `&districtId=${valueSelected}`;
            }
        });
    </script>
@endsection

