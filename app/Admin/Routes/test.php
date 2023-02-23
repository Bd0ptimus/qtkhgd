<?php
//Test Route
Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
    Route::get("/simulator", "TestController@simulator")->name('simulator');
});