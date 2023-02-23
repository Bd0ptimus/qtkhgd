@extends('layouts.contentLayoutMaster')
@php
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => "Danh sách phiếu bài tập về nhà", 'link' => route('homework_sheet.index')],
        ['name' => $title_description ?? ''],
    ];
    $schoolLevels = SCHOOL_TYPES;
@endphp
@section('title', $title_description ?? '')

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/plugins/forms/validation/form-validation.css')}}">
@endpush

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="form-group">
                                <!-- File Bài Tập Về Nhà-->
                                <div id="form-group-file" class="col-sm-12 {{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Chọn file bài tập về nhà <sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="file" multiple id="file" name="files[]" />
                                        @if(isset($homeworkSheet) && count($homeworkSheet->attachments) > 0)
                                        @forelse ($homeworkSheet->attachments as $attchment)
                                        <div class="list_attachments" id="file_list-{{$attchment->id}}">                                            
                                            <a class="ml-1" href="{{ route('homework_sheet.download_attach_file', ['attachmentId' => $attchment->id]) }}">{{$attchment->name}}</a>
                                            <a type="button" class="delete-item"
                                             data-url="{{ route('homework_sheet.delete_file', ['attachmentId' => $attchment->id]) }}"
                                             href="#"><i class="fa fa-times"></i></a>
                                         </div>
                                        @empty
                                        @endforelse
                                        @endif
                                    </div>
                                    @if ($errors->has('file'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('file') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-2  control-label">Tên phiếu bài tập<sup class="text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="name" name="name"
                                               value="{{ old('name',$homeworkSheet['name']??'')}}"
                                               class="form-control name" placeholder="Tên bài giảng"/>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('assemblage') ? ' has-error' : '' }}">
                                <label for="assemblage" class="col-sm-2  control-label">Thuộc bộ sách</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <select class="form-control input-sm select2"
                                                    data-placeholder="Bộ sách" style="width: 100%;" name="assemblage">
                                                    <option>Chọn bộ sách</option>
                                                @foreach (BOOK_ASSEMBLAGES as $value)
                                                    <option value="{{ $value }}" {{ (isset($homeworkSheet) && $homeworkSheet->assemblage == $value )?'selected':'' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    @if ($errors->has('assemblage'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('assemblage') }}
                                        </span>
                                    @endif
                                </div>
                            </div>


                            {{-- select level --}}
                            <div class="form-group  {{ $errors->has('level') ? ' has-error' : '' }}">
                                <label for="subject" class="col-sm-2  control-label">Cấp học <sup class="text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm level select2" id="school-type" onchange="selectChange(this.value, 'level')"
                                            data-placeholder="Cấp học" style="width: 100%;" name="level">
                                        <option value=""></option>
                                        @foreach ($schoolLevels as $index => $level)
                                            <option value="{{ $index }}"{{ (!empty($schoolType) && $schoolType == $index)?'selected':'' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('level'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('level') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- //select level --}}

                            {{-- select grades --}}
                            <div class="form-group  {{ $errors->has('grade') ? ' has-error' : '' }}">
                                <label for="subject" class="col-sm-2  control-label">Khối học <sup class="text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm grade select2" onchange="selectChange(this.value, 'grade')"
                                            data-placeholder="Khối học" style="width: 100%;" name="grade">
                                        <option value=""></option>
                                        @foreach ($grades as $index => $grade)
                                            <option value="{{ $index }}" {{ (isset($homeworkSheet) && $index == $homeworkSheet['grade'])?'selected':'' }}>{{ $grade }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('grade'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('grade') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- //select grades --}}

                            {{-- select subject --}}
                            <div class="form-group  {{ $errors->has('subject_id') ? ' has-error' : '' }}">
                                <label for="subject" class="col-sm-2  control-label">Môn học <sup class="text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm subject select2"
                                            data-placeholder="Môn học" style="width: 100%;" name="subject_id">
                                        <option value=""></option>
                                        @foreach ($subjects as $index => $subject)
                                            <option value="{{ $subject->id }}" {{ (isset($homeworkSheet) && $homeworkSheet->subject_id == $subject->id)?'selected':'' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('subject_id'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('subject_id') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- //select subject --}}

                            <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-2  control-label">Nội dung phiếu bài tập <sup class="text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <textarea class="form-control content-homework description" name="content" rows="20">{{ old('content',$homeworkSheet['content'] ?? '')}}</textarea>
                                    </div>
                                    @if ($errors->has('content'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('content') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        @csrf
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
                            </div>

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2()
            $('#file').on('change', function(e) {
                const file = $('#file')[0].files[0];
                const message = `File bài giảng không thể lớn hơn 100000 kilobytes.`;
                if (file.size > 100000000) {// Byte
                    $('#form-group-file').addClass('has-error');
                    $('#form-group-file').children('span.help-block').remove();
                    $('#form-group-file').append(
                        `<span class="help-block text-danger">
                            <i class="fa fa-info-circle"></i> ${message}
                        </span>
                        `
                    );
                } else {
                    $('#form-group-file').removeClass('has-error');
                    $('#form-group-file').children('span.help-block').remove();
                }
            });
            
            $('.delete-item').on('click', function(e) {   
              e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá file đính kèm này?');
                if (confirmDelete) {
                    let element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {                            
                           element.parents('div.list_attachments').remove();
                            alert('Đã xoá file đính kèm');
                        }
                    });
                }
            });
        });

        function selectChange(value, key) {
            if (!value) return
            $.ajax({
                type: 'GET',
                header: "Content-type: text/plain",
                data: {value, key},
                url: "{{ route('homework_sheet.change.select') }}",
                dataType: 'json',
                success: function(res){
                    if (res.grades) {
                        let data = res.grades
                        let select = $(".grade"), options = '';
                        select.empty();
                        for(let i = 0; i < data.length; i++)
                        {
                            options += "<option value='"+data[i].id+"'>"+ data[i].name +"</option>";
                        }

                        select.append(options);
                    }

                    if (res.subjects) {
                        let data = res.subjects
                        let select = $(".subject"), options = '';
                        select.empty();
                        for(let i = 0; i < data.length; i++)
                        {
                            options += "<option value='"+data[i].id+"'>"+ data[i].name +"</option>";
                        }

                        select.append(options);
                    }

                    if (res.schoolType) {
                        let data = res.schoolType
                        let allLevel = res.schoolTypes
                        let select = $("#school-type"), options = '';
                        select.empty();
                        for(let i = 0; i < allLevel.length; i++)
                        {
                            if (allLevel[i].id === data.id) options += "<option value='"+allLevel[i].id+"' selected='selected'>"+ allLevel[i].name +"</option>";
                            else options += "<option value='"+allLevel[i].id+"'>"+ allLevel[i].name +"</option>";
                        }

                        select.append(options);
                    }
                },
                error: function(res){
                    console.log(res)
                }
            });
        }
    </script>
@endpush
