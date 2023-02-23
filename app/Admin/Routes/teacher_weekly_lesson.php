<?php
Route::group(['prefix' => 'common/teacher-weekly-lesson', 'as' => 'teacher_weekly_lesson.'], function () {
    Route::get("/", "TeacherWeeklyLessonController@index")->name('index');
    Route::get("/create", "TeacherWeeklyLessonController@create")->name('create');
    Route::post("/store", "TeacherWeeklyLessonController@store")->name('store');
    Route::get("/edit/{weeklyLessonId}", "TeacherWeeklyLessonController@edit")->name('edit');
    Route::put("/update/{weeklyLessonId}", "TeacherWeeklyLessonController@update")->name('update');
    Route::delete("/destroy/{weeklyLessonId}", "TeacherWeeklyLessonController@destroy")->name('destroy');
    Route::get("/lessons", "TeacherWeeklyLessonController@getLessonByGradeAndSubject")->name('lessons');
    Route::get("/export", "TeacherWeeklyLessonController@export")->name('export');
    Route::patch("/update_progress", "TeacherWeeklyLessonController@updateProgress")->name('update-progress');
});
