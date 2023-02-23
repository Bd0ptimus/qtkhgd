<div class="modal fade" id="modalLessonSampleDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Xem bài giảng mẫu</h5>
                <button type="button" class="close" data-dismiss="modal" data-target="#modalLessonSampleDetail{{$sampleLesson->id}}" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        
            <div class="modal-body">
                <div class="table-responsive">
                    <textarea class='form-control description' name='content'>{{ $sampleLesson->content}}</textarea>
                </div>
            </div>
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