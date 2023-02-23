@extends('layouts.contentLayoutMaster')

@php
    $title = 'Chuyển lớp';
    $breadcrumbs = [
        ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
        ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
        ['name' => $title],
    ];

@endphp

@section('title', $title)

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/dragula.min.css')) }}">
@endsection

@section('page-style')
        <!-- Page css files -->
        <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/drag-and-drop.css')) }}">
@endsection
@section('main')
 <section id="dd-with-handle">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Hướng dẫn: Chọn điểm trường và lớp, sau đó kéo thả học sinh cần chuyển lớp.</h4>
        </div>
        <div class="card-content">
        <form method="post" action="{{ route('school.move_student_between_classes.update', [ 'id' => $school->id]) }}">
            @csrf
          <div class="card-body">
                <div class="form-group col-sm-3 ">
                    <label for="date" class="control-label">Ngày chuyển lớp</label>
                    <input required type="date" name="date" class="form-control filter-date" placeholder="" value="{{ old('date', $date) }}">
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12"> 
                        <div class="col-sm-6 form-group">
                            <label for="class1">Lớp chuyến đi</label>
                            <select class="form-control select2 filter-class1" style="width: 100%;" name="class1" >
                                <option value="">--Chọn lớp--</option>
                                    @foreach ($school->classes as $key => $class)
                                        @if(($selected_class_2 && $class->id != $selected_class_2) || (!$selected_class_2) )
                                            <option value="{{ $class->id }}" @if($selected_class_1 && $class->id == $selected_class_1) selected @endif>{!! $class->class_name !!}</option>
                                        @endif
                                    @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <h4 class="my-1">Danh sách học sinh</h4>
                            <ul class="list-group" id="multiple-list-group-a" data-id={{ $class1->id ?? null }}>
                                    @foreach ($class1_student_list as $index1 => $student1)
                                    <li class="list-group-item" id="{{ $student1->id }}">
                                        <div class="media">
                                            <div class="media-body" >
                                                <h5 class="mt-0">{{ $student1->fullname }}</h5>
                                                Ngày sinh: {{ $student1->dob }}
                                                <input hidden name="students[{{ $student1->id }}]" value="{{ $class1->id }}" data-id={{ $class1->id ?? null }}>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="col-sm-6 form-group">
                            <label for="class2">Lớp chuyển đến</label>
                            <select class="form-control select2 filter-class2" style="width: 100%;" name="class2" >
                                <option value="">--Chọn lớp--</option>
                                    @if(count($optionSelectClass2) > 0)
                                        @foreach ($optionSelectClass2 as $key => $class)
                                            @if(!$selected_class_1 || ($selected_class_1 && $class->id != $selected_class_1))
                                            <option value="{{ $class->id }}" @if($selected_class_2 && $class->id == $selected_class_2) selected @endif>{!! $class->class_name !!}</option>
                                            @endif
                                        @endforeach
                                    @endif
                            </select>
                        </div>
                        <div class="col">
                        <h4 class="my-1">Danh sách học sinh</h4>
                        <ul class="list-group" id="multiple-list-group-b" data-id={{ $class2->id ?? null }}>
                            @foreach ($class2_student_list as $index2 => $student2)
                            <li class="list-group-item" id="{{ $student2->id }}">
                                <div class="media">
                                    <div class="media-body">
                                        <h5 class="mt-0">{{ $student2->fullname }}</h5>
                                        Ngày sinh: {{ $student2->dob }}
                                        <input hidden name="students[{{ $student2->id }}]" value="{{ $class2->id }}" data-id={{ $class2->id ?? null }}>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        </div>
                    </div>
                </div>
                <br>
                <button type="submit" class="btn btn-primary ag-grid-export-btn waves-effect waves-light mr-1">
                    Lưu thông tin
                </button>
            </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- // With Handle Ends -->

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/extensions/dragula.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            
        });
        dragula([document.getElementById('multiple-list-group-a'), document.getElementById('multiple-list-group-b')]).on('drop', function(el) 
        {
            class_id_original = el.querySelector('input').getAttribute('data-id');
            class_id_drop = el.parentNode.getAttribute('data-id');
            el.querySelector('input').value = class_id_drop;
            if(class_id_drop != class_id_original){
                el.style.backgroundColor = '#28c76f';
                el.style.color = 'white';
                el.querySelector('h5').style.color='white';
                el.style.border = 'double';
            }else{
                el.style.backgroundColor = '#fff';
                el.style.color = '#626262';
                el.querySelector('h5').style.color='#2c2c2c';
                el.style.border = '1px solid rgba(34, 41, 47, 0.125)';
            }
        });
    </script>

    <script>
        let searchParams = new URLSearchParams(window.location.search);
        var date = searchParams.get('date');
        var class1 = searchParams.get('class1');
        var class2 = searchParams.get('class2');

        if(date != null) {
            $('select[name="date"]').val(date);
        }
        if(class1 != null) {
            $('select[name="class1"]').val(class1);
        }

        if(class2 != null) {
            $('select[name="class2"]').val(class2);
        }

        $('.filter-class1').on('change', function(){
            var url = "{!! route('school.move_student_between_classes', [ 'id' => $school->id]) !!}";
            if($('.filter-date').val() != '') {
                url = addUrlParam(url, 'date', $('.filter-date').val());
            }
            if($('select[name="class1"]').val() != '') {
                url = addUrlParam(url, 'class1', $('select[name="class1"]').val());
            }
            if($('select[name="class2"]').val() != '') {
                url = addUrlParam(url, 'class2', $('select[name="class2"]').val());
            }
            window.location.replace(url);
        });

        $('.filter-class2').on('change', function(){
            var url = "{!! route('school.move_student_between_classes', [ 'id' => $school->id]) !!}";
            if($('.filter-date').val() != '') {
                url = addUrlParam(url, 'date', $('.filter-date').val());
            }
            if($('select[name="class1"]').val() != '') {
                url = addUrlParam(url, 'class1', $('select[name="class1"]').val());
            }
            if($('select[name="class2"]').val() != '') {
                url = addUrlParam(url, 'class2', $('select[name="class2"]').val());
            }
            window.location.replace(url);
        });
        $('.filter-date').on('change', function(){
            
            var url = "{!! route('school.move_student_between_classes', [ 'id' => $school->id]) !!}";
            if($('.filter-date').val() != '') {
                url = addUrlParam(url, 'date', $('.filter-date').val());
            }
            if($('select[name="class1"]').val() != '') {
                url = addUrlParam(url, 'class1', $('select[name="class1"]').val());
            }
            if($('select[name="class2"]').val() != '') {
                url = addUrlParam(url, 'class2', $('select[name="class2"]').val());
            }

            window.location.replace(url);
        });

        function addUrlParam(url, param, value) {
            if(url.includes('?')) {
                url += `&${param}=${value}`;
            }else{
                url += `?${param}=${value}`;
            }
            return url;
        }
    </script>
@endsection
