<?php
Route::group(['prefix' => 'category'], function ($router) {
    $router->get('/', 'ShopFront@getCategories')->name('categories');
    $router->get('/{name}_{id}', 'ShopFront@productToCategory')
        ->where(['id' => '[0-9]+'])->name('category');
});