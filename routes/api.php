<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(
    [
        'middleware' => 'api',
        'name' => 'api',
        'namespace' => '\App\Modules\Api\Controllers',
    ],
    function ($router) {
        $router->group(['prefix' => 'auth', 'name' => 'auth.'], function ($router) {
            $router->post('/login', 'AuthController@login')->name('login');
            $router->group(['middleware' => 'auth:api'], function ($router) {
                $router->get('/user', 'AuthController@checkUser')->name('check-user');
                $router->post('/user/change-password', 'AuthController@changePassword')->name('change-password');
            });
        });

        $router->group(['middleware' => 'auth:api'], function ($router) {
            $router->group(['prefix' => 'health', 'name' => 'health.'], function ($router) {
                $router->get('/category', 'HealthController@getCategories')->name('categories');
                $router->get('/post', 'HealthController@getPosts')->name('posts');
                $router->get('/post/{post}', 'HealthController@getPostDetail')->name('postDetail');
            });
            $router->group(['prefix' => 'medical', 'name' => 'medical.'], function ($router) {
                $router->get('/students', 'MedicalController@getStudents')->name('students');
                $router->get('/students/{id}', 'MedicalController@getStudentDetail')->name('student-detail');
                $router->get('/students/{id}/medicines', 'MedicalController@getMedicinesByStudent')->name('student-medicines');
                $router->get('/students/{id}/equipments', 'MedicalController@getEquipmentByStudent')->name('student-equipment');
                $router->get('/students/{id}/health-index', 'MedicalController@getStudentHealthIndex')->name('student-health-index');
                $router->get('/students/{id}/health-abnormal', 'MedicalController@getStudentHealthAbnormal')->name('student-health-abnormal');
                $router->get('/students/{id}/health-abnormal/{abnormalId}', 'MedicalController@getStudentDetailHealthAbnormal')->name('student-get-detail-health-abnormal');
                $router->post('/students/{id}/health-abnormal/add', 'MedicalController@addStudentHealthAbnormal')->name('student-add-health-abnormal');
                $router->get('/students/{id}/special-test', 'MedicalController@getStudentSpecialTest')->name('student-special-test');
            });
            $router->group(['prefix' => 'notification', 'name' => 'notification.'], function ($router) {
                $router->get('/', 'NotificationController@getNotifications')->name('list');
                $router->post('/token', 'NotificationController@setToken')->name('set-token');
                $router->post('/read/{id}', 'NotificationController@readNotification')->name('read');
            });
        });
    });
