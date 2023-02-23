@extends('layouts.contentLayoutMaster')

@section('main')  
   <div class="row">
      <div class="col-md-12">
        <div class="card">
                <div class="card-header">
                    <h2 class="box-title">{{ $title_description??'' }}</h2>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <!-- form start -->
                        <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">
                            <div class="fields-group">

                                <div class="form-group   {{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-sm-2  control-label">{{ trans('user.name') }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            
                                            <input type="text" required  id="name" name="name" value="{{ old('name',$user['name']??'')}}" class="form-control name" placeholder="" />
                                        </div>
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                    {{ $errors->first('name') }}
                                                </span>
                                            @endif
                                    </div>
                                </div>

                                <div class="form-group   {{ $errors->has('phone_number') ? ' has-error' : '' }}">
                                    <label for="phone_number" class="col-sm-2  control-label">Số điện thoại</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            
                                            <input type="text"  id="phone_number" name="phone_number" value="{{ old('phone_number',$user['phone_number']??'')}}" class="form-control phone_number" placeholder="" />
                                        </div>
                                            @if ($errors->has('phone_number'))
                                                <span class="help-block">
                                                    {{ $errors->first('phone_number') }}
                                                </span>
                                            @endif
                                    </div>
                                </div>

                                <div class="form-group   {{ $errors->has('username') ? ' has-error' : '' }}">
                                    <label for="username" class="col-sm-2  control-label">{{ trans('user.user_name') }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            
                                            <input type="text" required  id="username" name="username" value="{{ old('username',$user['username']??'') }}" class="form-control username" placeholder="" />
                                        </div>
                                            @if ($errors->has('username'))
                                                <span class="help-block">
                                                    {{ $errors->first('username') }}
                                                </span>
                                            @endif
                                    </div>
                                </div>

                                <div class="hidden form-group   {{ $errors->has('avatar') ? ' has-error' : '' }}">
                                    <label for="avatar" class="col-sm-2  control-label">{{ trans('user.avatar') }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="text" id="avatar" name="avatar" value="{{env('APP_URL')}}/data/avatar/636486094790550182_1096_SMBpdthqNA.jpeg" class="form-control input-sm avatar" placeholder=""  />
                                            <span class="input-group-btn">
                                            <a data-input="avatar" data-preview="preview_avatar" data-type="avatar" class="btn btn-sm btn-primary lfm">
                                            <i class="fa fa-picture-o"></i> {{trans('product.admin.choose_image')}}
                                            </a>
                                            </span>
                                        </div>
                                            @if ($errors->has('avatar'))
                                                <span class="help-block">
                                                    {{ $errors->first('avatar') }}
                                                </span>
                                            @endif
                                        <div id="preview_avatar" class="img_holder"><img src="{{ old('avatar',$user['avatar']??'') }}"></div>
                                    </div>
                                </div>
                                <div class="form-group   {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-sm-2  control-label">{{ trans('user.password') }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            
                                            <input type="password" id="password" name="password" value="{{ old('password')??'' }}" class="form-control password" placeholder="" />
                                        </div>
                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    {{ $errors->first('password') }}
                                                </span>
                                            @else
                                                @if ($user)
                                                    <span class="help-block">
                                                        {{ trans('user.admin.keep_password') }}
                                                    </span>
                                                @endif
                                            @endif
                                    </div>
                                </div>

                                <div class="form-group   {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label for="password" class="col-sm-2  control-label">{{ trans('user.password_confirmation') }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            
                                            <input type="password" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation')??'' }}" class="form-control password_confirmation" placeholder="" />
                                        </div>
                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">
                                                    {{ $errors->first('password_confirmation') }}
                                                </span>
                                            @else
                                                @if ($user)
                                                    <span class="help-block">
                                                        {{ trans('user.admin.keep_password') }}
                                                    </span>
                                                @endif
                                            @endif
                                    </div>
                                </div>

                                <!-- Select Role -->
                                <div class="form-group  {{ $errors->has('roles') ? ' has-error' : '' }}">
                                    @php
                                    $listRoles = [];
                                        $old_roles = old('roles',($user)?$user->roles->pluck('id')->toArray():'');
                                        if(is_array($old_roles)){
                                            foreach($old_roles as $value){
                                                $listRoles[] = (int)$value;
                                            }
                                        }
                                    @endphp
                                    <label for="roles" class="col-sm-2  control-label">{{ trans('user.admin.select_roles') }}</label>
                                    <div class="col-sm-8">
                                        <select required class="form-control input-sm roles select2" data-placeholder="{{ trans('user.admin.select_roles') }}" style="width: 100%;" name="roles[]" >
                                            <option value=""></option>
                                            @foreach ($roles as $k => $v)
                                                @if(in_array($k, [1,2,10]))
                                                    <option value="{{ $k }}"  {{ (count($listRoles) && in_array($k, $listRoles))?'selected':'' }}>{{ $v }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                            @if ($errors->has('roles'))
                                                <span class="help-block">
                                                    {{ $errors->first('roles') }}
                                                </span>
                                            @endif
                                    </div>
                                    
                                </div>

                                <div class="form-group">
                                    <label for="roles" class="col-sm-2  control-label">{{ trans('user.account_type') }}</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-sm roles select2" data-placeholder="{{ trans('user.account_type') }}"  style="width: 100%;" name="is_demo_account" >
                                            <option value=""></option>
                                            @foreach (ACCOUNT_TYPE as $keyAccount => $valueAccount)
                                                <option value="{{ $keyAccount }}" {{ old('is_demo_account') === $keyAccount ? 'selected' : '' }}  >{{ $valueAccount }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            


                            <!-- /.box-body -->

                            <div class="form-group">
                                    @csrf
                        
                                <div class="col-md-12">
                                    <div class="btn-group pull-right">
                                        <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                                    </div>
                                    
                                    <div class="btn-group pull-right" style="margin-right: 20px">
                                        <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
                                    </div>
                                </div>
                            </div>

                            <!-- /.box-footer -->
                            <br><br>
                        </form>
                    </div>
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
    $("[name='top'],[name='status'], [name='auto_update_tier2']").bootstrapSwitch();
</script>

<script type="text/javascript">

$(document).ready(function() {
    $('.select2').select2()
});



</script>

@endpush
