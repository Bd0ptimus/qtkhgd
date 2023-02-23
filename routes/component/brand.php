<?php
Route::group(['prefix' => 'brand'], function ($router) {
    $router->get('/', 'ShopFront@getBrands')->name('brands');
    $router->get('/{name}_{id}', 'ShopFront@productToBrand')
        ->where(['id' => '[0-9]+'])->name('brand');
});

Route::get('/checkout.html', 'ShopCart@getCheckout')
->name('checkout');
Route::post('/checkout.html', 'ShopCart@processCart')
->name('checkout.prepare');

Route::post('/order_add', 'ShopCart@addOrder')
->name('order.add');