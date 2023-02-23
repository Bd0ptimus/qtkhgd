@extends('layouts/contentLayoutMaster')
@section('title', $title)
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
            <h4 class="card-title">Thông tin nhân viên {{$staff->fullname}}</h4>
          </div>
          <div class="card-content">
            <div class="card-body">
              <p></p>
              <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#staff-profile" role="tab"
                    aria-controls="staff-profile" aria-selected="true">Thông tin cá nhân</a>
                </li>
               
              </ul>

              {{-- Tab panes --}}
              <div class="tab-content pt-1">
                <!-- Start Student Profile -->
                <div class="tab-pane active" id="staff-profile" role="tabpanel" aria-labelledby="home-tab-justified">
                  <div class="row">
                  <!-- information start -->
                  <div class="col-md-6 col-12">
                    <div class="card detail-area">
                      <div class="card-body">
                        <div class="card-title mb-2">Thông tin cơ bản</div>
                        <table>
                          <tr>
                            <td class="font-weight-bold">Họ và tên</td>
                            <td>{{ $staff->fullname }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Ngày sinh</td>
                            <td>{{ $staff->dob }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Số chứng minh</td>
                            <td>{{ $staff->identity_card }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Giới tính</td>
                            <td>{{ $staff->gender }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Dân tộc</td>
                            <td>{{ $staff->ethnic }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Tôn giáo</td>
                            <td>{{ $staff->religion }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Quốc tịch</td>
                            <td>{{ $staff->nationality }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Địa chỉ</td>
                            <td>{{ $staff->address }}</td>
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
                        <div class="card-title mb-2">Thông tin nghiệp vụ</div>
                        <table>
                          <tr>
                            <td class="font-weight-bold">Trình độ chuyên môn</td>
                            <td>{{ $staff->qualification }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Chức danh</td>
                            <td>{{ $staff->position }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Chứng chỉ hành nghề</td>
                            <td>{{ $staff->getTextValue('professional_certificate') }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Chuyên trách</td>
                            <td>{{ $staff->getTextValue('responsible') }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Kiêm nhiệm</td>
                            <td>{{ $staff->getTextValue('concurrently') }}</td>
                          </tr>
                          <tr>
                            <td class="font-weight-bold">Trạng thái làm việc</td>
                            <td>{{ $staff->status }}</td>
                          </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                  </div>
                </div>
                <!-- Finish Student Profile -->
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
  <script src="{{ asset(mix('js/scripts/pages/app-user.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
  <script type="text/javascript">
      $(document).ready(function () {
        $('#specialist_test_table').DataTable();
        $('#health_abnormal_table').DataTable();
      });
  </script>
@endsection
