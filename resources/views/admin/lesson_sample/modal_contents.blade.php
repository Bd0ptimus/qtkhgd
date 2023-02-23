{{-- <div class="modal fade" id="modalLessonSampleContent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Xem bài giảng mẫu</h5>
                <button type="button" class="close" data-dismiss="modal" data-target="" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        
            <div class="modal-body">
                <div class="table-responsive">
                    <textarea class='form-control description' name='content'></textarea>
                </div>
            </div>
        </div>
    </div>
</div> --}}


<!-- Modal lên bài giảng -->
<div class="modal fade" id="modalLessonSampleContent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nội dung bài giảng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- {{dd($lessonSample)}} --}}
            {{-- <form method="POST" action="{{ route('school.staff.teacher_lesson.edit', ['school_id' => $school->id, 'staffId' => $teacherPlan->staff_id, 'planId' => $teacherPlan->id, 'lessonId' => $lessonSample->id]) }}"> --}}
            <form>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4 border-right-dark">
                            <h4>Thông tin bài giảng</h4>
                            <div class="info-lesson-teacher">
                                <ul>
                                    <li style="line-height: 30px;"><b>Tên bài học: </b>{{ $lessonSample->title }}</li>
                                    <li style="line-height: 30px;"><b>Thuộc bộ sách: </b>{{ $lessonSample->getAssemblage() }}
                                    </li>
                                    <li style="line-height: 30px;"><b>Khối học:{{GRADES[$lessonSample->grade]}} </b></li>
                                    <li style="line-height: 30px;"><b>Môn học: </b>{{ $lessonSample->subject->name }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <h4>Bài giảng điện tử</h4>
                            <div class="clearfix">
                                <ul class="nav nav-pills mb-3" id="pills-{{$lessonSample->id}}-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lessonSample->id}}-1-tab" data-toggle="pill" href="#pills-{{$lessonSample->id}}-1" role="tab" aria-controls="pills-{{$lessonSample->id}}-1" aria-selected="true">Word</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lessonSample->id}}-2-tab" data-toggle="pill" href="#pills-{{$lessonSample->id}}-2" role="tab" aria-controls="pills-{{$lessonSample->id}}-2" aria-selected="false">Powerpoint</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lessonSample->id}}-3-tab" data-toggle="pill" href="#pills-{{$lessonSample->id}}-3" role="tab" aria-controls="pills-{{$lessonSample->id}}-3" aria-selected="false">Thiết bị số</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lessonSample->id}}-4-tab" data-toggle="pill" href="#pills-{{$lessonSample->id}}-4" role="tab" aria-controls="pills-{{$lessonSample->id}}-4" aria-selected="false">Trò chơi vận dụng</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-{{$lessonSample->id}}-5-tab" data-toggle="pill" href="#pills-{{$lessonSample->id}}-5" role="tab" aria-controls="pills-{{$lessonSample->id}}-5" aria-selected="false">Mô hình mô phỏng</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-{{$lessonSample->id}}-tabContent">
                                    <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-1" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-1-tab">
                                        <div class="table-responsive">
                                            <textarea class='form-control description' name='content' value="{{$lessonSample->content ?? ''}}">{{$lessonSample->content ?? ''}}</textarea>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-2" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-2-tab">
                                        <div class="rpw">
                                            @if (isset($onlyView))
                                                @if(isset($lessonSample) && count($lessonSample->attachments) > 0)
                                                    @forelse ($lessonSample->attachments as $attachment)
                                                        <a class="ml-1" href="{{ route('lesson_sample.download_attach_file', ['attachmentId' => $attachment->id]) }}">{{$attachment->name}}</a><br/>
                                                        @empty
                                                    @endforelse
                                                @endif
                                            @else
                                                <input type="file" multiple id="file" name="files[]" />
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-3" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-3-tab">
                                        <div class="rpw">
                                            <label for="name" class="control-label">Video Thiết bị số</label><br>
                                            @if (!isset($onlyView))
                                                <input name='video_tbs' type="file"  />     
                                            @endif
                                            @if($lessonSample->video_thiet_bi_so)
                                                <div class="col-sm-4">
                                                <a target="_blank" href="{{ \App\Admin\Helpers\Utils::getLinkS3($lessonSample->video_thiet_bi_so, 30) }}">Xem video</a>
                                                </div>
                                            @endif
                                            
                                        </div>
                                    </div>
        
                                    <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-4" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-4-tab">
                                        <div class="rpw">
                                            <div class="col-sm-8">
                                                <label for="name" class="control-label">Link trò chơi vận dụng</label>
                                                <div class="input-group">
                                                    @if (!isset($onlyView))
                                                        <input name='game_simulator' type="text" class="form-control" value="{{$lessonSample->game_simulator ?? ''}}"/>
                                                    @endif
                                                    @if($lessonSample->game_simulator)
                                                        <div class="col-sm-4">
                                                            <a target="_blank" href="{{ $lessonSample->game_simulator }}">Xem mô phỏng</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-5" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-5-tab">
                                        <div class="rpw">
                                            <div class="col-sm-8">
                                                <label for="name" class="control-label">Link Mô hình Mô phỏng</label>
                                                <div class="input-group">
                                                    @if (!isset($onlyView))
                                                        <input name='diagram_simulator' type="text" class="form-control" value="{{$lessonSample->diagram_simulator ?? ''}}"/>
                                                    @endif
                                                    @if($lessonSample->diagram_simulator)
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
                        </div>
                    </div>
                    <br/>
                    <hr>
                    <h4>Tài liệu bổ sung</h4>
                    <div class="clearfix">
                        <ul class="nav nav-pills mb-3" id="pills-{{$lessonSample->id}}-tab" role="tablist">
                            <li class="nav-item">                                                             

                                <a class="nav-link" id="pills-{{$lessonSample->id}}-1-tab" data-toggle="pill" href="#pills-{{$lessonSample->id}}-homesheet" role="tab" aria-controls="pills-{{$lessonSample->id}}-1" aria-selected="true">Bài tập về nhà</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-{{$lessonSample->id}}-3-tab-add" data-toggle="pill" href="#pills-{{$lessonSample->id}}-exercise" role="tab" aria-controls="pills-{{$lessonSample->id}}-3-add" aria-selected="false">Để kiểm tra</a>
                            </li>                            
                            <li class="nav-item">
                                <a class="nav-link" id="pills-{{$lessonSample->id}}-4-tab-add" data-toggle="pill" href="#pills-{{$lessonSample->id}}-4-add" role="tab" aria-controls="pills-{{$lessonSample->id}}-3-add" aria-selected="false">Trò chơi vận dụng</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-{{$lessonSample->id}}-tabContent">
                            
                            <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-homesheet" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-1-tab">
                                <div class="table-responsive">
                                    <div class="rpw">
                                        <h5>File nội dung đính kèm</h5>
                                        @if (isset($onlyView))
                                            @if(isset($lessonSample->homesheet) && count($lessonSample->homesheet->attachments) > 0)
                                                @forelse ($lessonSample->homesheet->attachments as $attachment)
                                                    <a class="ml-1" href="{{ route('homework_sheet.download_attach_file', ['attachmentId' => $attachment->id]) }}">{{$attachment->name}}</a><br/>
                                                    @empty
                                                @endforelse
                                            @endif
                                        @else
                                            <input type="file" multiple id="file" name="files[]" />
                                        @endif
                                    </div>
                                    <textarea class='form-control description' name='content' value="{{ isset($lessonSample->homesheet) ? $lessonSample->homesheet->content : '' }}">{{ isset($lessonSample->homesheet) ? $lessonSample->homesheet->content : '' }}</textarea>
                                </div>
                            </div>                            
                            <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-exercise" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-1-tab">
                                <div class="table-responsive">
                                
                                        <div class="rpw">
                                            <h5>File nội dung đính kèm</h5>
                                            @if (isset($onlyView))
                                                @if(isset($lessonSample->exercise) && count($lessonSample->exercise->attachments) > 0)
                                                    @forelse ($lessonSample->exercise->attachments as $attachment)
                                                        <a class="ml-1" href="{{ route('exercise_question.download_attach_file', ['attachmentId' => $attachment->id]) }}">{{$attachment->name}}</a><br/>
                                                        @empty
                                                    @endforelse
                                                @endif
                                            @else
                                                <input type="file" multiple id="file" name="files[]" />
                                            @endif
                                        </div>   
                                    <textarea class='form-control description' name='content' value="{{ isset($lessonSample->exercise) ? $lessonSample->exercise->content : '' }}">{{ isset($lessonSample->exercise) ? $lessonSample->exercise->content : '' }}</textarea>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-{{$lessonSample->id}}-4-add" role="tabpanel" aria-labelledby="pills-{{$lessonSample->id}}-4-tab-add">
                                <div class="table-responsive">
                                    <textarea class='form-control description' name='game_content' value=""></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

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
            fontsize_formats: "8pt 9pt 10pt 11pt 12pt 13pt 14pt 18pt 24pt 30pt 36pt 48pt 60pt 72pt 96pt",
            content_style: "body {font-size: 13pt; font-family:'Times New Roman, Times, serif;}",
            paste_data_images: true,
            //autosave/update text area
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            //upload images
            images_upload_handler: function(blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '/portal/upload-tinymce-image');
                xhr.setRequestHeader("X-CSRF-Token", '');
                xhr.onload = function() {
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

            init_instance_callback: function(editor) {
                if (document.getElementById(editor.id).hasAttribute('disabled')) {
                    editor.getBody().setAttribute('contenteditable', false);
                }
            }
        });
    });
</script>
