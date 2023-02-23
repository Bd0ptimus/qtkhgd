<?php
Route::group(['prefix' => 'common/ebooks', 'as' => 'ebooks.'], function () {
    Route::get("/", "EbookController@index")->name('index');
    Route::get("/view/{id}", "EbookController@show")->name('show')->where('id', '[0-9]+');
    Route::get("/download/{id}", "EbookController@download")->name('download')->where('id', '[0-9]+');
    Route::any("/create", "EbookController@create")->name('create');
    Route::any("/edit/{id}", "EbookController@edit")->name('edit')->where('id', '[0-9]+');
    Route::post("/delete/{id}", "EbookController@delete")->name('delete')->where('id', '[0-9]+');
});