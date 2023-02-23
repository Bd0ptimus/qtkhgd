@extends('layouts.contentLayoutMaster')
@section('main')
<div class="box-tools">
    <div class="btn-group pull-right" style="margin-right: 5px">
        <a href="{{ route('admin_user.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{trans('admin.back_list')}}</span></a>
    </div>
</div>
<div class="table-responsive" >
  <table class="table table-striped" style="text-align:center"  align="center" border="1" border-collapse="collapse" table-layout="fixed" width= "50%">
    <tr>
      <th style="text-align:center">Type</th>
      <th style="text-align:center">Value</th>
    </tr>
    <tr>
      <td >Tên đầy đủ</td>
      <td >{!! $user->name??'' !!}</td>
    </tr>
    <tr>
      <td>Số điện thoại</td>
      <td >{!! $user->phone_number??'' !!}</td>
    </tr>
    <tr>
      <td>Tên đăng nhập</td>
      <td >{!! $user->username??'' !!} </td>
    </tr>
    <tr>
      <td>Vai trò</td>
      <td >{!! $role??'' !!} </td>
    </tr>
    <tr>
      <td>Trạng thái</td>
      <td >{!! $status??'' !!} </td>
    </tr>
</table>
</div>                  
@endsection