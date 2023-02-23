@extends('layouts/contentLayoutMaster')

@php
    $title = 'Danh sách Nhiệm vụ';
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => $title],
    ];

    $taskPriority = PRIORITY;

    $priorityValue = PRIORITY_VALUE;

    $tableHeadings = [
        ['headerName' => 'STT', 'field' => 'stt', "pinned"=> "left", "width" => 70],
        ['headerName' => 'Mã ID', 'field' => 'id', "pinned"=> "left","width" => 120,   "cellStyle" => [  "font-weight" => "bold"]],
        ['headerName' => 'Tiêu đề', 'field' => 'title', "pinned"=> "left", "width" => 150, "cellStyle" => ["font-weight" => "bold"]],
        ['headerName' => 'Sự ưu tiên', 'field' => 'priority'],
        ['headerName' => 'Danh mục', 'field' => 'check_list'],
        ['headerName' => 'Ngày bắt đầu', 'field' => 'start_date'],
        ['headerName' => 'Hạn hoàn thành', 'field' => 'due_date'],
        ['headerName' => 'Trạng thái', 'field' => 'status'],
        ['headerName' => 'Nguời làm', 'field' => 'assignee'],
        ['headerName' => 'Nguời tạo', 'field' => 'creator_id'],
        ['headerName' => 'Nguời giám sát', 'field' => 'follower'],
        ['headerName' => 'Ngày tạo', 'field' => 'created_at'],
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
    <section id="horizontal-vertical" class="task-manager">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex justify-content-between mb-1">
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle mr-1"
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

                                        <a href="{{route('tasks.create')}}" class="main-action btn btn-outline-primary">
                                            <i class="feather icon-plus"></i> Tạo mới
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-primary waves-effect waves-light" type="button">
                                                <i class="feather icon-search"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control"
                                               placeholder="{{ trans('admin.search') }}"
                                               oninput="onFilterTextBoxChanged(this)"
                                               aria-label="Amount">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group form-filter">
                                <div class='row'>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="formGroupExampleInput2">Trạng thái</label>
                                            <select class="form-control parent select2 filter-status"
                                                    style="width: 100%;"
                                                    name="statusId">
                                                <option value=""><a href="#">Tất cả</a></option>
                                                @foreach ($status as $key => $item)
                                                    <option value="{{ $item->id }}"
                                                            @if($item->id == $statusId) selected @endif>
                                                        {!! $item->title !!}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="formGroupExampleInput2">Sự ưu tiên</label>
                                            <select class="form-control parent select2 filter-priority"
                                                    style="width: 100%;"
                                                    name="priorityId">
                                                <option value=""><a href="#">Tất cả</a></option>
                                                @foreach ($taskPriority as $key => $item)
                                                    <option value="{{ $item }}"
                                                            @if($item == $priorityId) selected @endif>{!! $key !!}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Ngày bắt đầu</label>
                                            <input class="form-control filter-start-date" type="text"
                                                   name="startDate" id="filter-start-date"
                                                   value="{{ old('startDate', $startDate) }}"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Hạn hoàn thành</label>
                                            <input class="form-control filter-due-date" type="text"
                                                   name="dueDate" id="filter-due-date"
                                                   value="{{  old('dueDate', $dueDate) }}"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Ngày tạo</label>
                                            <input class="form-control filter-created-date" type="text"
                                                   name="created" id="filter-created-date"
                                                   value="{{ old('created', $created) }}"/>
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
                                </div>
                            </div>
                            <hr>
                            <div class="d-block">
                                <div class="ag-grid-page-size">
                                    <b>Kích thước trang:</b>
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
        //config select2
        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script type="text/javascript">
        @php
            $rowData = [];
            foreach($tasks as $index => $task) {
                $data = [];
                $data['stt'] = $index + 1;
                $data['id'] = $task->id;
                $data['title'] = $task->title;
                $data['priority'] = $priorityValue[$task->priority];
                $data['start_date'] = $task->start_date;
                $data['due_date'] = $task->due_date;
                $data['status'] = @$task->currentStatus->title;
                $data['check_list'] = collect($task->checklists)->implode('title', ', ');
                $data['assignee'] = collect($task->assigned)->implode('name', ', ');
                $data['creator_id'] = $task->creator->name;
                $data['follower'] = collect($task->followers)->implode('name', ', ');
                $data['created_at'] = $task->created_at;
                $data['action'] = [
                    'route_show' => route('tasks.show',['id' => $task->id]),
                    'id' => $task->id,
                    'user_id' => $user_id,
                    'is_admin' => $isAdmin,
                    'creator_id' => $task->creator_id,
                ];
                $rowData[] = $data;
            }
        @endphp

        var columnDefs = @json($tableHeadings);
        columnDefs.push({
            headerName: "Thao tác",
            field: "action",
            pinned: 'right',
            width: 150,
            cellRenderer: function (params) {
                let show = '<a type="button" href="' + params.value.route_show + '" style="margin: 2px" class="btn btn-flat btn-info waves-effect waves-light"><span title="Chi tiết"><i class="fa fa-eye" aria-hidden="true"></i></span></a>';
                let del = '<a type="button" style="margin: 2px" class="main-action btn-delete-school btn btn-flat btn-danger" href="#" data-id="' + params.value.id + '"><span title="Xóa"><i  class="fa fa-trash" aria-hidden="true"></i></span></a>';
                return !params.value.is_admin ? ((params.value.user_id == params.value.creator_id) ? (show + del) : show) : (show + del);
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

        //update parameter when change filter
        function updateParameter(urlCurrent, key, value) {
            let url = new URL(urlCurrent);
            let search_params = url.searchParams;

            // new value of "key" is set to "value"
            search_params.set(key, value);

            // change the search property of the main url
            url.search = search_params.toString();

            return url.toString();
        }

        $(document).ready(function () {
            var gridDiv = document.querySelector('#grid-data');
            new agGrid.Grid(gridDiv, gridOptions);
            
            $(document).on('click', '.toggle-aggrid-vis', function () {
                // Toggle the visibility
                gridOptions.columnApi.setColumnVisible($(this).attr('data-column'), $(this).prop('checked'));
            });

            $(document).on('click', '.btn-delete-school', function (e) {
                if (checkIfAccountIsDemo()) return false;
                e.preventDefault();
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
                                    method: 'POST',
                                    url: '{{ route("tasks.index") }}/' + id,
                                    data: {
                                        id: id,
                                        _token: '{{ csrf_token() }}',
                                    },
                                    success: function (data) {
                                        if (data.error == 1) {
                                            swalWithBootstrapButtons.fire('Đã huỷ', data.msg, 'error')
                                        } else {
                                            resolve(data);
                                        }
                                    }
                                });
                            });
                        }
                    }).then((result) => {
                        if (result.value) {
                            swalWithBootstrapButtons.fire('Đã xoá!', 'Thông tin đã bị xoá.', 'success');
                            location.reload()
                        }
                    })
                }
            });


            $('.reset-filter').click(function () {
                window.location.href = window.location.href.substring(0, window.location.href.indexOf('?'))
            });

            $('.filter-status').change(function () {
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'statusId', optionSelected.val())
            });

            $('.filter-priority').change(function () {
                let optionSelected = $(this).find("option:selected");
                window.location.href = updateParameter(window.location.href, 'priorityId', optionSelected.val())
            });

            $('.filter-start-date').change(function () {
                window.location.href = updateParameter(window.location.href, 'startDate', $(this).val())
            });

            $('.filter-due-date').change(function () {
                window.location.href = updateParameter(window.location.href, 'dueDate', $(this).val())
            });

            $('.filter-created-date').change(function () {
                window.location.href = updateParameter(window.location.href, 'created', $(this).val())
            });
        });
    </script>
    <script>
        Dropzone.options.myDropzone = {
            url: "{{route('files.import')}}",
            autoProcessQueue: false,
            paramName: "file",
            clickable: true,
            maxFilesize: 5, //in mb
            addRemoveLinks: true,
            dictDefaultMessage: "Tải file tại đây",
            init: function() {
                this.on("sending", function(file, xhr, formData) {
                    console.log("sending file");
                });
                this.on("success", function(file, responseText) {
                    console.log('great success');
                });
                this.on("addedfile", function(file){
                    console.log('file added');
                });
            }
        };
    </script>
@endsection

