@extends('layouts/contentLayoutMaster')

@section('title', 'Thông tin học sinh')

@section('page-style')
        {{-- Page Css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/app-user.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('main')

{{-- Nav Justified Starts --}}
  <section class="page-users-view" id="nav-justified">
    <div class="row">
      <div class="col-sm-12">
        <div class="card overflow-hidden">
          <div class="card-header">
            <h4 class="card-title">Thông tin học sinh {{$student->fullname}}</h4>
          </div>
          <div class="card-content">
            <div class="card-body">
              <p></p>
              <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#student-profile" role="tab"
                    aria-controls="student-profile" aria-selected="true">Thông tin cá nhân</a>
                </li>
              </ul>

              {{-- Tab panes --}}
              <div class="tab-content pt-1">
                <!-- Start Student Profile -->
                <div class="tab-pane active" id="student-profile" role="tabpanel" aria-labelledby="home-tab-justified">
                  <div class="row">
                  <!-- information start -->
                  <div class="col-md-6 col-12">
                    <div class="card detail-area">
                      <div class="card-body">
                        <div class="card-title mb-2">Học Sinh</div>
                        <table>
                          <tr>
                            <td class="font-weight-bold">Họ và tên</td>
                            <td>{{ $student->fullname }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Lớp</td>
                            <td>{{ $class ? $class->class_name : ''}}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Ngày sinh</td>
                            <td>{{ $student->dob }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Giới tính</td>
                            <td>{{ $student->gender }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Dân tộc</td>
                            <td>{{ $student->ethnic }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Tôn giáo</td>
                            <td>{{ $student->religion }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Quốc tịch</td>
                            <td>{{ $student->nationality }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Địa chỉ</td>
                            <td>{{ $student->address }}</td>
                          </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                  <!-- information start -->
                  <!-- social links end -->
                  <div class="col-md-6 col-12">
                    <div class="card detail-area">
                      <div class="card-body">
                        <div class="card-title mb-2">Phụ Huynh</div>
                        <table>
                          <tr>
                            <td class="font-weight-bold">Họ tên bố</td>
                            <td>{{ $student->father_name }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Số ĐT bố</td>
                            <td>{{ $student->father_phone }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Email bố</td>
                            <td>{{ $student->father_email }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Họ tên mẹ</td>
                            <td>{{ $student->mother_name }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Số ĐT mẹ</td>
                            <td>{{ $student->mother_phone }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Email mẹ</td>
                            <td>{{ $student->mother_email }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Là con thứ</td>
                            <td>{{ $student->child_no }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Tổng số con</td>
                            <td>{{ $student->total_childs }}</td>
                          </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  {{-- Nav Justified Ends --}}

@endsection
@section('vendor-script')
{{-- vendor files --}}
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
  <script src="{{ asset(mix('js/scripts/pages/app-user.js')) }}"></script>
  <script type="text/javascript">
      $(document).ready(function () {
        $('#specialist_test_table').DataTable();
        $('#periodical_check_table').DataTable();
        $('#health_abnormal_table').DataTable();  
      });
  </script>
@endsection
