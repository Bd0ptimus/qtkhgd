<?php 
$router->group(['prefix' => 'sysconf', 'as' => 'sysconf.'], function ($router) {

    $router->any('/import-locations', 'SystemConfig\SystemConfigController@importLocations')->name('import_locations');

    $router->group(['prefix' => 'subject', 'as' => 'subject.'], function ($router) {
        $router->get("/", "SystemConfig\SubjectController@index")->name('index');
        $router->any("/create", "SystemConfig\SubjectController@create")->name('create');
        $router->any("/edit/{id}", "SystemConfig\SubjectController@edit")->name('edit');
        $router->post('/delete/{id}', 'SystemConfig\SubjectController@delete')->name('delete');
        $router->any("/subject-by-grade", "SystemConfig\SubjectController@subjectByGrade")->name('subject_by_grade');
    });

    $router->group(['prefix' => 'regular-group', 'as' => 'regular_group.'], function ($router) {
        $router->get("/", "SystemConfig\RegularGroupController@index")->name('index');
        $router->any("/create", "SystemConfig\RegularGroupController@create")->name('create');
        $router->any("/edit/{id}", "SystemConfig\RegularGroupController@edit")->name('edit');
        $router->post('/delete/{id}', 'SystemConfig\RegularGroupController@delete')->name('delete');
    });
});