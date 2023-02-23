<?php
Route::group(['prefix' => 'member', 'middleware' => 'auth'], function ($router) {
    $router->get('/', 'ShopAccount@index')->name('member.index');
    $router->get('/order_list.html', 'ShopAccount@orderList')
        ->name('member.order_list');
    $router->get('/change_password.html', 'ShopAccount@changePassword')
        ->name('member.change_password');
    $router->post('/change_password.html', 'ShopAccount@postChangePassword')
        ->name('member.post_change_password');
    $router->get('/change_infomation.html', 'ShopAccount@changeInfomation')
        ->name('member.change_infomation');
    $router->post('/change_infomation.html', 'ShopAccount@postChangeInfomation')
        ->name('member.post_change_infomation');
});