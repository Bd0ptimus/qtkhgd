<?php 
$router->any('truncate_data', 'Auth\LoginController@truncateData')->name('admin.truncate_data');
$router->get('set-year/{year}', 'HomeController@changeYear')->name('system.change_year');