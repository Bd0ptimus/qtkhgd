<?php
Route::group(['prefix' => 'product'], function ($router) {
    $router->get('/', 'ShopFront@allProducts')->name('product.all');
    $router->post('/info', 'ShopFront@productInfo')
        ->name('product.info');
    $router->get('/{name}_{id}', 'ShopFront@productDetail')
        ->where(['id' => '[0-9]+'])
        ->name('product.detail');
});