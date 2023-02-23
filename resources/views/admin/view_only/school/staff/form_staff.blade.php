@extends('layouts.contentLayoutMaster')

@php
    $breadcrumbs = [
    ['name' => trans('admin.home'), 'link' => route('admin.home')],
    ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
    ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
    ['name' => 'Danh sách nhân viên theo trường', 'link' => route('admin.school.view_staff_list', ['id' => $school->id])],
    ];
@endphp

@section('title', $title)

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/plugins/forms/validation/form-validation.css')}}">
@endpush

@section('main')
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <form method="POST" action="{{ $routing }}" class="form">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="school_branch_id">Điểm trường</label>
                                                <select class="custom-select form-control required select2"
                                                        name="school_branch_id">
                                                    @foreach($school->branches as $branch)
                                                        <option {{ strval($branch->id) === strval(old('school_branch_id', $staff['school_branch_id'] ?? '')) ? 'selected' : '' }} value="{{$branch->id}}">{{ $branch->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('fullname') ? ' error' : '' }}">
                                                <label for="fullname">Tên đầy đủ</label>
                                                <input type="text" class="form-control" placeholder="Tên đầy đủ"
                                                       name="fullname"
                                                       value="{{ old('fullname', $staff['fullname'] ?? '') }}">
                                                @if ($errors->has('fullname'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('fullname') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('dob') ? ' error' : '' }}">
                                                <label for="dob">Ngày sinh</label>
                                                <input type="date" id="dob" name="dob"
                                                       value="{{ old('dob', !empty($staff) ? $staff->getOriginal('dob') : '') }}"
                                                       class="form-control" placeholder=""/>
                                                @if ($errors->has('dob'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('dob') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="gender">Giới tính</label>
                                                <select class="custom-select form-control required select2"
                                                        name="gender">
                                                    @foreach($data['gender'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('gender', !empty($staff) ? $staff->getOriginal('gender') : '')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="ethnic">Dân tộc</label>
                                                <select class="custom-select form-control required select2"
                                                        name="ethnic">
                                                    @foreach($data['ethnic'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('ethnic', !empty($staff) ? $staff->getOriginal('ethnic') : '')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="religion">Tôn giáo</label>
                                                <select class="custom-select form-control required select2"
                                                        name="religion">
                                                    @foreach($data['religion'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('religion', !empty($staff) ? $staff->getOriginal('religion') : '')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="nationality">Quốc tịch</label>
                                                <select class="custom-select form-control required select2"
                                                        name="nationality">
                                                    @foreach($data['nationality'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('nationality', !empty($staff) ? $staff->getOriginal('nationality') : 1)) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('address') ? ' error' : '' }}">
                                                <label for="address">Địa chỉ</label>
                                                <input type="text" class="form-control" placeholder="Địa chỉ"
                                                       name="address"
                                                       value="{{ old('address', $staff['address'] ?? '') }}">
                                                @if ($errors->has('address'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('address') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('identity_card') ? ' error' : '' }}">
                                                <label for="identity_card">Chứng minh nhân dân</label>
                                                <input type="text" class="form-control"
                                                       placeholder="Chứng minh nhân dân" name="identity_card"
                                                       value="{{ old('identity_card', $staff['identity_card'] ?? '') }}">
                                                @if ($errors->has('identity_card'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('identity_card') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('phone_number') ? ' error' : '' }}">
                                                <label for="phone_number">Số điện thoại</label>
                                                <input type="text" class="form-control" placeholder="Số điện thoại"
                                                       name="phone_number"
                                                       value="{{ old('phone_number', $staff['phone_number'] ?? '') }}">
                                                @if ($errors->has('phone_number'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('phone_number') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group{{ $errors->has('email') ? ' error' : '' }}">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" placeholder="Email" name="email"
                                                       value="{{ old('email', $staff['email'] ?? '') }}">
                                                @if ($errors->has('email'))
                                                    <div class="help-block">
                                                        <ul role="alert">
                                                            <li>{{ $errors->first('email') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="qualification">Trình độ chuyên môn</label>
                                                <select class="custom-select form-control required select2"
                                                        name="qualification">
                                                    @foreach($data['qualification'] as $value => $label)
                                                        <option {{ strval($label) === strval(old('qualification', $staff['qualification'] ?? '')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="position">Vị trí</label>
                                                <select class="custom-select form-control required select2"
                                                        name="position">
                                                    @foreach($data['position'] as $value => $label)
                                                        <option {{ strval($label) === strval(old('position', $staff['position'] ?? '')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="status">Trạng thái làm việc</label>
                                                <select class="custom-select form-control required select2"
                                                        name="status">
                                                    @foreach($data['status'] as $value => $label)
                                                        <option {{ strval($value) === strval(old('status', !empty($staff) ? $staff->getOriginal('status') : '')) ? 'selected' : '' }} value="{{ $value }}">{{$label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!--
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-2">
                                                    <fieldset>
                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                            <input type="hidden" value="0" name="responsible">
                                                            <input type="checkbox" name="responsible" {{ 1 === old('responsible', $staff['responsible'] ?? 0) ? 'checked' : '' }} value="1">
                                                            <span class="vs-checkbox">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                            <span class="">Chuyên trách</span>
                                                        </div>
                                                    </fieldset>
                                                </li>

                                                <li class="d-inline-block mr-2">
                                                    <fieldset>
                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                            <form>
                                                                <input type="hidden" value="0" name="professional_certificate">
                                                                <input type="checkbox" name="professional_certificate" {{ '1' === strval(old('professional_certificate', $staff['professional_certificate'] ?? 0)) ? 'checked' : '' }} value="1">
                                                                <span class="vs-checkbox">
                                                                    <span class="vs-checkbox--check">
                                                                        <i class="vs-icon feather icon-check"></i>
                                                                    </span>
                                                                </span>
                                                                <span class="">Chứng chỉ hành nghề</span>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ul>
                                        </div>
                                    </div> -->

                                        {{-- select subject --}}
                                        <div class="col-md-6 col-12">
                                            <div class="form-group  {{ $errors->has('subject') ? ' has-error' : '' }}">
                                                @php
                                                    $listSubjects = [];
                                                    $oldSubjects = old('subjects',(isset($staff) ? $staff->staffSubjects->pluck('subject_id')->toArray():''));
                                                        if(is_array($oldSubjects)){
                                                            foreach($oldSubjects as $value){
                                                                $listSubjects[] = (int)$value;
                                                            }
                                                        }
                                                @endphp
                                                <label for="subjects">Các môn học gv giảng dạy</label>
                                                <div>
                                                    <select class="form-control input-sm subject select2"
                                                            multiple="multiple" data-placeholder="Môn học"
                                                            style="width: 100%;" name="subjects[]">
                                                        <option value=""></option>
                                                        @foreach ($subjects as $index => $subject)
                                                            <option value="{{ $subject->id }}" {{ (count($listSubjects) && in_array($subject->id, $listSubjects))?'selected':'' }}>{{ $subject->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('subjects'))
                                                        <span class="help-block">
                                                            {{ $errors->first('subjects') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- //select subject --}}

                                        {{-- select grades --}}
                                        <div class="col-md-6 col-12">

                                            <div class="form-group  {{ $errors->has('subject') ? ' has-error' : '' }}">
                                                @php
                                                    $listGrades = [];
                                                    $oldGrades = old('grades',(isset($staff) ? $staff->staffGrades->pluck('grade')->toArray():''));
                                                        if(is_array($oldGrades)){
                                                            foreach($oldGrades as $value){
                                                                $listGrades[] = (int)$value;
                                                            }
                                                        }
                                                @endphp
                                                <label for="subject">Các khối giáo viên giảng dạy</label>
                                                <div>
                                                    <select class="form-control input-sm subject select2"
                                                            multiple="multiple" data-placeholder="Khối học"
                                                            style="width: 100%;" name="grades[]">
                                                        <option value=""></option>
                                                        @foreach ($grades as $index => $grade)
                                                            <option value="{{ $index }}" {{ (count($listGrades) && in_array($index, $listGrades))?'selected':'' }}>{{ $grade}}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('grades'))
                                                        <span class="help-block">
                                                            {{ $errors->first('subject') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- //select grades --}}

                                        <div class="col-md-4 col-4">
                                            <div class="form-group">
                                                
                                                <input @if(isset($staff) && true == $staff->has_pregnant) checked @endif name='has_pregnant' type="checkbox"/><label for="status">Đang mang thai</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-4">
                                            <div class="form-group">
                                                <input @if(isset($staff) && true == $staff->has_baby) checked @endif name='has_baby' type="checkbox"/><label for="status">Có con nhỏ</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-4">
                                            <div class="form-group">
                                                <input @if(isset($staff) && true == $staff->is_linking_staff) checked @endif name='is_linking_staff' type="checkbox"/><label for="status">Giáo viên liên kết</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit"
                                                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light">@lang('admin.submit')
                                            </button>
                                        </div>

                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Scroll - horizontal and vertical table -->
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2()
        });
    </script>
@endpush