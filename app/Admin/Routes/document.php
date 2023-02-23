<?php
$router->group(['prefix' => 'documents'], function ($router) {
    $router->get('/', 'SystemDocumentController@index')->name('system_document.index');
    $router->get('/create', 'SystemDocumentController@create')->name('system_document.create');
    $router->post('/create', 'SystemDocumentController@store')->name('system_document.store');
    $router->get('/edit/{id}', 'SystemDocumentController@edit')->name('system_document.edit');
    $router->post('/update/{id}', 'SystemDocumentController@update')->name('system_document.update');
    $router->post('/delete', 'SystemDocumentController@delete')->name('system_document.delete');
    $router->get('/files/documents/{id}', 'SystemDocumentController@licenceFileShow')->name('system_document.show');;


});