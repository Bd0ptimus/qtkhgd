<?php
$router->group(['prefix' => 'common/lesson-sample', 'as' => 'lesson_sample.'], function ($router) {
    $router->get("/", "LessonSampleController@index")->name('index');
    $router->get("/view/{id}", "LessonSampleController@show")->name('show')->where('id', '[0-9]+');
    $router->get("/download/{id}", "LessonSampleController@download")->name('download')->where('id', '[0-9]+');
    $router->get("/download-attach-file/{attachmentId}", "LessonSampleController@downloadAttachFile")->name('download_attach_file')->where('id', '[0-9]+');
    $router->any("/create", "LessonSampleController@create")->name('create');
    $router->any("/edit/{id}", "LessonSampleController@edit")->name('edit')->where('id', '[0-9]+');
    $router->post("/delete/{id}", "LessonSampleController@delete")->name('delete')->where('id', '[0-9]+'); 
    $router->post("/delete-file/{attachmentId}", "LessonSampleController@deleteFile")->name('delete_file')->where('id', '[0-9]+');;  
    $router->get("/up-lesson", "LessonSampleController@upLesson")->name('up.lesson');
    $router->get("/homework/sheet", "LessonSampleController@getDataHomeworkSheet")->name('homework.sheet');
    $router->get("/exercise/question", "LessonSampleController@getDataExerciseQuestion")->name('exercise.question');
    $router->get("/game", "LessonSampleController@getDataGame")->name('game');
    $router->get("/simulation", "LessonSampleController@getDataSimulation")->name('simulation');
    $router->any("/edit/{id}", "LessonSampleController@edit")->name('edit')->where('id', '[0-9]+');

    $router->group(['prefix' => '{id}/lesson_content', 'as' => 'lesson_content.'], function ($router) {
        $router->get("/", "LessonSampleContentController@index")->name('index');
        $router->any("/create", "LessonSampleContentController@create")->name('create');
        $router->any("/edit/{lesson_sample_id}", "LessonSampleContentController@edit")->name('edit');
        $router->post("/delete/{lesson_sample_id}", "LessonSampleContentController@delete")->name('delete');       
    });
});