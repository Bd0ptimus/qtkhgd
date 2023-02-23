@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@php
    $breadcrumbs;
@endphp

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bảng mục tiêu năm học.</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @if(Admin::user()->inRoles([ROLE_HIEU_TRUONG, ROLE_ADMIN, ROLE_CM]))
                                <div class="d-flex">
                                    <div class="btn-group text-nowrap">
                                        <a type="button"
                                            class="main-action btn btn-flat btn-success"
                                            href="{{ route('school.target.create', ['id' => $school->id]) }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            Thêm chỉ tiêu
                                        </a>
                                    </div>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-subject">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên mục tiêu</th>
                                        <th scope="col">Loại chỉ tiêu</th>
                                        <th scope="col">Trọng số</th>
                                        <th scope="col">Kết quả </th>
                                        <th scope="col">Đánh giá kết quả</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    
                                    @foreach($targets as $key=>$target)
                                        <tr>
                                            <td scope="row" class="font-weight-bold">{{$key+1}}</td>
                                            <td class="font-weight-bold">{{ $target->title}}</td>
                                            <td>{{ TARGET_TYPES[$target->type] ?? ''}}</td>
                                            <td>{{ $target->target_index }} %</td>
                                            <td>{{ round($target->result, 1) }} %</td>
                                            <td>{{ round($target->final_target ?? 0, 1) }} %</td>
                                            <td>
                                                @if($target->school_id == $school->id)
                                                 
                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="btn btn-datatable btn-warning"
                                                            href="{{ route('school.target.edit', ['id' => $school->id, 'targetId' => $target->id]) }}">
                                                            <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                        </a>
                                                        @if (count($target->mainPoints) > 0)
                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="btn btn-datatable btn-primary"
                                                            href="{{ route('school.target.assign_staff', ['id' => $school->id, 'targetId' => $target->id]) }}">
                                                            <i class="fa fa-pencil" aria-hidden="true"></i>Giao tiêu chí
                                                        </a>
                                                    
                                                        <a style="margin-top: 3px" type="button" 
                                                            name=""
                                                            class="btn btn-datatable btn-primary"
                                                            href="{{ route('school.target.summary_target', ['id' => $school->id, 'targetId' => $target->id]) }}">
                                                            <i class="fa fa-eye" aria-hidden="true"></i>Thống kê tiêu chí
                                                        </a>
                                                        @endif
                                                        @if (count($target->mainPoints) == 0)
                                                        <a style="margin-top: 3px; text-decoration: none; color:white;" type="button" id="self-result-button"
                                                            name="result"
                                                            class="btn btn-datatable btn-info update-item"
                                                            data-url="{{ route('school.target.result', ['id' => $school->id, 'targetId' => $target->id]) }}">
                                                            <i class="fa fa-pencil" aria-hidden="true"></i>Kết quả
                                                        </a>
                                                        @endif
                                                        <a style="margin-top: 3px" type="button"
                                                            class="btn btn-datatable btn-danger delete-item"
                                                            data-url="{{route('school.target.delete', ['id' => $school->id, 'targetId' => $target->id])}}" href="#">
                                                            <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
                                                        </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
            $('.select2').select2({
                allowClear: true
            });

            $('#shool-subject').DataTable();

            $('#shool-subject').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá chỉ tiêu này?');
                if(confirmDelete) {
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            element.parents('tr').remove();
                            alert('Đã xoá chỉ tiêu');
                        }
                    });
                }
            });

            $('#shool-subject').on('click','.update-item',function (e) {
                e.preventDefault();
                var resultInput = prompt('Kết quả %','');
                var regex = "^0*(?:[1-9][0-9]?|100)$";
                if(resultInput.match(regex) ||parseInt(resultInput)==0){
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            result: resultInput,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            console.log(res['error']);
                            if(res['error']==0){
                                document.location.reload();
                            }
                            alert('Đã thêm kết quả');
                        }
                    });
                }else{
                    alert('Chỉ chấp nhận số từ 0-100');
                }
                
            });
        });

    </script>
    <style>
        #self-result-button:hover{
        }
    </style>
@endsection