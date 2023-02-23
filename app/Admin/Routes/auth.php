<?php

$router->group(['prefix' => 'auth'], function ($router) {
    $authController = Auth\LoginController::class;
    $router->get('login', $authController . '@getLogin')->name('admin.login');
    $router->post('login', $authController . '@postLogin')->name('admin.login');
    $router->get('logout', $authController . '@getLogout')->name('admin.logout');
    $router->get('setting', $authController . '@getSetting')->name('admin.setting');
    $router->post('setting', $authController . '@putSetting')->name('admin.setting');
    $router->any('identify', $authController . '@identify')->name('admin.identify');
     /* Edit - reset password school's accounts*/
    $router->get('/user/edit/{id}', 'Auth\UsersController@edit')->name('admin_user.edit');
    $router->post('/user/edit/{id}', 'Auth\UsersController@postEdit')->name('admin_user.edit');
    $router->get('/user/reset_password/{id}', 'Auth\UsersController@resetPassword')->name('admin_user.reset_password');
    $router->patch('user/update_status/{user_id}', 'Auth\UsersController@updateStatus')->name('admin_user.update_status');
});

$router->any('select-module', 'HomeController@selectModule')->name('admin.select_module');
