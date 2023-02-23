<?php
//FILEUPLOAD
Route::group(['as' => 'files.'], function () {
    Route::post("/fileupload", "FileUploadController@save")->name('save');
    //UPLOAD IMPORT FILE
    Route::post("/upload-import-file", "FileUploadController@uploadImportFiles")->name('import');
    //TINYMCE IMAGE FILEUPLOAD
    Route::post("/upload-tinymce-image", "FileUploadController@saveTinyMCEImage")->name('tinymce');
    //COVER IMAGE UPLAOD
    Route::post("/upload-cover-image", "FileUploadController@uploadCoverImage")->name('cover');
});