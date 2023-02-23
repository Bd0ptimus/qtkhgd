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
                    <strong>Bước 1. </strong> Tải <a href="/demo_imports/file_import_du_lieu_dia_chinh.xls">File mẫu</a> về máy. <br>
                    <strong>Bước 2. </strong> Điền đầy đủ thông tin theo hướng dẫn. Lưu file sau khi chỉnh sửa.<br>
                    <strong>Bước 3. </strong> Click vào nút "Choose file" để upload file dữ liêu.<br>
                    <strong>Bước 4. </strong> Click submit để tiến hành import thông tin.<br>
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
                        <h4 class="card-title">Chọn file dữ liệu danh mục địa chính</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form enctype="multipart/form-data" method="POST"
                                  action="{{ route('sysconf.import_locations') }}"
                                  class="form-horizontal">
                                @csrf
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="file" id="file_upload" name="file_upload"></input>
                                    </div>
                                </div>
                                @if ($errors->has('file_upload'))
                                    <span class="help-block" style="color:red">
                                        {!! $errors->first('file_upload') !!}
                                    </span>
                                @endif
                                <button class="main-action btn btn-success btn-flat" type="submit">Tiến hành nhập liệu</button>
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