@extends('layouts.contentLayoutMaster')

@php
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => "Danh sách Ebook", 'link' => route('ebooks.index')],
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

                            <div class="row">
                                <!-- File -->
                                <div class="col-sm-12 {{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Chọn file sách<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="file" id="file" name="file"></input>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <!-- Tên sách -->
                                <div class="col-sm-6 {{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Tên sách<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" name="name"
                                               value="{{ old('name',$ebook['name']??'')}}"
                                               class="form-control name" placeholder="Tên sách"/>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Nhà phát hành -->
                                <div class="col-sm-6 {{ $errors->has('publisher') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Nhà xuất bản<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" name="publisher"
                                               value="{{ old('publisher',$ebook['publisher']??'')}}"
                                               class="form-control publisher" placeholder="Nhà xuất bản"/>
                                    </div>
                                    @if ($errors->has('publisher'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('publisher') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <!-- Tên sách -->
                                <div class="col-sm-6 {{ $errors->has('authors') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Tác giả<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" name="authors"
                                               value="{{ old('authors',$ebook['authors']??'')}}"
                                               class="form-control authors" placeholder="Tên tác giả"/>
                                    </div>
                                    @if ($errors->has('title'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('authors') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Nhà phát hành -->
                                <div class="col-sm-6 {{ $errors->has('assemblage') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Thuộc bộ sách</label>
                                    <div class="input-group">

                                        <select class="form-control input-sm select2"
                                                data-placeholder="Thuộc bộ sách" style="width: 100%;" name="assemblage">
                                            <option value="">Chọn bộ sách</option>
                                            @foreach (BOOK_ASSEMBLAGES as $value)
                                                <option value="{{ $value }}" {{ (isset($ebook) && $ebook->assemblage == $value )?'selected':'' }}>{{ $value }}</option>
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

                         
                            <div class="row">
                                <!-- Cấp học -->
                                <div class="col-sm-6 form-group  {{ $errors->has('level') ? ' has-error' : '' }}">
                                    <label for="level" class="ccontrol-label">Cấp học <sup class="text-danger">*</sup></label>
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

                                <!-- Khối học -->
                                <div class="col-sm-6 {{ $errors->has('grade') ? ' has-error' : '' }}">
                                    <label for="grade" class="control-label">Khối học <sup class="text-danger">*</sup></label>
                                    <select class="form-control input-sm grade select2" onchange="selectChange(this.value, 'grade')"
                                            data-placeholder="Khối học" style="width: 100%;" name="grade">
                                        <option value=""></option>
                                        @foreach ($grades as $index => $grade)
                                            <option value="{{ $index }}" class="value-school-type"
                                                    {{ (isset($ebook) && $index == $ebook['grade'])?'selected':'' }}>
                                                {{ $grade }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('grade'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('grade') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            
                            <div class="row">
                                <!-- Môn học -->
                                <div class="col-sm-6 {{ $errors->has('subject_id') ? ' has-error' : '' }}">
                                    <label for="subject" class="control-label">Môn học <sup class="text-danger">*</sup></label>
                                    <select class="form-control input-sm subject select2"
                                            data-placeholder="Môn học" style="width: 100%;" name="subject_id">
                                        <option value=""></option>
                                        @foreach ($subjects as $index => $subject)
                                            <option value="{{ $subject->id }}" {{ (isset($ebook) && $ebook->subject_id == $subject->id)?'selected':'' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('subject_id'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('subject_id') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Số trang -->
                                <div class="col-sm-6 {{ $errors->has('size') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Kích thước</label>
                                    <div class="input-group">
                                        <input type="text" name="size"
                                               value="{{ old('size',$ebook['size']??'')}}"
                                               class="form-control size" placeholder="Kích thước"/>
                                    </div>
                                    @if ($errors->has('size'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('size') }}
                                        </span>
                                    @endif
                                </div>

                            </div>
                            <div class="row">
                                <!-- Loại sách -->
                                <div class="col-sm-6 {{ $errors->has('ebook_categories') ? ' has-error' : '' }}">
                                    <label for="ebook_categories" class="control-label">Loại sách <sup class="text-danger">*</sup></label>
                                    <select class="form-control input-sm ebook_categories select2" multiple="multiple"
                                            data-placeholder="Loại sách" style="width: 100%;" name="ebook_categories[]">
                                        <option value=""></option>
                                        @foreach ($ebookCategories as $ebookCategory)
                                            <option value="{{ $ebookCategory->id }}"
                                                {{ $availableEbookCategoryIds->contains($ebookCategory->id) ? 'selected' : '' }}
                                                >{{ $ebookCategory->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('ebook_categories'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('ebook_categories') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                         
                            <div class="row {{ $errors->has('description') ? ' has-error' : '' }}">
                               
                                <div class="col-sm-12">
                                    <label for="description" class="control-label">Mô tả <sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <textarea class="form-control content-exercise-question description" name="description" rows="20">{{ old('description',$ebook['description'] ?? '')}}</textarea>
                                    </div>
                                    @if ($errors->has('description'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('description') }}
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
            $('.select2').select2();
        });

        function selectChange(value, key) {
            if (!value) return
            $.ajax({
                type: 'GET',
                header: "Content-type: text/plain",
                data: {value, key},
                url: "{{ route('exercise_question.change.select') }}",
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
