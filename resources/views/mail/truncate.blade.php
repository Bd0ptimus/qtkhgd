@extends('mail.layout')

@section('main')
  <h1 style="color:red">Bạn đang thực hiện lệnh xoá toàn bộ dữ liệu trên hệ thống. Xin lưu ý. Khi dữ liệu bị xoá, bạn sẽ không thể khôi phục lại.</h1>
  <h1 style="color:red">Mã xác thực có hiệu lực trong vòng 1 phút. Mã xác thực của bạn là: <b>{!! $code !!}</b></h1>
@endsection