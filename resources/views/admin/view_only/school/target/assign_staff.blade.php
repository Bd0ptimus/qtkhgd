@extends('layouts.contentLayoutMaster')

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

                            <!-- Target details -->
                            <div class="row">
                                <input type="hidden" name="school_id" value="{{ $school->id }}">    
                                <!-- Title -->
                                <div class="col-sm-6 {{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title" class="control-label">Tên chỉ tiêu<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input readonly required type="text" name="title"
                                               value="{{ old('title',$target['title']??'')}}"
                                               class="form-control title" placeholder="Tên chỉ tiêu"/>
                                    </div>
                                    @if ($errors->has('title'))
                                        <span class="help-block text-danger">
                                            <i class="fa fa-info-circle"></i> {{ $errors->first('title') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Type -->
                                <div class="col-sm-6 {{ $errors->has('type') ? ' has-error' : '' }}">
                                    <label for="type" class="control-label">Loại chỉ tiêu</label>
                                    <div class="input-group">
                                        <select disabled required class="form-control input-sm select2" data-placeholder="Chọn loại chỉ tiêu" style="width: 100%;" name="type">
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
                                        <textarea readonly type="text" name="description"
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
                                        <textarea readonly type="text" name="solution"
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
                            <h4>Giao nhiệm vụ cho giáo viên</h4> <input type="checkbox" id="selectAll"/> <strong>Chọn toàn bộ giáo viên</strong>
                            <hr>
                            <!-- List Staff -->
                            <div class="row">
                                @foreach($school->staffs as $staff)
                                    @php $filtered = collect($staffTargets)->filter(function ($value, $key) use($staff){
                                        return $value->staff_id == $staff->id;
                                    }); @endphp

                                    <div class="col-md-3">
                                        <input @if(count($filtered)) checked @endif name="assigns[{{$staff->id}}]" type="checkbox" value="{{ $staff->id }}"/>
                                        <span>{{ $staff->fullname }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        @csrf
                        <br/>
                        <div class="col-md-8 pull-right">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.submit') }}</button>
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

            $('#selectAll').on('click', function(){
                $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
            });
        });
    </script>
@endpush
