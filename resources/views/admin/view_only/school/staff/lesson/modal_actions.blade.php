<!-- Modal lên bài giảng -->
<div class="modal fade" id="modalLessonContent{{$lesson->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nội dung bài giảng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('school.staff.teacher_lesson.edit', ['school_id' => $school->id, 'staffId' => $teacherPlan->staff_id,'planId' => $teacherPlan->id, 'lessonId' => $lesson->id]) }}">
                @csrf
                <div class="modal-body">
                    <div class="table-responsive">
                        <textarea class='form-control description' name='content' value="{{$lesson->content ?? $lessonTemplate}}">{{$lesson->content ?? $lessonTemplate}}</textarea>
                    </div>
                </div>
                @if($planOwner->id == Admin::user()->id)
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Lưu bài giảng</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Modal lịch sử -->
<div class="modal fade" id="modalLessonHistory{{$lesson->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Lịch sử</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-nowrap table-plan">
                    <thead>
                        <tr>
                            <th scope="col">Thời gian</th>
                            <th scope="col">Nội dung</th>
                            <th scope="col">Người nhận xét</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lesson->histories as $history)
                            <tr>
                                <td>{{$history->created_at}}</td>
                                <td>{{$history->notes}}</td>
                                <td>{{$history->createdBy->name}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
        </div>
        </div>
    </div>
</div>

<!-- Modal Add Note -->
<div class="modal fade" id="modalAddNote{{$lesson->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Thêm nhận xét</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="{{ route('school.staff.teacher_lesson.add_review', ['school_id' => $school->id, 'staffId' => $teacherPlan->staff_id,'planId' => $teacherPlan->id, 'lessonId' => $lesson->id]) }}">
        @csrf    
            <div class="modal-body">
                <div class="table-responsive">
                    <textarea class='form-control' name='notes'></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Nhận xét</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Modal Select Lesson -->
<div class="modal fade" id="modalLessonSample{{$lesson->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Chọn bài giảng</h5>
                <button type="button" class="close" data-dismiss="modal" data-target="#modalLessonSample" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <input name="title-{{ $lesson->id }}" class="form-control" type="text" placeholder="Nhâp tên bài giảng">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-primary btnSearch" type="button" data-sample-lession="{{ $lesson->id }}">Tìm kiếm</button>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-nowrap table-plan">
                            <thead>
                                <tr>
                                    <th scope="col">Tên bài giảng</th>
                                    <th scope="col">Khối học</th>
                                    <th scope="col">Môn học</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                                @foreach($sampleLessons as $sampleLesson)
                                    <tr class="content-lesson-{{ $lesson->id }}" data-title="{{$sampleLesson->title}}">
                                        <td>{{$sampleLesson->title}}</td>
                                        <td>{{GRADES[$sampleLesson->grade]}}</td>
                                        <td>{{$sampleLesson->subject->name}}</td>
                                        <td>
                                            <a style="margin-top: 3px" href="#" class="btn btn-flat btn-success btn-datatable"
                                                data-toggle="modal" data-target="#modalLessonSampleDetail{{$sampleLesson->id}}">
                                                Xem nội dung
                                            </a>
                                            <a type="button" class="btn btn-flat btn-warning btn-datatable" href="{{ route('school.staff.teacher_lesson.select_sample', ['school_id' => $school->id, 'staffId' => $teacherPlan->staff_id,'planId' => $teacherPlan->id, 'lessonId' => $lesson->id, 'sampleId' => $sampleLesson->id])}}">Sử dụng bài giảng này</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
