@extends('layouts.contentLayoutMaster')

@section('main')
    <div class="card">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="card-content">
                    <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-row "
                          id="form-main" enctype="multipart/form-data">
                        <input type="hidden" name="previous_url" value="{{$previous_url}}">
                        <div class="form-group col-md-12">
                            @if(Admin::user()->email == "")
                                <h4 style="color:red">Vui lòng cập nhật email để tiếp tục sử dụng phần mềm</h4>
                            @endif

                            @if(Admin::user()->force_change_pass == true)
                                <h4 style="color:red">Để đảm bảo an toàn, vui lòng thay đổi mật khẩu để tiếp tục sử dụng
                                    dịch vụ</h4>
                            @endif
                        </div>

                        <div class="form-group col-md-4 {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="control-label">{{ trans('user.name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name',$user['name']??'')}}"
                                   class="form-control name" placeholder=""/>
                            @if ($errors->has('name'))
                                <span class="help-block" style="color:red">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>


                        <div class="form-group col-md-4  {{ $errors->has('phone_number') ? ' has-error' : '' }}">
                            <label for="phone_number" class="control-label">Số điện thoại</label>
                            <input type="text" id="phone_number" name="phone_number"
                                   value="{{ old('phone_number',$user['phone_number']??'') }}"
                                   class="form-control phone_number" placeholder=""/>
                            @if ($errors->has('phone_number'))
                                <span class="help-block" style="color:red">
                                    {{ $errors->first('phone_number') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group col-md-4  {{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">Email</label>
                            <input type="text" id="email" name="email" value="{{ old('email',$user['email']??'') }}"
                                   class="form-control" placeholder=""/>
                            @if ($errors->has('email'))
                                <span class="help-block" style="color:red">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>

                        <div class="hidden form-group col-md-6  {{ $errors->has('avatar') ? ' has-error' : '' }}">
                             <label for="avatar" class="control-label">{{ trans('user.avatar') }}</label>
                             <input type="text" id="avatar" name="avatar"
                                   value="{{env('APP_URL')}}/data/avatar/636486094790550182_1096_SMBpdthqNA.jpeg"
                                   class="form-control input-sm avatar" placeholder=""/>
                             <span class="input-group-btn">
                                 <a data-input="avatar" data-preview="preview_avatar" data-type="avatar"
                                    class="btn btn-sm btn-primary lfm">
                                   <i class="fa fa-picture-o"></i> {{trans('product.admin.choose_image')}}
                                 </a>
                             </span>
                            @if ($errors->has('avatar'))
                                <span class="help-block" style="color:red">
                                    {{ $errors->first('avatar') }}
                                </span>
                            @endif
                            <div id="preview_avatar" class="img_holder">
                                <img src="{{ old('avatar',$user['avatar']??'') }}">
                            </div>
                        </div>


                        <div class="form-group col-md-4  {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">{{ trans('user.password') }}</label>
                            <input type="password" id="password" name="password" value="{{ old('password')??'' }}"
                                   class="form-control password" placeholder=""/>
                            @if ($errors->has('password'))
                                <span class="help-block" style="color:red">
                                    {{ $errors->first('password') }}
                                </span>
                            @else
                                @if ($user && $user->force_change_pass != true)
                                    <span class="help-block text-danger">
                                        {{ trans('user.admin.keep_password') }}
                                    </span>
                                @endif
                            @endif
                        </div>

                        <div class="form-group col-md-4  {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password"
                                   class="control-label">{{ trans('user.password_confirmation') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   value="{{ old('password_confirmation')??'' }}"
                                   class="form-control password_confirmation" placeholder=""/>

                            @if ($errors->has('password_confirmation'))
                                <span class="help-block" style="color:red">
                                    {{ $errors->first('password_confirmation') }}
                                </span>
                            @else
                                @if ($user && $user->force_change_pass != true)
                                    <span class="help-block text-danger">
                                        {{ trans('user.admin.keep_password') }}
                                    </span>
                                @endif
                            @endif
                        </div>

                        {{-- select roles --}}
                        <div class="form-group col-md-4 {{ $errors->has('roles') ? ' has-error' : '' }}">
                            @php
                                $listRoles = [];
                                    $old_roles = old('roles',($user)?$user->roles->pluck('id')->toArray():'');
                                    if(is_array($old_roles)){
                                        foreach($old_roles as $value){
                                            $listRoles[] = (int)$value;
                                        }
                                    }
                            @endphp
                            <label for="roles" class="control-label">{{ trans('user.admin.select_roles') }}</label>

                            <select readonly required class="form-control input-sm roles select2"
                                    data-placeholder="{{ trans('user.admin.select_roles') }}" style="width: 100%;">
                                <option value=""></option>
                                @foreach ($listRoles as $k => $v)
                                    <option value="{{ $k }}" selected>{{ $roles[$v] }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group col-md-4">
                            <input type="checkbox" id="email_notification" name="email_notification"
                                   {{ $user['email_notification'] == 1 ? 'checked' : ''}} placeholder=""/>
                            <label for="email_notification" class="control-label">Nhận thông báo qua mail</label>
                        </div>

                        <div class="form-group col-md-4">
                            <input type="checkbox" id="web_notification" name="web_notification"
                                   {{ $user['web_notification'] == 1 ? 'checked' : ''}} placeholder=""/>
                            <label for="web_notification" class="control-label">Nhận thông báo qua web</label>
                        </div>

                        <div class="form-group col-md-4 text-right">
                            @csrf
                            <div class="btn-group">
                                <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
                            </div>

                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">

    {{-- switch --}}
    <link rel="stylesheet" href="{{ asset('admin/plugin/bootstrap-switch.min.css')}}">
@endpush

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    {{-- switch --}}
    <script src="{{ asset('admin/plugin/bootstrap-switch.min.js')}}"></script>

    <script type="text/javascript">
        $("[name='top'],[name='status']").bootstrapSwitch();
    </script>

    <script type="text/javascript">

        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endpush
