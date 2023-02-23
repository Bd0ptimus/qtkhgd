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
                <li class="nav-item">
                  <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#student-health-profile" role="tab"
                    aria-controls="student-health-profile" aria-selected="true">Hồ sơ sức khoẻ</a>
                </li>
             <li class="nav-item">
                  <a class="nav-link" id="specialist-test-tab-justified" data-toggle="tab" href="#specialist-test" role="tab"
                    aria-controls="specialist-test" aria-selected="false">Lịch sử khám chuyên khoa</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="periodical-check-tab-justified" data-toggle="tab" href="#periodical-check" role="tab"
                    aria-controls="periodical-check" aria-selected="false">Chỉ số sức khoẻ định kỳ</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="health-abnormal-tab-justified" data-toggle="tab" href="#health-abnormal" role="tab"
                    aria-controls="health-abnormal" aria-selected="false">Diễn biến sức khoẻ bất thường</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="medicine-tab-justified" data-toggle="tab" href="#medicine" role="tab"
                    aria-controls="medicine" aria-selected="false">Tình hình sử dụng thuốc</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="settings-tab-justified" data-toggle="tab" href="#settings-just" role="tab"
                    aria-controls="settings-just" aria-selected="false">Biểu đồ phát triển</a>
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
                <!-- Finish Student Profile -->
                
                <!-- Start Student Health Profile -->
                <div class="tab-pane" id="student-health-profile" role="tabpanel" aria-labelledby="profile-tab-justified">
                  <div class="row">
                    <div class="col-md-5 col-12">
                        <div class="card detail-area">
                          <div class="card-body">
                          <div class="card-title mb-2">Lịch sử sức khoẻ</div>
                            
                              <table>
                                  <tr>
                                    <td class="font-weight-bold">Dạng khuyết tật</td>
                                    <td>{{ $student->disabilities }}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Tiền sử sức khỏe</td>
                                    <td>{{ $student->health_history }}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Sản khoa</td>
                                    <td>{{ $student->born_history }}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Tiền sử bệnh tật</td>
                                    <td>{{ $student->disease_history}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Bệnh đang điều trị</td>
                                    <td>{{ $student->treating_disease }}</td>
                                  </tr>
                                </table>
                           
                          </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-12">
                        <div class="card detail-area">
                          <div class="card-body">
                          <div class="card-title mb-2">Lịch sử tiêm chủng</div>
                              <table>
                                  <tr>
                                    <th>Loại tiêm chủng</th>
                                    <th>Có</th>
                                    <th>Không</th>
                                    <th>Không Nhớ Rõ</th>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">TC BCG</td>
                                    <td>{{ $student->tc_bcg == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bcg == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bcg == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Ho gà, bạch hầu, uốn ván (mũi 1)</td>
                                    <td>{{ $student->tc_bhhguv_m1 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bhhguv_m1 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bhhguv_m1 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Ho gà, bạch hầu, uốn ván (mũi 2)</td>
                                    <td>{{ $student->tc_bhhguv_m2 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bhhguv_m2 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bhhguv_m2 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Ho gà, bạch hầu, uốn ván (mũi 3)</td>
                                    <td>{{ $student->tc_bhhguv_m3 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bhhguv_m3 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bhhguv_m3 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Bại liệt (mũi 1)</td>
                                    <td>{{ $student->tc_bailiet_m1 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bailiet_m1 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bailiet_m1 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Bại liệt (mũi 2)</td>
                                    <td>{{ $student->tc_bailiet_m2 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bailiet_m2 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bailiet_m2 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Bại liệt (mũi 3)</td>
                                    <td>{{ $student->tc_bailiet_m3 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bailiet_m3 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_bailiet_m3 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Viêm gan B ( mũi 1)</td>
                                    <td>{{ $student->tc_viemganb_m1 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemganb_m1 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemganb_m1 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Viêm gan B ( mũi 2)</td>
                                    <td>{{ $student->tc_viemganb_m2 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemganb_m2 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemganb_m2 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Viêm gan B ( mũi 3)</td>
                                    <td>{{ $student->tc_viemganb_m3 == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemganb_m3 == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemganb_m3 == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Sởi</td>
                                    <td>{{ $student->tc_soi == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_soi == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_soi == 2 ? "X" : ""}}</td>
                                  </tr>
                                  <tr>
                                    <td class="font-weight-bold">Viêm não nhật bản</td>
                                    <td>{{ $student->tc_viemnaonb == 1 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemnaonb == 0 ? "X" : ""}}</td>
                                    <td>{{ $student->tc_viemnaonb == 2 ? "X" : ""}}</td>
                                  </tr>
                                  
                                </table>
                           
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
                <!-- Finish Health Profile -->
                <!-- Start Student Specialist Test -->
                <div class="tab-pane" id="specialist-test" role="tabpanel" aria-labelledby="specialist-test-tab-justified">
                <div class="row">
                    <div class="col-12">
                      <div class="card">
                      @if(empty($specialistData))
                        <p>
                          Chưa có dữ liệu
                        </p>
                      @else
                        <div class="table-responsive">
                          <table class = "table text-nowrap table-bordered table-striped text-center" id="specialist_test_table" style="border-spacing: 1px">
                            <thead>
                              <tr>
                                <th><b>STT</b></th>
                                <th><b>Ngày theo dõi</b></th>
                                <th><b>Khoa Nhi</b></th>
                                <th><b>Khoa Mắt</b></th>
                                <th><b>Tai mũi họng</b></th>
                                <th><b>Răng hàm mặt</b></th>
                                <th><b>Cơ xương khớp</b></th>
                              </tr>
                            </thead>
                            <tbody>
                            @php
                                $stt = 1;
                            @endphp
                            @foreach($specialistData as $specialist)
                                <tr>
                                    <td>{{ $stt++ }}</td>
                                    <td> {{ $specialist['date'] }} </td>
                                    <td><?=  $specialist['data_nhi'] ?? null ?></td>
                                    <td><?=  $specialist['data_mat'] ?? null ?></td>
                                    <td><?=  $specialist['data_tmh'] ?? null ?></td>
                                    <td><?= $specialist['data_rhm'] ?? null ?></td>
                                    <td><?= $specialist['data_cxk'] ?? null ?></td>
                                </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
               <!-- Finish Specialist Test -->
               <!-- Start Student Periodical Check -->
                <div class="tab-pane" id="periodical-check" role="tabpanel" aria-labelledby="periodical-check-tab-justified">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="table-responsive">
                          <table class = "table text-nowrap table-bordered table-striped text-center" id="periodical_check_table" style="border-spacing: 1px">
                            <thead>
                              <tr>
                                <th style="vertical-align: middle; " rowspan="2" data-field="0">
                                  <div class="th-inner ">STT</div>
                                </th>
                                <th style="vertical-align: middle; " rowspan="2" data-field="month">
                                  <div class="th-inner ">Tháng</div>
                                </th> 
                                <th style="vertical-align: middle; " rowspan="2" data-field="month_age">
                                  <div class="th-inner ">Tháng tuổi</div>
                                </th>
                                  <th style="vertical-align: middle; width: 100px; " rowspan="2" data-field="weight">
                                  <div class="th-inner ">Cân nặng (kg)</div>
                                </th>
                                <th style="vertical-align: middle; " rowspan="2" data-field="height">
                                  <div class="th-inner ">Chiều cao (cm)</div>
                                </th>
                                <th style="vertical-align: middle; " rowspan="2" data-field="bmi">
                                  <div class="th-inner ">BMI và Phân kênh BMI</div/.                     
                                </th>
                                <th style="text-align: center; vertical-align: middle; " colspan="2">
                                  <div class="th-inner ">Huyết áp</div>
                                </th>
                                <th style="vertical-align: middle; " rowspan="2" data-field="heart_rate">
                                  <div class="th-inner ">Nhịp tim</div>
                                </th>
                                <th style="text-align: center; vertical-align: middle; " colspan="2">
                                  <div class="th-inner ">Mắt không đeo kính</div>
                                </th>
                                <th style="text-align: center; vertical-align: middle; " colspan="2">
                                  <div class="th-inner ">Mắt có đeo kính</div>
                                  
                                </th>
                              </tr>
                              <tr>
                                <th style="vertical-align: top; " data-field="systolic_blood_pressure" data-not-first-th="">
                                  <div class="th-inner ">Tâm thu</div>
                                </th>
                                <th style="vertical-align: top; " data-field="diastolic_blood_pressure">
                                  <div class="th-inner ">Tâm trương</div>
                                </th>
                                <th style="vertical-align: top; " data-field="right_without_glass_eyesight">
                                  <div class="th-inner ">Mắt phải</div>
                                </th>
                                <th style="vertical-align: top; " data-field="left_without_glass_eyesight">
                                  <div class="th-inner ">Mắt trái</div>
                                </th>
                                <th style="vertical-align: top; " data-field="right_with_glass_eyesight">
                                  <div class="th-inner ">Mắt phải</div>
                                </th>
                                <th style="vertical-align: top; " data-field="left_with_glass_eyesight">
                                  <div class="th-inner ">Mắt trái</div>
                                </th>
                              </tr>
                            </thead>
                            @php
                                $stt = 1;
                            @endphp
                            <tbody>
                            @foreach($healthIndexData as $healthIndex)
                              <tr>
                                  <td>{{ $stt++ }}</td>
                                  <td>{{ date('m/Y', strtotime($healthIndex->month)) }}</td>
                                  <td>{{ ($healthIndex->month_age) }}</td>
                                  <td><?= $healthIndex->weight_result ?></td>
                                  <td><?= $healthIndex->height_result ?></td>
                                  <td><?= $healthIndex->bmi_result ?></td>
                                  <td><?= $healthIndex->systolic_result ?></td>
                                  <td><?= $healthIndex->diastolic_result ?></td>
                                  <td><?= $healthIndex->heart_rate_result ?></td>
                                  <td><?= $healthIndex->right_without_glass_eyesight_result ?></td>
                                  <td><?= $healthIndex->left_without_glass_eyesight_result ?></td>
                                  <td><?= $healthIndex->right_with_glass_eyesight_result ?></td>
                                  <td><?= $healthIndex->left_with_glass_eyesight_result ?></td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Finish Periodical Check -->
                <!-- Start Student health-abnormal -->
                <div class="tab-pane" id="health-abnormal" role="tabpanel" aria-labelledby="health-abnormal-tab-justified">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        @if(count($abnormals) == 0)
                          <p>
                            Chưa có dữ liệu
                          </p>
                        @else
                        <div class="card-body table-responsive">
                          <table class = "table text-nowrap table-bordered table-striped text-center" id="health_abnormal_table" style="border-spacing: 1px">
                          <thead>
                              <tr>
                                <th><b>STT</b></th>
                                <th><b>Ngày</b></th>
                                <th><b>Chuẩn đoán ban đầu</b></th>
                                <th><b>Phân loại</b></th>
                                <th><b>Chuẩn đoán</b></th>
                                <th><b>KQ Xét Nghiệm</b></th>
                                <th><b>Tình trạng</b></th>
                                <th><b>Ngày khởi phát</b></th>
                                <th><b>Xử lý</b></th>
                                <th><b>Chuyển tuyến</b></th>
                                <th><b>Ghi chú</b></th>
                              </tr>
                            </thead>
                            <tbody>
                            @php
                                $stt = 1;
                            @endphp
                            
                            @foreach($abnormals as $abnormal)
                              <tr>
                                  <td>{{ $stt++ }}</td>
                                  <td>{{ ($abnormal->date) }}</td>
                                  <td>{{ $abnormal->initial_diagnosis }}</td>
                                  <td>{{ $abnormal->type }}</td>
                                  <td>{{ $abnormal->diagnosis }}</td>
                                  <td>{{ $abnormal->test_result }}</td>
                                  <td>{{ $abnormal->patient_status }}</td>
                                  <td>{{ $abnormal->begin_date }}</td>
                                  <td>{{ $abnormal->handle }}</td>
                                  <td>{{ $abnormal->move_to }}</td>
                                  <td>{{ $abnormal->note }}</td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                        @endif
                      </div>  
                    </div>
                  </div>
                </div>
                <!-- Finish Student health-abnormal -->
                <div class="tab-pane" id="medicine" role="tabpanel" aria-labelledby="medicine-tab-justified">
                  <p>
                    Chưa có dữ liệu
                  </p>
                </div>
                <div class="tab-pane" id="settings-just" role="tabpanel" aria-labelledby="settings-tab-justified">
                  <p>
                    Chưa có dữ liệu
                  </p>
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
