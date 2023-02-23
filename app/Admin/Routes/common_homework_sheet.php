<?php
//TASKS Route
Route::group(['prefix' => 'common/homework-sheet', 'as' => 'homework_sheet.'], function () {
    Route::get("/list", "HomeworkSheetController@index")->name('index');
    Route::any("/create", "HomeworkSheetController@create")->name('create');
    Route::any("/edit/{id}", "HomeworkSheetController@edit")->name('edit');
    Route::post('/delete/{id}', 'HomeworkSheetController@delete')->name('delete');
    Route::post("/delete-file/{attachmentId}", "HomeworkSheetController@deleteFile")->name('delete_file')->where('id', '[0-9]+');
    Route::any('/download/{id}', 'HomeworkSheetController@download')->name('download');
    Route::any("/change/select", "HomeworkSheetController@changeSelectByParam")->name('change.select');
    Route::get("/download-attach-file/{attachmentId}", "HomeworkSheetController@downloadAttachFile")->name('download_attach_file')->where('id', '[0-9]+');
});