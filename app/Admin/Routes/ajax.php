<?php
$router->group(['prefix' => 'ajax'], function ($router) {
    $router->get('/get-class-by-branch', 'AjaxController@ajaxGetClassesByBranch')->name('ajax_get_classed_by_branch');
    $router->get('/get-students-by-class', 'AjaxController@getStudentByClass')->name('ajax_get_students_by_class');
    $router->get('/get-system-target-by-id', 'AjaxController@systemTargetById')->name('ajax_get_system_target_by_id');
    $router->get('/get-lesson-by-id', 'AjaxController@getLessonById')->name('ajax_get_lesson_by_id');
    $router->get('/get-teacher-lesson-by-id', 'AjaxController@getTeacherLessonById')->name('ajax_get_teacher_lesson_by_id');
    $router->get('/get-lesson-sample-by-id', 'AjaxController@getLessonSampleById')->name('ajax_get_lesson_sample_by_id');
});
