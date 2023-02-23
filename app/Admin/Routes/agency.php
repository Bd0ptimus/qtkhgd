<?php
$router->group(['prefix' => 'agency'], function ($router) {
    $router->get('/', 'AgencyController@index')->name('admin.agency.index');
    $router->get('/provinces', 'AgencyController@provinces')->name('admin.agency.provinces');
    $router->get('/districts', 'AgencyController@districts')->name('admin.agency.districts');
    $router->get('/wards', 'AgencyController@wards')->name('admin.agency.wards');
    $router->post('/wards', 'AgencyController@wards')->name('admin.agency.wards');
    $router->get('/provinces/{id}/view-account-list', 'AgencyController@viewSgdAccountList')->name('admin.agency.province.view_account_list');
    $router->get('/districts/{id}/view-account-list', 'AgencyController@viewPgdAccountList')->name('admin.agency.district.view_account_list');
    $router->get('/wards/{id}/view-account-list', 'AgencyController@viewWardAccountList')->name('admin.agency.ward.view_account_list');

    $router->get('/districts/{id}/export-accounts', 'AgencyController@exportAccount')->name('admin.agency.districts.export_account');
    $router->get('/districts/{id}/add-school', 'AgencyController@addSchool')->name('admin.agency.districts.add_school');
    $router->post('/districts/{id}/add-school', 'AgencyController@postAddSchool')->name('admin.agency.districts.post_add_school');
    $router->get('/districts/{id}/import-school', 'AgencyController@importSchool')->name('admin.agency.districts.import_school');
    $router->post('/districts/{id}/import-school', 'AgencyController@postImportSchool')->name('admin.agency.districts.import_school');
    $router->get('/districts/{id}/edit-school/{school_id}', 'AgencyController@editSchool')->name('admin.agency.districts.edit_school');
    $router->post('/districts/{id}/edit-school/{school_id}', 'AgencyController@updateSchool')->name('admin.agency.districts.update_school');
});
