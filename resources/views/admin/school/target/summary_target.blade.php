@extends('layouts/contentLayoutMaster')
@php
    $breadcrumbs;
@endphp
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
                    <div class="card-header">
                        <h4 class="card-title">Thống kê các tiêu chí của chỉ tiêu {{$target->title}}</h4>
                        <h4 class="card-title">Kết quả đạt được: {{ round($target->getResultMainPoint(), 1) }}%</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-subject">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tiêu chí</th>
                                        <th scope="col">Trọng số</th>
                                        <th scope="col">Kết quả</th>
                                        <th scope="col">Đánh giá kết quả</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($target->mainPoints as $key => $mainPoint)
                                        <tr>
                                            <td scope="row" class="font-weight-bold">{{$key+1}}</td>
                                            <td class="font-weight-bold">{{ $mainPoint->content}}</td>
                                            <td class="font-weight-bold">{{ $mainPoint->index_point}}%</td>
                                            <td class="font-weight-bold">{{ round($mainPoint->result, 1) }}%</td>
                                            <td class="font-weight-bold">{{ round($mainPoint->final_point, 1) }}%</td>
                                            @if ($mainPoint->subPoints->count() == 0)
                                                <td>   
                                                    <a style="margin-top: 3px; text-decoration: none; color:white;" type="button" id="self-result-button"
                                                        name="result"
                                                        class="btn btn-datatable btn-info update-item"
                                                        data-url="{{ route('school.target.result.main.point', ['id' => $school->id, 'pointId' => $mainPoint->id]) }}">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>Kết quả
                                                    </a>
                                                </td>
                                            @endif
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
                        dataType:"json",
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
@endsection
