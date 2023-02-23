<?php
$router->group(['prefix' => 'ward'], function ($router) {
    $router->get('/', 'Ward\WardController@getList')->name('ward.index');
    $router->get('/manage/{id}', 'Ward\WardController@manage')->name('ward.manage');
    $router->get('/manage/{id}/school-activity-list', 'Ward\WardController@schoolActivityList')->name('ward.manage.view_school');
    $router->get('/manage/{id}/school-list', 'Ward\WardController@schoolList')->name('ward.manage.school_list');
    $router->get('/manage/{id}/school-list/export', 'Ward\WardController@exportSchoolList')->name('ward.manage.school_list.export');
});