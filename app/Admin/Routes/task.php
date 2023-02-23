<?php
//TASKS Route
Route::group(['prefix' => 'common', 'as' => 'tasks.'], function () {
    Route::get("/tasks", "TaskController@index")->name('index');
    Route::get("/json-tasks", "TaskController@toJsonTasks")->name('json-tasks');
    Route::get("/tasks/create", "TaskController@create")->name('create');
    Route::get("/tasks/{id}", "TaskController@show")->name('show')->where('id', '[0-9]+');
    Route::post("/tasks", "TaskController@store")->name('store');
    Route::put("/tasks/{id}", "TaskController@update")->name('update')->where('id', '[0-9]+');
    Route::post("/tasks/{id}", "TaskController@destroy")->name('destroy')->where('id', '[0-9]+');
    Route::post("/comment", "TaskController@storeComment")->name('comment');
});