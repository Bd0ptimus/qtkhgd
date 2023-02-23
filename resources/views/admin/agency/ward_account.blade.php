
@extends('layouts/contentLayoutMaster')

@section('title', 'Danh sách đơn vị theo địa chính')

@section('vendor-style')
        {{-- vendor css files --}}
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('main')

  <!-- Scroll - horizontal and vertical table -->
  <section id="horizontal-vertical">
      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Danh sách các tài khoản {{ $ward->name }}</h4>
                  </div>
                  <div class="card-content">
                      <div class="card-body card-dashboard">
                          <p class="card-text"></p>
                          <div class="table-responsive">
                              <table class="table zero-configuration">
                                  <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên đăng nhập</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">GSO ID</th>
                                        <th scope="col">Ngày cập nhật</th>
                                        
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($users as $key => $user)
                                        <tr>
                                            <th scope="row">{{ $key + 1}}</th>
                                            <td>{{ $user->username}}</td>
                                            <td>{{ $user->name}}</td>
                                            <td>{{ $ward->gso_id}}</td>
                                            <td>{{ $user->updated_at }}</td>
                                            
                                            <td>
                                            
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
@endsection
