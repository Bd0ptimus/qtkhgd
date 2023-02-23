@if($canManage)
    @if($teacherPlan->status == PLAN_APPROVED) 
        <a style="margin-top: 3px" href="#" class="btn btn-flat btn-info btn-datatable"
            data-toggle="modal" data-target="#modalLessonContent{{$lesson->id}}">
            <i class="fa fa-edit" aria-hidden="true"></i>{{empty($lesson->content) ? 'Lên bài giảng' : 'Sửa bài giảng'}}
        </a>

        <a style="margin-top: 3px" href="#" class="btn btn-flat btn-warning btn-datatable"
            data-toggle="modal" data-target="#modalLessonSample{{$lesson->id}}" data-lessonId="{{$lesson->id}}">
            <i class="fa fa-edit" aria-hidden="true"></i>Chọn bài giảng
        </a>
    @else
        <a href='#' class="delete-row btn btn-danger">Xoá</a>
    @endif
@endif

@if($planOwner->id !== Admin::user()->id)
    <a style="margin-top: 3px" href="#" class="btn btn-flat btn-info btn-datatable"
            data-toggle="modal" data-target="#modalLessonContent{{$lesson->id}}">
            <i class="fa fa-eye" aria-hidden="true"></i>Xem bài giảng
        </a>
    <a style="margin-top: 3px" href="#" class="btn btn-flat btn-warning btn-datatable"
        data-toggle="modal" data-target="#modalAddNote{{$lesson->id}}">
        <i class="fa fa-edit" aria-hidden="true"></i>Nhận xét bài giảng
    </a>
@endif
<a style="margin-top: 3px" href="#" class="btn btn-flat btn-success btn-datatable"
    data-toggle="modal" data-target="#modalLessonHistory{{$lesson->id}}">
    <i class="fa fa-eye" aria-hidden="true"></i>Xem nhận xét
</a>