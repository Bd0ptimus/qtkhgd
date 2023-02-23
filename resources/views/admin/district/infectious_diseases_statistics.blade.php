@extends('layouts/contentLayoutMaster')

@php
    $title = 'Báo cáo bệnh truyền nhiễm';
    $breadcrumbs = [
        ['name' => $title],
    ];
    $tableHeadings = [
        ['headerName' => 'STT', 'field' => 'stt', "pinned"=> "left", "width" => 70],
        ['headerName' => 'Mã trường', 'field' => 'school_code', "pinned"=> "left", "width" => 120, "cellStyle" => [  "font-weight" => "bold"]],
        ['headerName' => 'Tên trường', 'field' => 'school_name', "pinned"=> "left", "width" => 150, "cellStyle" => [  "font-weight" => "bold"]],
        ['headerName' => 'Cấp', 'field' => 'school_type'],
        ['headerName' => 'Địa chỉ', 'field' => 'address'],
        
        ['headerName' => 'Tiêu chảy', 'field' => 'tieuchay'],
        ['headerName' => 'Chân tay miệng', 'field' => 'chantaymieng'],
        ['headerName' => 'Sởi', 'field' => 'soi'],
        ['headerName' => 'Quai bị', 'field' => 'quaibi'],
        ['headerName' => 'Cúm', 'field' => 'cum'],
        ['headerName' => 'Rubella', 'field' => 'rubella'],
        ['headerName' => 'Số xuất huyết', 'field' => 'sotxuathuyet'],
        ['headerName' => 'Thủy đậu', 'field' => 'thuydau'],
        ['headerName' => 'Sars-cov-2', 'field' => 'sars_cov_2'],
        ['headerName' => 'Khác', 'field' => 'khac'],
        
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
                                    href="{{ route('district.infectious_diseases_statistics.export').$query_string }}">
                                        Xuất excel
                                    </a>
                                </div>
                            </div>
                            <div class="form-group   {{ $errors->has('pay_type') ? ' has-error' : '' }}">
                                <div class='row'>
                                    <div class="col-sm-3">
                                        <select disabled class="form-control parent select2 filter-province" style="width: 100%;"
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
                                        <select disabled class="form-control parent select2 filter-district" style="width: 100%;"
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
                            <div class="form-group">
                                <form method="GET" action="" class="form row">
                                    <div class="col-md-3">
                                        <label>Từ ngày</label>
                                        <input placeholder="Từ ngày" class="form-control" type="date"
                                            name="filter_start_date" value="{{ $filter_start_date ?? null }}" required/>

                                    </div><!-- Ngày bắt đầu -->

                                    <div class="col-md-3">
                                        <label>Đến ngày</label>
                                        <input placeholder="Đến ngày" class="form-control" type="date"
                                            name="filter_end_date" value="{{ $filter_end_date ?? null }}" required/>

                                    </div><!-- Ngày kết thúc -->
                                    <div class="col-md-3  nopadding-left">
                                        <label> </label>
                                        <button type="submit" class="btn btn-default form-control"><i
                                                    class="fa fa-search"></i> Tìm kiếm
                                        </button>
                                    </div>
                                </form>
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
                $data['address'] = $school->school_address;
                $diagnosis_tieuchay = 21;
                $data['tieuchay'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_tieuchay);
                $diagnosis_chantaymieng = 22;
                $data['chantaymieng'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_chantaymieng);
                $diagnosis_soi = 23;
                $data['soi'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_soi);
                $diagnosis_quaibi = 24;
                $data['quaibi'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_quaibi);
                $diagnosis_cum = 25;
                $data['cum'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_cum);
                $diagnosis_rubella = 26;
                $data['rubella'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_rubella);
                $diagnosis_sotxuathuyet = 27;
                $data['sotxuathuyet'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_sotxuathuyet);
                $diagnosis_thuydau = 28;
                $data['thuydau'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_thuydau);
                $diagnosis_sars_cov_2 = 29;
                $data['sars_cov_2'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_sars_cov_2);
                $diagnosis_khac = 30;
                $data['khac'] =  $school->countingStudentsWithInfectiousDiseases($diagnosis_khac);
                
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

