<?php
$router->group(['prefix' => 'district', 'as' => 'district.'], function ($router) {
    $router->get('/', 'District\DistrictController@getList')->name('index');
    $router->get('/users/{id}', 'District\DistrictController@users')->name('users');
    $router->get('/manage/{id}', 'District\DistrictController@manage')->name('manage');
    $router->get('/manage/{id}/view-school', 'District\DistrictController@schoolList')->name('manage.view_school');
    $router->get('/specialist-users', 'District\DistrictSpecialistController@getSpecialistUserList')->name('specialist_users');
    $router->get('/manage/{district_id}/users/add-with-role/{role_id}', 'District\DistrictSpecialistController@createDistrictUser')->name('manage.add_user');
    $router->get('/specialist-school', 'District\DistrictSpecialistController@getSpecialistSchool')->name('manage.specialist_school');
    $router->post('/create-specialist-school', 'District\DistrictSpecialistController@assignSpecialistSchool')->name('store.specialist_school');
    $router->post('/add-specialist-user', 'District\DistrictSpecialistController@postAddUserDistrict')->name('post.add_user');
    $router->put('/edit-specialist-user', 'District\DistrictSpecialistController@putEditUserDistrict')->name('put.edit_user');

    $router->group(['prefix' => '{districtId}/schools', 'as' => 'schools.'], function ($router) {
        $router->get('/review-school-plans', 'District\DistrictController@reviewSchoolPlans')->name('pending_school_plans');
        $router->post('/add-review-school-plan/{planId}', 'District\DistrictController@addReviewSchoolPlan')->name('add_review_school_plan');
        $router->any('/approve-school-plans/{planId}', 'District\DistrictController@approveSchoolPlan')->name('approve_school_plan');
        $router->get('/school-plans', 'District\DistrictController@schoolPlans')->name('school_plans');
        $router->get('/group-plans', 'District\DistrictController@groupPlans')->name('group_plans');
        $router->get('/teacher-plans', 'District\DistrictController@teacherPlans')->name('teacher_plans');
    });
});