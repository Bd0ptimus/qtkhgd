<?php
//TASKS Route
Route::group(['prefix' => 'common/exercise-question', 'as' => 'exercise_question.'], function () {
    Route::get("/list", "ExerciseQuestionController@index")->name('index');
    Route::any("/create", "ExerciseQuestionController@create")->name('create');
    Route::any("/edit/{id}", "ExerciseQuestionController@edit")->name('edit');
    Route::post('/delete/{id}', 'ExerciseQuestionController@delete')->name('delete');
    Route::post("/delete-file/{attachmentId}", "ExerciseQuestionController@deleteFile")->name('delete_file')->where('id', '[0-9]+');
    Route::any('/download/{id}', 'ExerciseQuestionController@download')->name('download');
    Route::any("/change/select", "ExerciseQuestionController@changeSelectByParam")->name('change.select');
    Route::get("/download-attach-file/{attachmentId}", "ExerciseQuestionController@downloadAttachFile")->name('download_attach_file')->where('id', '[0-9]+');
});