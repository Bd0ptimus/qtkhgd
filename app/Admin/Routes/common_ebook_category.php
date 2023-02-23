<?php
Route::group(['prefix' => 'common/ebook-categories', 'as' => 'ebook-categories.'], function () {
    Route::get("/", "EbookCategoryController@index")->name('index');
    Route::get("/view/{id}", "EbookCategoryController@show")->name('show')->where('id', '[0-9]+');
    Route::any("/create", "EbookCategoryController@create")->name('create');
    Route::any("/edit/{id}", "EbookCategoryController@edit")->name('edit')->where('id', '[0-9]+');
    Route::post("/delete/{id}", "EbookCategoryController@destroy")->name('delete')->where('id', '[0-9]+');
});