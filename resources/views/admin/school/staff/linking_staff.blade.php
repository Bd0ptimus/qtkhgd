@extends('layouts/contentLayoutMaster')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css') }}">
    <style>
        @media(min-width: 576px) {
            .modal-dialog {
                max-width: 1366px!important;
            }
        }
    </style>

@endsection

@php
$breadcrumbs;
@endphp

@section('main')
    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách giáo viên liên kết của trường.</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <form method="post" action="" name="form">
                                @csrf
                                <div class="d-flex float-right">
                                    <div class="text-nowrap ml-1">
                                        {{-- <button type="submit" class="btn btn-success">
                                            Lưu lại
                                        </button> --}}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table zero-configuration table-bordered table-striped text-nowrap"
                                        id="table" style="border-spacing: 1px">
                                        <thead>
                                            <tr>
                                                <th class="col-md-1" scope="col">STT</th>
                                                <th class="col-md-2" scope="col">Tên giáo viên</th>
                                                <th class="col-md-2" scope="col">Khối học</th>
                                                <th class="col-md-2" scope="col">Giảng dạy bộ môn</th>
                                                <th class="col-md-2" scope="col">Trường chính</th>
                                                <th class="col-md-4" scope="col">Trường liên kết</th>
                                                <th class="col-md-2" scope="col">Lịch tuần</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $listLinkingSchools = []; @endphp
                                            @foreach ($linkingStaffs as $index => $staff)
                                                <tr>
                                                    <input type='hidden' name='staffs[{{ $staff->id }}][id]'
                                                        value="{{ $staff->id }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $staff->fullname }}</td>

                                                    <!-- Staff Grades -->
                                                    <td>
                                                        @php
                                                            $listGrades = [];
                                                            $oldGrades = old('grades', isset($staff) ? $staff->staffGrades->pluck('grade')->toArray() : '');
                                                            if (is_array($oldGrades)) {
                                                                foreach ($oldGrades as $value) {
                                                                    $listGrades[] = (int) $value;
                                                                }
                                                            }
                                                        @endphp
                                                        <select disabled class="form-control input-sm subject select2"
                                                            multiple="multiple" data-placeholder="Khối học"
                                                            style="width: 100% !important;">
                                                            <option value=""></option>
                                                            @foreach ($grades as $index => $grade)
                                                                <option value="{{ $index }}"
                                                                    {{ count($listGrades) && in_array($index, $listGrades) ? 'selected' : '' }}>
                                                                    {{ $grade }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <!-- Staff Subjects -->
                                                    <td>
                                                        @php
                                                            $listSubjects = [];
                                                            $oldSubjects = old('subjects', isset($staff) ? $staff->staffSubjects->pluck('subject_id')->toArray() : '');
                                                            if (is_array($oldSubjects)) {
                                                                foreach ($oldSubjects as $value) {
                                                                    $listSubjects[] = (int) $value;
                                                                }
                                                            }
                                                        @endphp

                                                        <select disabled class="form-control input-sm subject select2"
                                                            multiple="multiple" data-placeholder="Môn học">
                                                            <option value=""></option>
                                                            @foreach ($subjects as $index => $subject)
                                                                <option value="{{ $subject->id }}"
                                                                    {{ count($listSubjects) && in_array($subject->id, $listSubjects) ? 'selected' : '' }}>
                                                                    {{ $subject->name }}</option>
                                                            @endforeach
                                                        </select>

                                                    </td>
                                                    <td>{{ $staff->school->school_name }}</td>

                                                    <td>
                                                        @php
                                                            $listLinkingSchools[$staff->id] = [];
                                                            
                                                            $oldLinkingSchools = old('linkingSchools', $staff->linkingSchools->pluck('additional_school_id')->toArray());
                                                            if (is_array($oldLinkingSchools)) {
                                                                foreach ($oldLinkingSchools as $value) {
                                                                    array_push($listLinkingSchools[$staff->id], (int) $value);
                                                                }
                                                            }
                                                            
                                                        @endphp

                                                        <select class="form-control input-sm select2" multiple="multiple"
                                                            data-placeholder="Chọn trường" style="width: 100%;"
                                                            name="staffs[{{ $staff->id }}][linkingSchools][]"
                                                            id='school-select-{{ $staff->id }}'
                                                            onChange="schoolSelect({{ $staff->id }},1)">
                                                            <option value=""></option>
                                                            @foreach ($districtSchools as $index => $districtSchool)
                                                                <option value="{{ $districtSchool->id }}"
                                                                    {{ count($listLinkingSchools[$staff->id]) && in_array($districtSchool->id, $listLinkingSchools[$staff->id]) ? 'selected' : '' }}>
                                                                    {{ $districtSchool->school_name }}</option>
                                                            @endforeach

                                                        </select>
                                                    </td>
                                                    <td>
                                                        <a {{ count($staff->linkingSchools) > 0 ? '' : 'hidden="true"' }} href="javascript:void(0)" data-toggle="modal" class="config-staff-{{ $staff->id }}" data-target="#config-staff-{{ $staff->id }}" onclick="openModal(this)">
                                                            Cấu hình
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>


                                    @foreach ($linkingStaffs as $index => $staff)
                                        @if (count($staff->linkingSchools) > 0)
                                            <div class="config-staff-table modal" id='config-staff-{{ $staff->id }}'
                                                style='display:none;' tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cấu hình cho giáo viên
                                                                {{ $staff->fullname }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col" class="col-3">Ngày</th>
                                                                        <th scope="col" class="col-3">Trường liên kết</th>
                                                                        <th scope="col" class="col-3">Tiết dạy</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @for ($index = 2; $index < 8; $index++)
                                                                        <tr>
                                                                            <th scope="col"class="col-3">Thứ {{ $index }}</th>
                                                                            <th scope="col"class="col-3">
                                                                                <div class="form-group">
                                                                                    <div class='row'>
                                                                                        <div class="col-sm-10">
                                                                                            <select
                                                                                                class="form-control parent select2 filter-school-linking"
                                                                                                style="width: 100%;"
                                                                                                name="staffs[{{ $staff->id }}][workingDays][]"
                                                                                                id="config-staff-{{ $staff->id }}-{{ $index }}">
                                                                                                <option>Chọn</option>
                                                                                                @foreach ($districtSchools as $key => $districtSchool)
                                                                                                    @if (in_array($districtSchool->id, $listLinkingSchools[$staff->id]))
                                                                                                        <option
                                                                                                            value="{{ $districtSchool->id }}">
                                                                                                            {!! $districtSchool->school_name !!}
                                                                                                        </option>
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col"class="col-3">
                                                                                <div class="form-group">
                                                                                    <div class='row'>
                                                                                        <div class="col-sm-10">
                                                                                            <select
                                                                                                    class="form-control parent select2" multiple="multiple"
                                                                                                    style="width: 100%;"
                                                                                                    id="config-slots-{{ $staff->id }}-{{ $index }}">
                                                                                                @foreach ($arraySlots[$index] as $num => $slot)
                                                                                                    @if (($index == 2 && $num == 0) || ($index == 7 && $num == 4))
                                                                                                        @continue
                                                                                                    @endif
                                                                                                    <option value="{{ $slot }}">
                                                                                                        Tiết {{ $num + 1 }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </th>
                                                                        </tr>
                                                                    @endfor

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal"
                                                                onclick="schoolSelect({{ $staff->id }},2)">Lưu</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

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
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        var configStaffData = [];

        function configStaffEvent(staffId, controller) {
            if (controller) {
                document.getElementById('config-staff-' + staffId).style.display = 'block';
                return;
            }
            document.getElementById('config-staff-' + staffId).style.display = 'none';
            return;
        }

        function openModal(that) {
            let modalId = $(that).attr('data-target');
            $('modalId').modal('show');
        }

        function handleInterFace(dataReturn) {
            for (var i = 2; i < 8; i++) {
                var configPanelContainer = document.getElementById('config-staff-' + dataReturn['staff_id'] + '-' + i);
                configPanelContainer.innerHTML = "";
                var firstElement = document.createElement('option');
                firstElement.value = "";
                firstElement.innerText = 'Chọn';
                configPanelContainer.appendChild(firstElement);
                dataReturn['school'].forEach(function(schoolData, index) {
                    var schoolElement = document.createElement('option');
                    schoolElement.value = schoolData['id'];
                    schoolElement.innerText = schoolData['name'];
                    try {
                        //condition for keep current config
                        if (schoolData['id'] == configStaffData['workday'][i]['id'] && dataReturn['staff_id'] ==
                            configStaffData['staff_id']) {
                            schoolElement.selected = true;
                        }
                    } catch(e) {
                        
                    }
                    configPanelContainer.appendChild(schoolElement);
                });
            }

        }


        function schoolSelect(staffIdIn, type) {
            //if type = 1 : add linking school 
            //        = 2 : config staff
            var schoolIdLinkingChosen = [];
            var elementId = "";
            if (type == 1) {
                elementId = '#school-select-' + staffIdIn;
                schoolIdLinkingChosen = $(elementId + ' option:selected').get().map(e => e.value);
            } else {
                for (var i = 2; i < 8; i++) {
                    elementId = "config-staff-" + staffIdIn + '-' + i;
                    let elementSlotId = "#config-slots-" + staffIdIn + '-' + i;
                    let data = {
                        school_id: document.getElementById(elementId).value,
                        slots:$(elementSlotId + ' option:selected').get().map(e => e.value)
                    };
                    schoolIdLinkingChosen.push(data)
                }
            }
            console.log(schoolIdLinkingChosen);
            console.log('staff id : ' + staffIdIn);
            var route = "{{ route('admin.school.linking_staff', ['id' => '+staffIdIn+']) }}";
            $.ajax({
                url: route,
                method: 'POST',
                data: {
                    type: type,
                    staffId: staffIdIn,
                    schoolId: schoolIdLinkingChosen,
                    _token: '{{ csrf_token() }}',
                },
                success: function(res) {
                    console.log('data return : ' + JSON.stringify(res));
                    if (res['error'] == 0) {
                        if (type == 1) {
                            $('.config-staff-' + staffIdIn).prop('hidden', false);
                            if (res['school'].length == 0) {
                                $('.config-staff-' + staffIdIn).prop('hidden', true);
                            }
                            alert('Thêm trường liên kết thành công');
                            // handleInterFace(res);
                            location.reload();
                        } else {
                            configStaffData = res;
                            alert('Cấu hình giáo viên thành công');
                            $('.close').trigger('click')
                        }
                        return;
                    }
                    alert('Có lỗi xảy ra');

                }
            });
        }




        $(document).ready(function() {
            $('.select2').select2({
                allowClear: true
            });

            $('#shool-subject').DataTable();

            $('#shool-subject').on('click', '.delete-item', function(e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá chỉ tiêu này?');
                if (confirmDelete) {
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            element.parents('tr').remove();
                            alert('Đã xoá chỉ tiêu');
                        }
                    });
                }
            });

            $('#shool-subject').on('click', '.update-item', function(e) {
                e.preventDefault();
                var resultInput = prompt('Kết quả %', '');
                var regex = "^0*(?:[1-9][0-9]?|100)$";
                if (resultInput.match(regex) || parseInt(resultInput) == 0) {
                    var element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            result: resultInput,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            console.log(res['error']);
                            if (res['error'] == 0) {
                                document.location.reload();
                            }
                            alert('Đã thêm kết quả');
                        }
                    });
                } else {
                    alert('Chỉ chấp nhận số từ 0-100');
                }

            });
        });
    </script>
    <style>
        #self-result-button:hover {}
    </style>
@endsection
