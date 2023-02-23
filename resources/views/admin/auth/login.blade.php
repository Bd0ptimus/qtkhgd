@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')

@section('page-style')
        {{-- Page Css files --}}
        <link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">
@endsection
@section('content')
<section class="row flexbox-container">
  <div class="col-xl-8 offset-xl-2 d-flex justify-content-center">
      <div class="card bg-authentication rounded-0 mb-0">
            <div class="row m-0">
                <div class="col-lg-6 d-lg-block d-none text-center align-self-center py-0">
                    <img width=100 height=100 src="{{ asset('images/logo/gp-logo.png') }}" alt="branding logo">
                    <h1>EDU SMART</h1>
                    <h4>Đơn vị cung cấp: TẬP ĐOÀN GP GROUP</h4>
                </div>
                <div class="col-lg-6 col-12 p-0">
                    <div class="card rounded-0 mb-0 px-2">
                        <div class="card-header pb-1">
                            <div class="card-title">
                                <h4 class="mb-0">Đăng nhập hệ thống</h4>
                            </div>
                        </div>
                        <p class="px-2">Xin chào, vui lòng đăng nhập để tiếp tục sử dụng dịch vụ</p> 
                        <div class="card-content">
                            <div class="card-body pt-1">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        {{ $errors->first() }}
                                    </div>
                                @endif
                                <form action="{{ route('admin.login') }}" method="post" class="mt-2">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                        <input name="username" type="text" class="form-control" id="user-name" placeholder="Tài khoản">
                                        <div class="form-control-position">
                                            <i class="feather icon-user"></i>
                                        </div>
                                        <label for="user-name">Tài khoản</label>
                                    </fieldset>

                                    <fieldset class="form-label-group position-relative has-icon-left">
                                        <input name="password" type="password" class="form-control" id="user-password" placeholder="Mật khẩu">
                                        <div class="form-control-position">
                                            <i class="feather icon-lock"></i>
                                        </div>
                                        <label for="user-password">Mật khẩu</label>
                                    </fieldset>
                                    <div class="form-group d-flex justify-content-between align-items-center">
                                        <div class="text-left">
                                            <fieldset class="checkbox">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">Ghi nhớ đăng nhập</span>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="text-right"><a href="auth-forgot-password" class="card-link">Quên mật khẩu</a></div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary float-right btn-inline">Đăng nhập</button>
                                </form>
                            </div>
                        </div>
                        <div class="login-footer">
                            
                        </div>
                    </div>
                </div>
            </div>
      </div>
  </div>
</section>
@endsection
