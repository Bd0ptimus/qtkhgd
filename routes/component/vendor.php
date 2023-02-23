<?php
Route::group(['prefix' => 'vendor'], function ($router) {
    $router->get('/', 'ShopFront@getVendors')->name('vendors');
    $router->get('/{name}_{id}', 'ShopFront@productToVendor')
        ->where(['id' => '[0-9]+'])->name('vendor');
});