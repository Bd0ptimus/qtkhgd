@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
@endsection

@section('main')
    @php 
        $title = 'Quản lý tổ chuyên môn';
        $breadcrumbs = [
            ['name' => 'Bàn làm việc', 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $title],
        ];
    @endphp 
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách các tổ chuyên môn</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex">
                                <div class="btn-group text-nowrap">
                                    <a type="button"
                                        class="btn btn-flat btn-success"
                                        href="{{ route('school.regular_group.create', ['id' => $school->id]) }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm tổ chuyên môn
                                    </a>
                                </div>
                                @if(count($regularGroups) == 0)
                                    <div class="btn-group text-nowrap ml-1">
                                        <a type="button"
                                            class="btn btn-flat btn-warning"
                                            href="{{ route('school.regular_group.init', ['id' => $school->id]) }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            Tạo tổ chuyên môn mặc định theo cấp học
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="shool-regular-group">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Cấp học</th>
                                        <th scope="col">Môn học</th>
                                        <th scope="col">Khối học</th>
                                        
                                        <th scope="col">Tổ trưởng</th>
                                        <th scope="col">Tổ phó</th>

                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($regularGroups as $key => $regularGroup)
                                        
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $regularGroup->name}}</td>
                                            <td><p>{{ $regularGroup->description}}</p></td>
                                            <td>{{ App\Models\School::SCHOOL_TYPES[$regularGroup->school_level]}}</td>
                                            <td>
                                                @foreach($regularGroup->subjects as $subject)   
                                                    <button class='btn success'>{{ $subject->name }} </button>
                                                @endforeach
                                            </td>
                                            <td>        
                                                @foreach($regularGroup->groupGrades->pluck('grade')->toArray() as $grade)
                                                    <button class='btn success'>{{ \App\Models\SchoolClass::GRADES[$grade] }} </button>
                                                @endforeach
                                            </td>
                                            <td>{{ $regularGroup->leader ?  ($regularGroup->leader->staff->fullname ?? '') : ''}}</td>
                                            <td>
                                                @if(count($regularGroup->deputies) > 0)
                                                    @foreach($regularGroup->deputies as $groupDeputy)
                                                        <button class='btn btn-datatable'>{{ $groupDeputy->staff->fullname ?? ''}}</button>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-info btn-datatable"
                                                    href="{{ route('school.regular_group.staffs', ['id' => $school->id, 'rgId' => $regularGroup->id ]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>Giáo viên
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-warning btn-datatable"
                                                    href="{{ route('school.regular_group.plan.index', ['id' => $school->id, 'rgId' => $regularGroup->id ]) }}">
                                                    <i class="fa fa-book" aria-hidden="true"></i>Kế hoạch tổ
                                                </a>
                                                <a style="margin-top: 3px" type="button" 
                                                    name=""
                                                    class="btn btn-flat btn-success btn-datatable"
                                                    href="{{ route('school.regular_group.edit', ['id' => $school->id, 'rgId' => $regularGroup->id]) }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                </a>
                                                <a style="margin-top: 3px" type="button"
                                                    class="btn btn-flat btn-danger delete-item btn-datatable"
                                                    data-url="{{route('school.regular_group.delete', ['id' => $school->id, 'rgId'  => $regularGroup->id])}}" 
                                                    href="#">
                                                    <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá
                                                </a>
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

            $('#shool-regular-group').DataTable();

            $('#shool-regular-group').on('click','.delete-item',function () {
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá tổ chuyên môn này?');
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
                            alert('Đã xoá Tổ chuyên môn');
                        }
                    });
                }
            });
        });
    </script>
@endsection
