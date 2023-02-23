<?php
$router->group(['prefix' => 'province'], function ($router) {
    $router->get('/', 'Province\ProvinceController@getList')->name('province.index');
    $router->get('/manage/{id}', 'Province\ProvinceController@manage')->name('province.manage');

    $router->get('/{id}/add-thpt-school', 'Province\SchoolController@addThptSchool')->name('admin.province.add_thpt_school');
    $router->post('/{id}/add-thpt-school', 'Province\SchoolController@postAddThptSchool')->name('admin.province.post_add_thpt_school');
    $router->get('/{id}/edit-thpt-school/{school_id}', 'Province\SchoolController@editThptSchool')->name('admin.province.edit_thpt_school');
    $router->post('/{id}/edit-thpt-school/{school_id}', 'Province\SchoolController@updateThptSchool')->name('admin.province.update_thpt_school');
    $router->get('/{id}/import-school', 'Province\SchoolController@importThptSchool')->name('admin.province.import_thpt_school');
    $router->post('/{id}/import-school', 'Province\SchoolController@postImportThptSchool')->name('admin.province.import_thpt_school');
    $router->get('/{id}/export-accounts', 'Province\SchoolController@exportThptAccount')->name('admin.province.export_thpt_account');
});