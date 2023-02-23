@extends('layouts.contentLayoutMaster')
@php
    
@endphp

@section('title', $title)
@section('main')
    <!-- Dropzone section start -->
    <section id="dropzone-examples">
        <!-- warnings and info alerts starts -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <strong>Bước 1. </strong> Tải file dữ liệu học sinh từ PM-S <br>
                    <strong>Bước 2. </strong> Mở file danh sách học sinh, xoá 3 dòng đầu đi<br>
                    <strong>Bước 3. </strong> Chọn file danh sách trên phần mềm và tiến hành Import<br>
                    <strong>Bước 4. </strong> Sau khi import, vào lại danh sách lớp của trường, chỉnh sửa lại thông tin khối cho các lớp học vừa được tạo.<br>
                </div>
                <div class="alert alert-info" role="alert">
                    <strong>Lưu ý: </strong> Khi có bất kỳ lỗi nào, hệ thống sẽ hiện cảnh báo. Bạn vui lòng chỉnh sửa
                    lại dữ liệu theo hướng dẫn của cảnh báo và tiến hành upload lại.
                </div>
            </div>
        </div>
        <!-- warnings and info alerts ends -->

        <!-- single file upload starts -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Chọn file dữ liệu học sinh từ PM-S</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form enctype="multipart/form-data" method="POST"
                                  action=""
                                  class="form-horizontal">
                                @csrf
                                <div class="form-group">
                                    <div class="input-group">
                                        <input required type="file" id="file_upload" name="file_upload"></input>
                                    </div>
                                </div>
                                @if ($errors->has('file_upload'))
                                    <span class="help-block" style="color:red">
                                        {!! $errors->first('file_upload') !!}
                                    </span>
                                @endif
                                <button class="btn btn-success btn-flat import-form" type="submit">Tiến hành nhập liệu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- single file upload ends -->

    </section>
    <!-- // Dropzone section end -->
@endsection