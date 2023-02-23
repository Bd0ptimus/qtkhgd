<?php
$router->group(['prefix' => 'notification'], function ($router) {
    $router->get('/', 'NotificationController@index')->name('notification.index');
    $router->get('/view/{id}', 'NotificationController@view')->name('notification.view');
});