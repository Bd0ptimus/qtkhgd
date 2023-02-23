<?php
//TASKS Route
Route::group(['prefix' => 'common/target', 'as' => 'target.'], function () {
    Route::get("/", "TargetController@index")->name('index');
    Route::any("/create", "TargetController@create")->name('create');
    Route::any("/edit/{id}", "TargetController@edit")->name('edit');
    Route::post('/delete/{id}', 'TargetController@delete')->name('delete');
    Route::any('/download/{id}', 'TargetController@download')->name('download');
    Route::any("/change/select", "TargetController@changeSelectByParam")->name('change.select');
});