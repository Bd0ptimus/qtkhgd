@extends('layouts/contentLayoutMaster')

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
                        <h4 class="card-title">Danh sách các môn học theo khối</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">

                            <form method="post">
                                @csrf
                            
                                <div class="d-flex float-right">
                                    <div class="text-nowrap ml-1">
                                        <button type="submit" class="main-action btn btn-success">
                                            Lưu lại
                                        </button>
                                    </div>
                                </div>
                            
                                <div class="table-responsive">
                                    <table class="table zero-configuration" id="sysconf-subject-by-grade">
                                        <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th scope="col">Khối</th>
                                            <th scope="col">Tổng số môn học</th>
                                            <th scope="col">DS môn học</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data as $grade => $subjects)
                                            <tr>
                                                <td>{{ $grade }}</td>
                                                <td scope="row">{{ App\Models\SchoolClass::GRADES[$grade]}}</td>
                                                <td scope="row">{{ count($subjects) }}</td>
                                                <td scope="row">
                                                    @php
                                                        $listSubjects = [];
                                                        foreach($subjects as $subject) {
                                                            $listSubjects[] = (int)$subject->id;
                                                        }
                                                    @endphp

                                                    <select class="form-control subject select2"  multiple="multiple" data-placeholder="Môn học" style="width: 100%;" name="grades[{{$grade}}][subjects][]">
                                                        <option value=""></option>
                                                        @foreach (App\Models\Subject::get() as $index => $subject)
                                                            <option value="{{ $subject->id }}"  {{ (count($listSubjects) && in_array($subject->id, $listSubjects))?'selected':'' }}>{{ $subject->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
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

            $('#sysconf-subject-by-grade').DataTable();

            $('#sysconf-subject-by-grade').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá môn học này?');
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
                            alert('Đã xoá môn học');
                        }
                    });
                }
            });
        });
    </script>
@endsection
