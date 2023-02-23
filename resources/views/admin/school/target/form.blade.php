@extends('layouts.contentLayoutMaster')
@php
    $breadcrumbs;
@endphp
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ $title_description??'' }}</h2>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="title" class="control-label">Chọn chỉ tiêu từ kho<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <select class="form-control select2 select-target" placeholder="Chọn chỉ tiêu từ kho">
                                                <option>Chọn chỉ tiêu mẫu</option>
                                                @foreach($systemTargets as $systemTarget)
                                                    <option value="{{ $systemTarget->id }}">{{ TARGET_TYPES[$systemTarget->type] ?? ''}} - {{ $systemTarget->title }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 {{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title" class="control-label">Tên chỉ tiêu<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input required type="text" name="title"
                                               value="{{ old('title',$target['title']??'')}}"
                                               class="form-control title" placeholder="Tên chỉ tiêu"/>
                                    </div>
                                    @if ($errors->has('title'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('title') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <br/><br/>

                            <div class="row">
                                <input type="hidden" name="school_id" value="{{ $school->id }}">    
                                <!-- Title -->
                             
                                <div class="col-sm-6 {{ $errors->has('target_index') ? ' has-error' : '' }}">
                                    <label for="target_index" class="control-label">Trọng số</label>
                                    <div class="input-group">
                                        <input  type="number" min='1' max='100' name="target_index" required
                                               value="{{ old('target_index', $target['target_index'] ?? '')}}"
                                               class="form-control title" placeholder="Trọng số"/>
                                    </div>
                                    @if ($errors->has('target_index'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('target_index') }}
                                        </span>
                                    @endif
                                </div>
                                <!-- Type -->
                                <div class="col-sm-6 {{ $errors->has('type') ? ' has-error' : '' }}">
                                    <label for="type" class="control-label">Loại chỉ tiêu</label>
                                    <div class="input-group">
                                        <select required class="form-control input-sm select2" data-placeholder="Chọn loại chỉ tiêu" style="width: 100%;" name="type">
                                            <option value="">Chọn loại chỉ tiêu</option>
                                            @foreach(TARGET_TYPES as $key => $type)
                                                <option value="{{ $key }}" {{ (isset($target) && $target->type == $key) ? 'selected':'' }}>{{$type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Description -->
                                <div class="col-sm-12 {{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description" class="control-label">Mô tả chỉ tiêu</label>
                                    <div class="input-group">
                                        <textarea type="text" name="description"
                                               value="{{ old('description',$target['description']??'')}}"
                                               class="form-control" placeholder="Mô tả chỉ tiêu">{{ old('description',$target['description']??'')}}</textarea>
                                    </div>
                                    @if ($errors->has('description'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('description') }}
                                        </span>
                                    @endif
                                </div>

                                
                                <!-- Solutions -->
                                <div class="col-sm-12 {{ $errors->has('solution') ? ' has-error' : '' }}">
                                    <label for="solution" class="control-label">Giải pháp thực hiện</label>
                                    <div class="input-group">
                                        <textarea type="text" name="solution"
                                               value="{{ old('solution',$target['solution']??'')}}"
                                               class="form-control" placeholder="Giải pháp thực hiện">{{ old('solution',$target['solution']??'')}}</textarea>
                                    </div>
                                    @if ($errors->has('solution'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('solution') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-success btn-add-criteria">+ Thêm tiêu chí cho chỉ tiêu</button>
                                </div>
                            </div>
                            <input type="hidden" value="" name="count_points" id="count-points">
                            <div class="list-criterias">
                              @if (isset($target) && $target->mainPoints()->count() > 0)
                                  @foreach ($target->mainPoints as $point)
                                  <div class="row">     
                                        <div class="col-sm-5">
                                            <label for="title" class="control-label">Tên tiêu chí<sup class="text-danger">*</sup></label>
                                            <div class="input-group">
                                                <input required type="text" name="content_points[{{$point->id}}]"
                                                        value="{{$point->content}}"
                                                        class="form-control" value="" placeholder="Tên tiêu chí" />
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <label for="title" class="control-label">Trọng số<sup class="text-danger">*</sup></label>
                                            <div class="input-group">
                                                <input required name="index_points[{{$point->id}}]"
                                                        value="{{$point->index_point}}"
                                                        class="form-control index-point"  type="number" min='1' max='100' placeholder="Trọng số"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 d-flex align-items-end">
                                            <button class="btn btn-outline-danger remove_field" data-url="{{route('school.target.delete.point',['id' => $school->id, 'pointId' => $point->id])}}" data-check="true" data-id_point="{{$point->id}}" type="button">Remove</button>
                                        </div>
                                    </div>
                                  @endforeach
                              @endif
                            </div>
                          

                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        @csrf
                        <br/>
                        <div class="col-md-8 pull-right">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary btn-submit-target">{{ trans('admin.submit') }}</button>
                            </div>
                            <div class="btn-group" style="margin-left: 20px">
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
            $('.select2').select2();
            $('.select-target').on('change', function(e) {
                $.ajax({
                    method: 'get',
                    url : `{{ route('ajax_get_system_target_by_id') }}?id=${$(this).val()}`,
                    success: function (res) {
                        var data = JSON.parse(res);
                        $('input[name="title"]').val(data.title);
                        $('textarea[name="solution"]').val(data.solution);
                        $('textarea[name="description"]').val(data.description);
                        $('select[name="type"]').val(data.type).change();
                    }
                });
                
            });
            var max_fields      = 5;
            var wrapper   		= $(".list-criterias"); 
            var add_button      = $(".btn-add-criteria");
            var x = {{ isset($target) && $target->mainPoints()->count() > 0 ? $target->mainPoints()->count()+1 : 1 }}; //initlal text box count
            $(add_button).click(function(e) {
                e.preventDefault();
                if(x <= max_fields){ //max input box allowed
                    $("#count-points").val(x);
                    x++; //text box increment
                    $(wrapper).append(
                         `<div class="row">     
                            <div class="col-sm-5">
                                <label for="title" class="control-label">Tên tiêu chí<sup class="text-danger">*</sup></label>
                                <div class="input-group">
                                    <input required type="text" name="content_points[]"
                                            value=""
                                            class="form-control title" placeholder="Tên tiêu chí"/>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <label for="title" class="control-label">Trọng số<sup class="text-danger">*</sup></label>
                                <div class="input-group">
                                    <input required name="index_points[]"
                                            value=""
                                            class="form-control index-point"  type="number" min='1' max='100' placeholder="Trọng số"/>
                                </div>
                            </div>
                            <div class="col-sm-2 d-flex align-items-end">
                                <button class="btn btn-outline-danger remove_field" data-check="false" type="button">Remove</button>
                            </div>
                        </div>`
                    ); 
                }
            });
            $(wrapper).on("click",".remove_field", function(e){ 
                e.preventDefault(); 
                const check = $(this).data("check");
                if(check) {
                   const id_point = $(this).data("id_point");
                   const isConfirm = confirm("Bạn có đồng ý xóa tiêu chí có sẵn này không ?");
                   if(isConfirm)
                    {
                        $.ajax({
                        url: $(this).data('url'),
                        method: 'POST',
                        dataType: "json",
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                          if(res.status == 200) return;
                        }
                    });
                        $(this).parent('div').parent('div').remove(); 
                    }
                }
                else {
                    $(this).parent('div').parent('div').remove(); 
                }
                x--;
                $("#count-points").val(x-1);
            })


        
            // $("#form-main").submit(function( event ) {
            //     $check = checkTotalInput();
            //     if(!$check) {
            //         alert("Tổng trọng số các tiêu chí phải bằng 100%");
            //     }
            //     return $check;
            // });


          $("#form-main").on("keyup", ".index-point", function() {
            $(this)[0].setAttribute("value", $(this).val());
          })
        });
   
        // function checkTotalInput() {
        //     if($(".index-point").length > 0) {
        //         let total = 0;
        //         $.each($(".index-point"), function (index, inputElement) { 
        //              total += parseInt(inputElement.getAttribute("value"));
        //              console.log(inputElement);
        //         });
        //         if(total == 100) return true;
        //         return false;
        //     }
        //     return true;
        // }
    </script>
@endpush
