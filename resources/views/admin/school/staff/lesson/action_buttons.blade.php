@if($canManage)
    @if($teacherPlan->status == PLAN_APPROVED) 
        <!-- <a href="#" class="btn btn-flat btn-info btn-datatable show-modal-lesson"
            data-toggle="modal" data-target="#modalLessonContent{{$lesson->id}}">
            <i class="fa fa-edit" aria-hidden="true"></i>{{empty($lesson->content) ? 'Lên bài giảng' : 'Sửa bài giảng'}}
        </a><br/> -->
        
        <a href="#" class="btn btn-flat btn-info btn-datatable show-modal-lesson"
            data-url="{{route('ajax_get_teacher_lesson_by_id', ['id' => $lesson->id ])}}">
            <i class="fa fa-edit" aria-hidden="true"></i>{{empty($lesson->content) ? 'Lên bài giảng' : 'Sửa bài giảng'}}
        </a><br/>

       <!--  <a href="#" class="btn btn-flat btn-warning btn-datatable"
            data-toggle="modal" data-target="#modalLessonSample{{$lesson->id}}" data-lessonId="{{$lesson->id}}">
            <i class="fa fa-edit" aria-hidden="true"></i>Chọn bài giảng
        </a><br/> -->
    @else
        <a href='#' class="delete-row btn btn-danger">Xoá</a><br/>
    @endif
@endif




@if( isset($groupLeader->staff_id) && Admin::user()->user_detail == $groupLeader->staff_id ) 
    @if ($lesson->status == PLAN_SUBMITTED )
        <a href="{{ route('school.staff.plan.lesson_approve', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $teacherPlan->regular_group_id,'lessonId' => $lesson->id])}}" class="btn btn-flat btn-success btn-datatable">
            <i class="fa fa-check" aria-hidden="true"></i>Duyệt kế hoạch
        </a><br/>
    @elseif(($lesson->status == PLAN_PENDING||$lesson->status == PLAN_SUBMITTED))
        <a href="{{ route('school.staff.plan.lesson_submit', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $teacherPlan->regular_group_id,'lessonId' => $lesson->id])}}" class="btn btn-flat btn-info btn-datatable">
            <i class="fa fa-send" aria-hidden="true"></i>Gửi tổ trưởng duyệt
        </a><br/>
    @endif
    <a href="#" class="btn btn-flat btn-info btn-datatable show-modal-lesson"
        data-url="{{route('ajax_get_teacher_lesson_by_id', ['id' => $lesson->id, 'view' => true ])}}">
            <i class="fa fa-eye" aria-hidden="true"></i>Xem bài giảng
        </a><br/>
    <a href="#" class="btn btn-flat btn-warning btn-datatable"
        data-toggle="modal" data-target="#modalAddNote{{$lesson->id}}">
        <i class="fa fa-edit" aria-hidden="true"></i>Nhận xét bài giảng
    </a><br/>
@else 
    @if(($lesson->status == PLAN_PENDING||$lesson->status == PLAN_SUBMITTED))
        <a href="{{ route('school.staff.plan.lesson_submit', ['school_id' => $school->id, 'staffId' => $staff->id, 'rgId' => $teacherPlan->regular_group_id,'lessonId' => $lesson->id])}}" class="btn btn-flat btn-info btn-datatable">
            <i class="fa fa-send" aria-hidden="true"></i>Gửi tổ trưởng duyệt
        </a><br/>
    @endif
@endif

@if(Admin::user()->isRole(ROLE_HIEU_TRUONG))
    <a href="#" class="btn btn-flat btn-info btn-datatable show-modal-lesson"
    data-url="{{route('ajax_get_teacher_lesson_by_id', ['id' => $lesson->id, 'view' => true ])}}">
        <i class="fa fa-eye" aria-hidden="true"></i>Xem bài giảng
    </a><br/> 
@endif


<a href="#" class="btn btn-flat btn-success btn-datatable"
    data-toggle="modal" data-target="#modalLessonHistory{{$lesson->id}}">
    <i class="fa fa-eye" aria-hidden="true"></i>Xem nhận xét
</a><br/>
