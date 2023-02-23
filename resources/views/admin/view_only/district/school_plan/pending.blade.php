@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = 'Duyệt Kế hoạch giáo dục của các trường';
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('district.manage', ['id' => $district->id])],
            ['name' => $title],
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <p class="card-text"> </p>
                            
                            <table class="table zero-configuration" id="shool-plans">
                                <thead>
                                <tr>
                                    <th scope="col">STT</th>
                                    <th scope="col">Trường</th>
                                    <th scope="col">Thời gian tạo</th>
                                    <th scope="col">Cập nhật lần cuối</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($plans as $key => $plan)
                                        
                                        <tr>
                                            <th scope="row">#{{ $plan->id}}</th>
                                            <td>{{ $plan->school->school_name}}</td>
                                            <td>{{ $plan->created_at }}</td>
                                            <td>{{ $plan->updated_at }}</td>
                                            <td>{{ PLAN_STATUSES[$plan->status]}}</td>
                                            <td>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info"
                                                    href="{{ route('school.school_plan.edit', ['id' => $plan->school->id, 'planId' => $plan->id]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Xem
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-warning"
                                                    href="{{ route('school.school_plan.download', ['id' => $plan->school->id, 'planId' => $plan->id]) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>Tải về
                                                </a>

                                                <a style="margin-top: 3px" href="#" class="btn btn-flat btn-warning btn-datatable"
                                                    data-toggle="modal" data-target="#modalAddNote{{$plan->id}}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Nhận xét
                                                </a>

                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-success"
                                                    href="{{ route('district.schools.approve_school_plan', ['districtId' => $district->id, 'planId' => $plan->id]) }}">
                                                    <i class="fa fa-check" aria-hidden="true"></i>Duyệt kế hoạch
                                                </a>
                                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            

                            @foreach($plans as $key => $plan)
                                <div class="modal fade" id="modalHistory{{$plan->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Lịch sử</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped text-nowrap table-plan">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Thời gian</th>
                                                            <th scope="col">Nội dung</th>
                                                            <th scope="col">Trạng thái kế hoạch</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($plan->histories as $history)
                                                            <tr>
                                                                <td>{{$history->created_at}}</td>
                                                                <td>{{$history->notes}}</td>
                                                                <td>{{PLAN_STATUSES[$history->status]}}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                                        </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalAddNote{{$plan->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Thêm nhận xét</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('district.schools.add_review_school_plan', ['districtId' => $district->id, 'planId' => $plan->id]) }}">
                                        @csrf    
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <textarea class='form-control description' name='notes'></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Nhận xét</button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
            
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

            $('#shool-plans').DataTable();

            $('#shool-plans').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá kế hoạch này?');
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
                            alert('Đã xoá kế hoạch');
                        }
                    });
                }
            });
        });
    </script>
@endsection
