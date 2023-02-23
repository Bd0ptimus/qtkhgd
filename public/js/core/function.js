/** ----------------------------------------------------------
 *  - tiny mce
 *  - basic fixed height of 300px
 *  - reinitialized by nextloop ajax
 * @param numeric tinyMCEHeight optional height setting
 * @param numeric tinyMCESelector optional element selector
 * ---------------------------------------------------------*/
function nxTinyMCEBasic(tinyMCEHeight = 400, tinyMCESelector = '.tinymce-textarea') {

    //remove
    tinymce.remove(tinyMCESelector);
    //initialize
    tinymce.init({
        selector: tinyMCESelector,
        mode: 'exact',
        theme: "modern",
        skin: 'light',
        branding: false,
        resize: true,
        menubar: false,
        statusbar: false,
        forced_root_block: false,
        autoresize_min_height: 300,
        plugins: [
            "fullscreen image paste link code media autoresize codesample",
            "table hr pagebreak toc advlist lists textcolor",
            "imagetools contextmenu colorpicker",
            "tiny_mce_wiris",
        ],
        height: tinyMCEHeight,
        toolbar: 'bold link bullist numlist image media alignleft aligncenter alignright outdent indent hr table code fullscreen',
        paste_data_images : true,
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.on('FullscreenStateChanged', function (e) {
                if (e.state) {
                    $('.modal-dialog').attr('style', 'transform: none !important');
                } else {
                    $('.modal-dialog').attr('style', 'transform: translate(0,0)');
                }
            });
        },
        //upload images
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '/portal/upload-tinymce-image');
            xhr.setRequestHeader("X-CSRF-Token", NX.csrf_token);
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
        }
    });
}

$(document).ready(function () {
    //$('#due-date.due-date').attr('disabled', true);
    $('#start-date').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
        startDate: new Date(),
        minDate: moment()
    });
    $('#start-date').datepicker("setDate", new Date());

    $('#due-date').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
        minDate: moment(),
        startDate: new Date()
    });
    // $('#due-date').datepicker("startDate", new Date());

    $('#start-date').datepicker().on('changeDate', function(ev) {
        //$('#due-date.due-date').attr('disabled', false);
        let start_date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0, 0);
        $('#due-date').datepicker('setStartDate',start_date);
    });

    $('#due-date').datepicker().on('changeDate', function(ev) {
        let end_date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0, 0);
        $('#start-date').datepicker('setEndDate',end_date);
    });

    $('#start-date-edit').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
        startDate: new Date(),
        minDate: moment()
    });

    $('#due-date-edit').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
        minDate: moment(),
        startDate: new Date()
    });

    // $('#start-date-edit').datepicker().on('changeDate', function(ev) {
    //     //$('#due-date.due-date').attr('disabled', false);
    //     let start_date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0, 0);
    //     $('#due-date-edit').datepicker('setStartDate',start_date);
    // });

    // $('#due-date-edit').datepicker().on('changeDate', function(ev) {
    //     let end_date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0, 0);
    //     $('#start-date-edit').datepicker('setEndDate',end_date);
    // });

    $('#filter-start-date').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        autoclose: true,
        todayHighlight: true,
    });
    $('#filter-due-date').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        autoclose: true,
        todayHighlight: true,
    });
    $('#filter-created-date').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        autoclose: true,
        todayHighlight: true,
    });

    /**-------------------------------------------------------------
     * FILE UPLOAD TOGGLE
     * ------------------------------------------------------------*/
    $(document).off('click', '#js-card-toggle-fileupload').on('click', '#js-card-toggle-fileupload', function () {
        $(document).find("#card-fileupload-container").toggle();
    });

    //focus on the editor
    tinymce.execCommand('mceFocus', true, 'card-comment-tinmyce');

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
            "paste autoresize",
            "tiny_mce_wiris",
        ],
        height: 700,
        toolbar: 'fontselect fontsizeselect forecolor backcolor bold link bullist numlist alignleft aligncenter alignright image hr table tiny_mce_wiris_formulaEditor code fullscreen',
        fontsize_formats:"8pt 9pt 10pt 11pt 12pt 13pt 14pt 18pt 24pt 30pt 36pt 48pt 60pt 72pt 96pt",
        content_style: "body {font-size: 13pt; font-family:'Times New Roman, Times, serif;}",
        paste_word_valid_elements: "b,strong,i,em,h1,h2,u,p,ol,ul,li,a[href],span,color,font-size,font-color,font-family,mark,table,tr,td",
        paste_retain_style_properties: "all",
        // paste_data_images : true,
        // paste_as_text: true,
        lists_indent_on_tab: false,
        // paste_enable_default_filters: true,
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });

            //handle paste event
            editor.on('paste', function(e, o){
                var clipBoardData = e.clipboardData.getData('text');
                //check content in clipboard is eqaution or not
                if(clipBoardData.includes('xmlns:mml="http://www.w3.org/1998/Math/MathML"')){
                //clear useless symbols 
                var dataForReplace = ["mml", ':'];
                for(var i in dataForReplace){
                    clipBoardData = clipBoardData.replaceAll(dataForReplace[i],'');
                }
                //merge data
                var currentContent = tinyMCE.activeEditor.getContent();
                tinymce.activeEditor.setContent(currentContent + clipBoardData);   
                }      
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


    tinymce.execCommand('mceFocus', true, 'task-description');
    tinymce.execCommand('mceRemoveEditor', true, 'task-description');
    tinymce.init({

        selector: '.task-description',
        mode: 'exact',
        theme: "modern",
        skin: 'light',
        branding: false,
        menubar: false,
        statusbar: false,
        forced_root_block: false,
        // document_base_url: NX.site_url,
        plugins: [
            "advlist autolink lists link image preview  codesample table hr",
            "paste autoresize",
            "tiny_mce_wiris",
        ],
        height: 450,
        toolbar: 'bold link bullist numlist alignleft aligncenter alignright image hr table code table fullscreen',
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
        }
    });
});

document.getElementById('add-file').addEventListener('click', () => {
    document.getElementById('file-upload').click();
});

//update parameter when change filter
function updateParameter(urlCurrent, key, value)
{
    let url = new URL(urlCurrent);
    let search_params = url.searchParams;

    // new value of "key" is set to "value"
    search_params.set(key, value);

    // change the search property of the main url
    url.search = search_params.toString();

    return url.toString();
}