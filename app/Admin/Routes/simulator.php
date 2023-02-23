<?php
//TASKS Route
Route::group(['prefix' => 'common/simulator', 'as' => 'simulator.'], function ($router) {
    $router->get('/', 'SimulatorController@index')->name('index');
    $router->get('/view/{simulatorId}', 'SimulatorController@view')->name('view');
    $router->any('/create', 'SimulatorController@create')->name('create');
    $router->any('/edit/{simulatorId}', 'SimulatorController@edit')->name('edit');
    $router->get('/delete/{simulatorId}', 'SimulatorController@delete')->name('delete');
    $router->post('/group-change', 'SimulatorController@groupChange')->name('group-change');

});