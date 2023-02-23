@extends('layouts/contentLayoutMaster')
@section('title', $title)
@section('vendor-style')
    {{-- Vendor Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/ag-grid/ag-grid.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/ag-grid/ag-theme-material.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/aggrid.css')) }}">
@endsection
@section('main')
    {{-- Statistics card section start --}}
    <section id="horizontal-vertical">
        <div class="card">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                        <form action="{{ route('admin.school.create_student',$schools[0]->id) }}" method="post" class="form-row" id="form-main">
                              <div class="form-group col-md-6 {{ $errors->has('fullname') ? ' has-error' : '' }}">
                                    <label for="fullname" class="control-label">Họ và tên</label>
                                    <input required type="text" id="fullname"  name="fullname"  value="{{ old('fullname',$student['fullname']??'') }}" class="form-control" placeholder="" />
                                    @if ($errors->has('fullname'))
                                        <span class="help-block"  style="color:red">
                                            {{ $errors->first('fullname') }}
                                        </span>
                                    @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('dob') ? ' has-error' : '' }}">
                                    <label for="dob" class="control-label">Ngày sinh</label>
                                          <input required type="date" id="dob" name="dob"  value="{{ old('dob',$student['dob']??'') }}" class="form-control" placeholder="" />

                                          @if ($errors->has('dob'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('dob') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('gender') ? ' has-error' : '' }}">
                                    <label for="gender" class="control-label">Giới tính</label>                   
                                        <select required class="form-control" name="gender">
                                          @foreach(\App\Models\Student::GENDER as $value => $position)
                                              <option value="{{$value}}" {{ $value == $student->gender ? 'selected' : '' }}>{{ $position }}</option>
                                          @endforeach
                                        </select>
                                     
                                          @if ($errors->has('gender'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('gender') }}
                                              </span>
                                          @endif
                              </div>

                              

                              <div class="form-group col-md-6 {{ $errors->has('ethnic') ? ' has-error' : '' }}">
                                    <label for="ethnic" class="control-label">Dân tộc</label>
                                  
                                          
                                        <select class="form-control" name="ethnic">
                                          @foreach(\App\Models\Student::ETHNICS as $value => $position)
                                              <option value="{{$value}}" {{ $value == $student->ethnic ? 'selected' : '' }}>{{ $position }}</option>
                                          @endforeach
                                        </select>
                                   
                                          @if ($errors->has('ethnic'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('ethnic') }}
                                              </span>
                                          @endif
                              </div>


                              <div class="form-group col-md-6 {{ $errors->has('religion') ? ' has-error' : '' }}">
                                    <label for="religion" class="control-label">Tôn giáo</label>
                                  
                                          
                                        <select class="form-control" name="religion">
                                          @foreach(\App\Models\Student::RELIGIONS as $value => $position)
                                              <option value="{{$value}}" {{ $value == $student->religion ? 'selected' : '' }}>{{ $position }}</option>
                                          @endforeach
                                        </select>                                      

                                          @if ($errors->has('religion'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('religion') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('nationality') ? ' has-error' : '' }}">
                                    <label for="nationality" class="control-label">Quốc tịch</label>
                                  
                                        <select class="form-control" name="religion">
                                          @foreach(\App\Models\Student::NATIONALITIES as $value => $position)
                                              <option value="{{$value}}" {{ $value == 1? 'selected' : '' }}>{{ $position }}</option>
                                          @endforeach
                                        </select>

                                          @if ($errors->has('nationality'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('nationality') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('address') ? ' has-error' : '' }}">
                                    <label for="address" class="control-label">Địa chỉ</label>
                                          <input required type="text" id="address" name="address" value="{{ old('address',$student['address']??'') }}" class="form-control" placeholder="" />

                                          @if ($errors->has('address'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('address') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('class_id') ? ' has-error' : '' }}">
                                    <label for="class_id" class="control-label">Lớp học</label>                   
                                    <select required class="form-control" name="class_id">
                                        <option value=""></option>
                                        @foreach($classes as $class)
                                            <option value="{{$class->id}}" {{ $class->id == $student->class_id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                        @endforeach
                                    </select>
                                        @if ($errors->has('gender'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('gender') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('father_name') ? ' has-error' : '' }}">
                                    <label for="father_name" class="control-label">Họ tên Bố</label>
                                          <input type="text"   id="father_name" name="father_name" value="{{ old('father_name',$student['father_name']??'')}}" class="form-control" placeholder="" />

                                          @if ($errors->has('father_name'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('father_name') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('father_phone') ? ' has-error' : '' }}">
                                    <label for="father_phone" class="control-label">Số điện thoại Bố</label>
                                          <input type="text"   id="father_phone" name="father_phone" value="{{ old('father_phone',$student['father_phone']??'')}}" class="form-control" placeholder="" />

                                          @if ($errors->has('father_phone'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('father_phone') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('father_email') ? ' has-error' : '' }}">
                                    <label for="father_email" class="control-label">Email Bố</label>
                                          <input type="text"   id="father_email" name="father_email" value="{{ old('father_email',$student['father_email']??'')}}" class="form-control" placeholder="" />

                                          @if ($errors->has('father_email'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('father_email') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('mother_name') ? ' has-error' : '' }}">
                                    <label for="mother_name" class="control-label">Họ tên Mẹ</label>
                                          <input type="text"  id="mother_name" name="mother_name" value="{{ old('mother_name',$student['mother_name']??'')}}" class="form-control" placeholder="" />

                                          @if ($errors->has('mother_name'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('mother_name') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('mother_phone') ? ' has-error' : '' }}">
                                    <label for="mother_phone" class="control-label">Số điện thoại Mẹ</label>
                                          <input type="text"   id="mother_phone" name="mother_phone" value="{{ old('mother_phone',$student['mother_phone']??'')}}" class="form-control" placeholder="" />

                                          @if ($errors->has('mother_phone'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('mother_phone') }}
                                              </span>
                                          @endif
                              </div>
                              <div class="form-group col-md-6 {{ $errors->has('mother_email') ? ' has-error' : '' }}">
                                    <label for="mother_email" class="control-label">Email Mẹ</label>
                                          <input type="text"   id="mother_email" name="mother_email" value="{{ old('mother_email',$student['mother_email']??'')}}" class="form-control" placeholder="" />

                                          @if ($errors->has('mother_email'))
                                              <span class="help-block"  style="color:red">
                                                  {{ $errors->first('mother_email') }}
                                              </span>
                                          @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('disabilities') ? ' has-error' : '' }}">
                                    <label for="disabilities" class="control-label">Dạng khuyết tật</label>                   
                                    <select class="form-control" name="disabilities">
                                        <option value=""></option>
                                        @foreach(\App\Models\Student::DISABILITIES as $value => $position)
                                            <option value="{{$value}}" {{ $value == $student->disabilities ? 'selected' : '' }}>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    
                                        @if ($errors->has('disabilities'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('disabilities') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('fptp') ? ' has-error' : '' }}">
                                    <label for="fptp" class="control-label">Diện chính sách</label>                   
                                    <select class="form-control" name="fptp">
                                        @foreach(\App\Models\Student::FPTP as $value => $position)
                                            <option value="{{$value}}" {{ $value == $student->fptp ? 'selected' : '' }}>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    
                                        @if ($errors->has('fptp'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('fptp') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('child_no') ? ' has-error' : '' }}">
                                    <label for="child_no" class="control-label">Con thứ</label>
                                    <input type="number" min="0"  id="child_no" name="child_no" value="{{ old('child_no',$student['child_no']??'')}}" class="form-control" placeholder="" />

                                    @if ($errors->has('child_no'))
                                        <span class="help-block"  style="color:red">
                                            {{ $errors->first('child_no') }}
                                        </span>
                                    @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('total_childs') ? ' has-error' : '' }}">
                                    <label for="total_childs" class="control-label">Tổng số con</label>
                                    <input type="number" min="0"  id="total_childs" name="total_childs" value="{{ old('total_childs',$student['total_childs']??'')}}" class="form-control" placeholder="" />

                                    @if ($errors->has('total_childs'))
                                        <span class="help-block"  style="color:red">
                                            {{ $errors->first('total_childs') }}
                                        </span>
                                    @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('health_history') ? ' has-error' : '' }}">
                                    <label for="health_history" class="control-label">Tiền sử sức khỏe</label>                   
                                    <select class="form-control" name="health_history">
                                        @foreach(\App\Models\Student::HEALTH_HISTORY as $value => $position)
                                            <option value="{{$value}}" {{ $value == $student->health_history ? 'selected' : '' }}>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    
                                        @if ($errors->has('health_history'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('health_history') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('born_history') ? ' has-error' : '' }}">
                                    <label for="born_history" class="control-label">Sản khoa</label>                   
                                    <select class="form-control" name="born_history">
                                        @foreach(\App\Models\Student::BORN_HISTORY as $value => $position)
                                            <option value="{{$value}}" {{ $value == $student->born_history ? 'selected' : '' }}>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    
                                        @if ($errors->has('born_history'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('born_history') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('disease_history') ? ' has-error' : '' }}">
                                    <label for="disease_history" class="control-label">Tiền sử bệnh tật</label>                   
                                    <select class="form-control" name="disease_history">
                                        @foreach(\App\Models\Student::DISEASE_HISTORY as $value => $position)
                                            <option value="{{$value}}" {{ $value == $student->disease_history ? 'selected' : '' }}>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    
                                        @if ($errors->has('disease_history'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('disease_history') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('treating_disease') ? ' has-error' : '' }}">
                                    <label for="treating_disease" class="control-label">Bệnh đang điều trị</label>                   
                                    <select class="form-control" name="treating_disease">
                                        @foreach(\App\Models\Student::TREATING_DISEASE as $value => $position)
                                            <option value="{{$value}}" {{ $value == $student->treating_disease ? 'selected' : '' }}>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    
                                        @if ($errors->has('treating_disease'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('treating_disease') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="row form-group col-md-12">
                                <div class="col"><hr></div>
                                <div class="col-auto"><h3>Thông tin tiêm chủng</h3></div>
                                <div class="col"><hr></div>
                              </div>
 
                              <div class="form-group col-md-6 {{ $errors->has('tc_bcg') ? ' has-error' : '' }}">
                                    <label for="tc_bcg" class="control-label">TC BCG</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_bcg" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div> 
                                        @if ($errors->has('tc_bcg'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_bcg') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_bhhguv_m1') ? ' has-error' : '' }}">
                                    <label for="tc_bhhguv_m1" class="control-label">TC Ho gà, bạch hầu, uốn vãn M1</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_bhhguv_m1" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_bhhguv_m1'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_bhhguv_m1') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_bhhguv_m2') ? ' has-error' : '' }}">
                                    <label for="tc_bhhguv_m2" class="control-label">TC Ho gà, bạch hầu, uốn vãn M2</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_bhhguv_m2" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_bhhguv_m2'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_bhhguv_m2') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_bhhguv_m3') ? ' has-error' : '' }}">
                                    <label for="tc_bhhguv_m3" class="control-label">TC Ho gà, bạch hầu, uốn vãn M3</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_bhhguv_m3" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_bhhguv_m3'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_bhhguv_m3') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_bailiet_m1') ? ' has-error' : '' }}">
                                    <label for="tc_bailiet_m1" class="control-label">TC Bại liệt M1</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_bailiet_m1" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_bailiet_m1'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_bailiet_m1') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_bailiet_m2') ? ' has-error' : '' }}">
                                    <label for="tc_bailiet_m2" class="control-label">TC Bại liệt M2</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_bailiet_m2" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_bailiet_m2'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_bailiet_m2') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_bailiet_m3') ? ' has-error' : '' }}">
                                    <label for="tc_bailiet_m3" class="control-label">TC Bại liệt M3</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_bailiet_m3" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_bailiet_m3'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_bailiet_m3') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_viemganb_m1') ? ' has-error' : '' }}">
                                    <label for="tc_viemganb_m1" class="control-label">TC Viêm gan B M1</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_viemganb_m1" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_viemganb_m1'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_viemganb_m1') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_viemganb_m2') ? ' has-error' : '' }}">
                                    <label for="tc_viemganb_m2" class="control-label">TC Viêm gan B M2</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_viemganb_m2" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_viemganb_m2'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_viemganb_m2') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_viemganb_m3') ? ' has-error' : '' }}">
                                    <label for="tc_viemganb_m3" class="control-label">TC Viêm gan B M3</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_viemganb_m3" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_viemganb_m3'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_viemganb_m3') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_soi') ? ' has-error' : '' }}">
                                    <label for="tc_soi" class="control-label">TC Sởi </label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_soi" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_soi'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_soi') }}
                                            </span>
                                        @endif
                              </div>

                              <div class="form-group col-md-6 {{ $errors->has('tc_viemnaonb') ? ' has-error' : '' }}">
                                    <label for="tc_viemnaonb" class="control-label">TC Viêm não NB</label>                   
                                    <div class="col-md-6">
                                      @foreach(\App\Models\Student::TC_STATUS as $value => $position)
                                      <div class="custom-control custom-radio custom-control-inline">
                                          <input class="form-check-input" type="radio"  name="tc_viemnaonb" value="{{$value}}" {{ $value == 1 ? 'checked' : '' }}>{{ $position }}</input>
                                      </div>
                                        @endforeach
                                    </div>
                                    
                                        @if ($errors->has('tc_viemnaonb'))
                                            <span class="help-block"  style="color:red">
                                                {{ $errors->first('tc_viemnaonb') }}
                                            </span>
                                        @endif
                              </div>

                        <!-- /.box-body -->
                          <div class="box-footer col-md-12">
                                  @csrf
                            <div class="col-md-2">
                            </div>
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
            </div>
        </div>
    </section>

@endsection