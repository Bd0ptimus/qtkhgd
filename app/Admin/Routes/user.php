<?php
$router->group(['prefix' => 'user'], function ($router) {
    $router->get('/', 'Auth\UsersController@index')->name('admin_user.index');
    $router->get('create', 'Auth\UsersController@create')->name('admin_user.create');
    $router->post('/create', 'Auth\UsersController@postCreate')->name('admin_user.create');
    $router->post('/delete', 'Auth\UsersController@deleteList')->name('admin_user.delete');
    $router->get('/view_user/{id}', 'Auth\UsersController@viewUser')->name('admin_user.view_user');
   
    $router->patch('/plus_balance/{user_id}', 'Auth\UsersController@plusBalance')->name('admin_user.plus_balance');
    $router->patch('/minus_balance/{user_id}', 'Auth\UsersController@minusBalance')->name('admin_user.dminus_balance');

    $router->get('/create/sgd/{gso_id}', 'Auth\UsersController@createSgdAccount')->name('admin_user.create_sgd_account');
    $router->get('/create/more-sgd/{gso_id}', 'Auth\UsersController@createSgdAccount')->name('admin_user.create_more_sgd_account');
    $router->get('/create/more-ttyt-sgd/{gso_id}', 'Auth\UsersController@createTtytSgdAccount')->name('admin_user.create_more_ttyt_sgd_account');
    $router->get('/create/pgd/{gso_id}', 'Auth\UsersController@createPgdAccount')->name('admin_user.create_pgd_account');
    $router->get('/create/more-pgd/{gso_id}', 'Auth\UsersController@createPgdAccount')->name('admin_user.create_more_pgd_account');
    $router->get('/create/more-ttyt-pgd/{gso_id}', 'Auth\UsersController@createTtytPgdAccount')->name('admin_user.create_more_ttyt_pgd_account');
    $router->get('/create/more-ttyt-ward/{gso_id}', 'Auth\UsersController@createTtytWardAccount')->name('admin_user.create_more_ttyt_ward_account');

    $router->get('/route/{route_name}', 'School\SchoolController@route')->name('school_route');
});
