<!-- Modal lên bài giảng -->
<!-- <div class="modal fade" id="modalLessonContent{{$lesson->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> -->
<div class="modal fade" id="modalLessonContent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1000px;">
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
                    <div class="row">
                        <div class="col-sm-4 border-right-dark">
                            <h4>Thông tin bài giảng</h4>
                            <div class="info-lesson-teacher">
                                <ul>
                                    @if($school->school_type == SCHOOL_MN)
                                        <li style="line-height: 30px;"><b>Chủ đề: </b>{{$lesson->chu_de}}</li>
                                        <li style="line-height: 30px;"><b>Chủ điểm: </b>{{$lesson->noi_dung}}</li>
                                        <li style="line-height: 30px;"><b>Khoảng TG: </b>{{$lesson->start_date }} - {{$lesson->end_date }}</li>
                                    @else
                                        <!-- <li style="line-height: 30px;"><b>Tuần, Tháng: </b>{{$lesson->tuan_thang}}</li>
                                        <li style="line-height: 30px;"><b>Chủ đề/Mạch nội dung: </b>{{$lesson->chu_de}}</li> -->
                                        <li style="line-height: 30px;"><b>Tên bài học: </b>{{$lesson->ten_bai_hoc}}</li>
                                        <li style="line-height: 30px;"><b>Tiết học/Thời lượng: </b>{{$lesson->so_tiet}}</li>
                                        <!--  <li style="line-height: 30px;"><b>Nội dung điều chỉnh, bổ sung: </b>{{$lesson->noi_dung_dieu_chinh}}</li>
                                        <li style="line-height: 30px;"><b>Ghi chú: </b>{{$lesson->ghi_chu}}</li> -->
                                        <li style="line-height: 30px;"><b>Khoảng TG: </b>{{$lesson->start_date }} - {{$lesson->end_date }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <h4>Bài giảng điện tử</h4>
                            <div class="clearfix">
                                <ul class="nav nav-pills mb-3" id="pills-{{$lesson->id}}-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lesson->id}}-1-tab" data-toggle="pill" href="#pills-{{$lesson->id}}-1" role="tab" aria-controls="pills-{{$lesson->id}}-1" aria-selected="true">Word</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lesson->id}}-2-tab" data-toggle="pill" href="#pills-{{$lesson->id}}-2" role="tab" aria-controls="pills-{{$lesson->id}}-2" aria-selected="false">Powerpoint</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lesson->id}}-3-tab" data-toggle="pill" href="#pills-{{$lesson->id}}-3" role="tab" aria-controls="pills-{{$lesson->id}}-3" aria-selected="false">Thiết bị số</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lesson->id}}-4-tab" data-toggle="pill" href="#pills-{{$lesson->id}}-4" role="tab" aria-controls="pills-{{$lesson->id}}-4" aria-selected="false">Trò chơi vận dụng</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lesson->id}}-5-tab" data-toggle="pill" href="#pills-{{$lesson->id}}-5" role="tab" aria-controls="pills-{{$lesson->id}}-5" aria-selected="false">Mô hình mô phỏng</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-{{$lesson->id}}-tabContent">
                                    <div class="tab-pane fade" id="pills-{{$lesson->id}}-1" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-1-tab">
                                        <div class="table-responsive">
                                            <textarea class='form-control description' name='content' value="{{ $lesson->content ?? ($lessonSample->content ?? $lessonTemplate) }}">{{$lesson->content ?? ($lessonSample->content ?? $lessonTemplate) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-{{$lesson->id}}-2" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-2-tab">
                                        <div class="rpw">
                                            @if ($onlyView)
                                                <p>{{ $lesson->ppt }}</p>
                                            @else
                                                <input name='ppt' type="file" />
                                            @endif
                                            
                                            @if(isset($lessonSample) && count($lessonSample->attachments) > 0)
                                                <hr>
                                                <h5>Tham khảo:</h5>
                                                @forelse ($lessonSample->attachments as $attachment)
                                                    <a class="ml-1" href="{{ route('lesson_sample.download_attach_file', ['attachmentId' => $attachment->id]) }}">{{$attachment->name}}</a><br/>
                                                    @empty
                                                @endforelse
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-{{$lesson->id}}-3" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-3-tab">
                                        <div class="rpw">
                                            <div class="col-sm-8">
                                                <label for="name" class="control-label">Link Video Thiết bị số</label>
                                                <div class="input-group">
                                                @if($canManage)
                                                    <input name='video_tbs' type="text" class="form-control" value="{{$lesson->video_tbs ?? ''}}"/>
                                                @endif
                                                </div>
                                            </div>
                                            @if($lesson->video_tbs)
                                                <div class="col-sm-4">
                                                    <a target="_blank" href="{{ $lesson->video_tbs }}">Xem video</a>
                                                </div>
                                            @endif
                                            
                                            
                                            @if(isset($lessonSample->video_thiet_bi_so))
                                                <hr>
                                                <h5>Tham khảo:</h5>
                                                <div class="col-sm-4">
                                                <a target="_blank" href="{{ \App\Admin\Helpers\Utils::getLinkS3($lessonSample->video_thiet_bi_so, 30) }}">Xem video</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
        
                                    <div class="tab-pane fade" id="pills-{{$lesson->id}}-4" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-4-tab">
                                        <div class="rpw">
                                            <div class="col-sm-8">
                                                <label for="name" class="control-label">Link trò chơi vận dụng</label>
                                                <div class="input-group">
                                                @if($canManage)
                                                    <input name='game_simulator' type="text" class="form-control" value="{{$lesson->game_simulator ?? ''}}"/>
                                                @endif
                                                </div>
                                            </div>
                                            @if($lesson->game_simulator)
                                                <div class="col-sm-4">
                                                    <a target="_blank" href="{{ $lesson->game_simulator }}">Xem mô phỏng</a>
                                                </div>
                                            @endif
                                            
                                            @if(isset($lessonSample->game_simulator))
                                                <hr>
                                                <h5>Tham khảo:</h5>
                                                <div class="col-sm-4">
                                                    <a target="_blank" href="{{ $lessonSample->game_simulator }}">Xem mô phỏng</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
        
                                    <div class="tab-pane fade" id="pills-{{$lesson->id}}-5" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-5-tab">
                                        <div class="rpw">
                                            <div class="col-sm-8">
                                                <label for="name" class="control-label">Link Mô hình Mô phỏng</label>
                                                <div class="input-group">
                                                @if($canManage)
                                                    <input name='diagram_simulator' type="text" class="form-control" value="{{$lesson->diagram_simulator ?? ''}}"/>
                                                @endif
                                                </div>
                                            </div>
                                            @if($lesson->diagram_simulator)
                                                <div class="col-sm-4">
                                                    <a target="_blank" href="{{ $lesson->diagram_simulator }}">Xem mô phỏng</a>
                                                </div>
                                            @endif

                                            @if(isset($lessonSample->diagram_simulator))
                                                <hr>
                                                <h5>Tham khảo:</h5>
                                                <div class="col-sm-4">
                                                    <a target="_blank" href="{{ $lessonSample->diagram_simulator }}">Xem mô phỏng</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
        
                            </div>
                        </div>
                    </div>
                    <br/>
                    
             
                    @unless ($school->school_type == SCHOOL_MN)
                    <hr>
                    <h4>Tài liệu bổ sung</h4>
                    <div class="clearfix">
                        <ul class="nav nav-pills mb-3" id="pills-{{$lesson->id}}-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-{{$lesson->id}}-1-tab-add" data-toggle="pill" href="#pills-{{$lesson->id}}-1-add" role="tab" aria-controls="pills-{{$lesson->id}}-1-add" aria-selected="true">Bài tập về nhà</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-{{$lesson->id}}-2-ta-addb" data-toggle="pill" href="#pills-{{$lesson->id}}-2-add" role="tab" aria-controls="pills-{{$lesson->id}}-2-add" aria-selected="false">Bài tập nâng cao</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-{{$lesson->id}}-3-tab-add" data-toggle="pill" href="#pills-{{$lesson->id}}-3-add" role="tab" aria-controls="pills-{{$lesson->id}}-3-add" aria-selected="false">Để kiểm tra</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-{{$lesson->id}}-4-tab-add" data-toggle="pill" href="#pills-{{$lesson->id}}-4-add" role="tab" aria-controls="pills-{{$lesson->id}}-3-add" aria-selected="false">Trò chơi vận dụng</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-{{$lesson->id}}-tabContent">
                            
                            <div class="tab-pane fade show active" id="pills-{{$lesson->id}}-1-add" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-1-tab-add">
                                <div class="col-sm-4 {{ $errors->has('subject_id') ? ' has-error' : '' }}">
                                    <label for="subject" class="control-label">Phiếu bài tập về nhà</label>
                                    <select class="form-control input-sm subject select2"
                                            data-placeholder="Phiếu bài tập về nhà" style="width: 100%;">
                                        <option value=""></option>
                                        @foreach (\App\Models\HomeworkSheet::with('subject')->get() as $index => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} - {{ GRADES[$item->grade] }} - {{ $item->subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(isset($lessonSample->homesheet) && count($lessonSample->homesheet->attachments) > 0)
                                        <hr>
                                        <h5>Tham khảo:</h5>
                                        @forelse ($lessonSample->homesheet->attachments as $attachment)
                                            <a class="ml-1" href="{{ route('homework_sheet.download_attach_file', ['attachmentId' => $attachment->id]) }}">{{$attachment->name}}</a><br/>
                                            @empty
                                        @endforelse
                                    @endif
                                </div>    
                                <div class="table-responsive">
                                    <textarea class='form-control description' name='homeworks' value="{{$lesson->homeworks ?? ''}}">{{$lesson->homeworks ?? ($lessonSample->homesheet->content ?? '')}}</textarea>
                                </div>
                            </div>
                            
                            
                            <div class="tab-pane fade" id="pills-{{$lesson->id}}-3-add" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-3-tab-add">
                                <div class="col-sm-4 {{ $errors->has('subject_id') ? ' has-error' : '' }}">
                                    <label for="subject" class="control-label">Đề kiểm tra</label>
                                    <select class="form-control input-sm subject select2"
                                            data-placeholder="Đề kiểm tra" style="width: 100%;">
                                        <option value=""></option>
                                        @foreach (\App\Models\ExerciseQuestion::with('subject')->get() as $index => $item)
                                            <option value="{{ $item->id }}">{{ $item->title }} - {{ GRADES[$item->grade] }} - {{ $item->subject->name }}</option>
                                        @endforeach
                                    </select>

                                    @if(isset($lessonSample->exercise) && count($lessonSample->exercise->attachments) > 0)
                                        <hr>
                                        <h5>Tham khảo:</h5>
                                        @forelse ($lessonSample->exercise->attachments as $attachment)
                                            <a class="ml-1" href="{{ route('exercise_question.download_attach_file', ['attachmentId' => $attachment->id]) }}">{{$attachment->name}}</a><br/>
                                            @empty
                                        @endforelse
                                    @endif
                                </div>    
                                <div class="table-responsive">
                                    <textarea class='form-control description' name='test_question' value="{{$lesson->test_question ?? ''}}">{{ $lesson->test_question ?? ($lessonSample->exercise->contentt ?? '')}}</textarea>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-{{$lesson->id}}-4-add" role="tabpanel" aria-labelledby="pills-{{$lesson->id}}-4-tab-add">
                                <div class="table-responsive">
                                    <textarea class='form-control description' name='game_content' value="{{$lesson->game_content ?? ''}}">{{$lesson->game_content ?? ''}}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endunless
                </div>
                @if($planOwner->id == Admin::user()->id && !$onlyView)
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Lưu bài giảng</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {

    tinymce.execCommand('mceRemoveEditor', true, 'description');
    tinymce.init({
        selector: '.description',
        convert_urls: false,
        mode: 'exact',
        theme: "modern",
        skin: 'light',
        branding: false,
        menubar: false,
        statusbar: false,
        forced_root_block: false,
        content_css: '/css/tinymce-scroll.css',
        plugins: [
            "advlist autolink lists link image preview  codesample table hr textcolor",
            "paste autoresize"
        ],
        height: 700,
        toolbar: 'fontselect fontsizeselect forecolor backcolor bold link bullist numlist alignleft aligncenter alignright image hr table code fullscreen',
        fontsize_formats:"8pt 9pt 10pt 11pt 12pt 13pt 14pt 18pt 24pt 30pt 36pt 48pt 60pt 72pt 96pt",
        content_style: "body {font-size: 13pt; font-family:'Times New Roman, Times, serif;}",
        paste_data_images : true,
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        },
        //upload images
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '/portal/upload-tinymce-image');
            xhr.setRequestHeader("X-CSRF-Token", '');
            xhr.onload = function () {
                var json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        },

        init_instance_callback : function(editor)
        {
            if(document.getElementById(editor.id).hasAttribute('disabled')) {
                editor.getBody().setAttribute('contenteditable',false);
            }
        }
    });
});
</script>