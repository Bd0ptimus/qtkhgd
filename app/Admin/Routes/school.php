<?php
$router->group(['prefix' => 'school'], function ($router) {
    $router->get('/', 'School\SchoolController@index')->name('school.index');
    $router->get('/maugiao-tieuhoc-thcs', 'School\SchoolController@maugiaoTieuhocThcs')->name('school.maugiao_tieuhoc_thcs');
    $router->get('/thpt', 'School\SchoolController@thpt')->name('school.thpt');
    $router->post('/delete-school', 'School\SchoolController@deleteSchool')->name('admin.school.delete_school');
    $router->get('/manage/{id}', 'School\SchoolController@manage')->name('admin.school.manage');
    $router->get('/chuanhoa/{id}', 'School\SchoolController@chuanhoa')->name('admin.school.chuanhoa');
    $router->get('/{id}/users', 'School\SchoolController@users')->name('admin.school.users');
    $router->get('/{id}/parent-accounts', 'School\SchoolController@parentAccounts')->name('admin.school.parent_accounts');
    $router->post('/{id}/users/assign-teacher', 'School\SchoolController@assignTeacherToClass')->name('admin.school.users.assign_teacher');
    $router->get('/{id}/view-branch', 'School\SchoolBranchController@index')->name('admin.school.view_branch_list');
    $router->get('/{id}/add-branch', 'School\SchoolBranchController@addBranch')->name('admin.school.add_branch');
    $router->post('/{id}/add-branch', 'School\SchoolBranchController@postAddBranch')->name('admin.school.post_add_branch');
    $router->get('/edit-branch/{id}', 'School\SchoolBranchController@editBranch')->name('admin.school.edit_branch');
    $router->post('/edit-branch/{id}', 'School\SchoolBranchController@postEditBranch')->name('admin.school.post_edit_branch');
    $router->post('/delete-branch', 'School\SchoolBranchController@deleteBranch')->name('admin.school.delete_branch');
    $router->get('/{school_id}/users/add-with-role/{role_id}', 'School\SchoolController@createSchoolUser')->name('admin.school.add_user');
    $router->post('/{school_id}/create-parent-accounts', 'School\SchoolController@createParentAccount')->name('school.create_parent_account');
    $router->get('/{id}/export-accounts', 'School\SchoolController@exportAccounts')->name('school.export_school_accounts');
    $router->get('/{id}/review-group-plans', 'School\SchoolController@reviewGroupPlans')->name('school.review_group_plan');
    
    /* Staff  */
    $router->any('/{id}/view-staff', 'School\StaffController@viewStaffList')->name('admin.school.view_staff_list');
    $router->get('/{id}/export-staffs', 'School\StaffController@exportStaffs')->name('admin.school.export_staffs');
    $router->get('/{id}/add-staff', 'School\StaffController@addStaff')->name('admin.school.add_staff');
    $router->any('/{id}/linking-staff', 'School\StaffController@linkingStaff')->name('admin.school.linking_staff');
    $router->post('/{id}/add-staff', 'School\StaffController@postAddStaff')->name('admin.school.post_add_staff');
    $router->get('/edit-staff/{id}', 'School\StaffController@editStaff')->name('admin.school.edit_staff');
    $router->post('/edit-staff/{id}', 'School\StaffController@postEditStaff')->name('admin.school.post_edit_staff');
    $router->post('/delete-staff', 'School\StaffController@deleteStaff')->name('admin.school.delete_staff');
    $router->get('/{id}/import-staff', 'School\StaffController@importStaff')->name('admin.school.import_staff');
    $router->post('/{id}/import-staff', 'School\StaffController@postImportStaff')->name('admin.school.import_staff');
    $router->get('/staff/view/{id}', 'School\StaffController@view')->name('admin.staff.view');
    $router->post('staff/assign-branch', 'School\StaffController@assignBranch')->name('admin.school.staff.assign_branch');
    $router->post('staff/assign-class', 'School\StaffController@assignClass')->name('admin.school.staff.assign_class');
     

    $router->get("/{id}/staff/all-staff-plans", "School\TeacherPlanController@allStaff")->name('admin.school.staff.all_staff_plans');
    $router->group(['prefix' => '{school_id}/staff', 'as' => 'school.staff.'], function ($router) {
        $router->any('manage-teacher-grade-and-subject', 'School\StaffController@manageTeacherGradeAndSubject')->name('manage_teacher_grade_and_subject');
        
        $router->group(['prefix' => '/{staffId}'], function ($router) {
            $router->any('/timetable', 'School\StaffController@timetable')->name('timetable');
            $router->any('/timetable/export', 'School\StaffController@exportTimetable')->name('export_timetable');
            $router->any('/regular-group', 'School\StaffController@regularGroups')->name('regular_group');
            $router->get("/all-staff-plan", "School\TeacherPlanController@all")->name('plans');

            $router->group(['prefix' => '/teacher-lesson', 'as' => 'teacher_lesson.'], function ($router) {
                $router->any("/", "School\TeacherPlanController@lessons")->name('index');
                $router->post("/add-review/{lessonId}", "School\TeacherPlanController@addLessonReview")->name('add_review');
                $router->post("/edit/{lessonId}", "School\TeacherPlanController@editLesson")->name('edit');
                $router->any("/select-sample/{lessonId}", "School\TeacherPlanController@selectLessonSample")->name('select_sample');
            });

            $router->group(['prefix' => '/group/{rgId}/staff-plan', 'as' => 'plan.'], function ($router) {
                $router->get("/", "School\TeacherPlanController@index")->name('index');
                $router->any("/create", "School\TeacherPlanController@create")->name('create');
                $router->any("/edit/{planId}", "School\TeacherPlanController@edit")->name('edit');
                $router->any("/submit/{planId}", "School\TeacherPlanController@submit")->name('submit');
                $router->any("/add-review/{planId}", "School\TeacherPlanController@addReview")->name('add_review');
                $router->any("/approve/{planId}", "School\TeacherPlanController@approve")->name('approve');
                $router->post("/delete/{planId}", "School\TeacherPlanController@delete")->name('delete');
                $router->any("/download/{planId}", "School\TeacherPlanController@download")->name('download');
                $router->any("/lesson-submit/{lessonId}", "School\TeacherPlanController@lessonSubmit")->name('lesson_submit');
                $router->any("/lesson-approve/{lessonId}", "School\TeacherPlanController@lessonApprove")->name('lesson_approve');
                $router->any("/lesson-deny/{lessonId}", "School\TeacherPlanController@lessonDeny")->name('lesson_deny');
            });
        });
    });

    /* Class */
    $router->get('/edit-class/{id}', 'School\ClassController@editClass')->name('admin.school.edit_class');
    $router->get('/{id}/add-class', 'School\ClassController@addClass')->name('admin.school.add_class');
    $router->post('/{id}/add-class', 'School\ClassController@postAddClass')->name('admin.school.post_add_class');
    $router->post('/edit-class/{id}', 'School\ClassController@postEditClass')->name('admin.school.post_edit_class');
    $router->post('/delete-class', 'School\ClassController@deleteClass')->name('admin.school.delete_class');
    $router->post('/{id}/delete-all-class', 'School\ClassController@deleteAllClass')->name('admin.school.delete_all_class');
    $router->get('/{id}/view-class', 'School\ClassController@viewClassList')->name('admin.school.view_class_list');
    $router->get('/{id}/import-class', 'School\ClassController@importClass')->name('admin.school.import_class');
    $router->post('/{id}/import-class', 'School\ClassController@postImportClass')->name('admin.school.import_class');
    
    /* Phan bo mon hoc va giao vien  */
    $router->group(['prefix' => '{id}/teaching-assignment', 'as' => 'school.teaching_assignment.'], function ($router) {
        $router->any("/homeroom-teacher", "School\TeachingAssignmentController@homeroomTeacher")->name('homeroom_teacher');
        $router->any("/teacher-classes", "School\TeachingAssignmentController@teacherClasses")->name('teacher_classes');
        $router->any("/class-subjects", "School\TeachingAssignmentController@classSubjects")->name('class_subjects');
    });

    /* School activities*/
    $router->any('/{school_id}/activities', 'AdminLogController@userActivity')->name('admin.school.user_activity');
    $router->any('/{school_id}/summary-activities', 'AdminLogController@summaryActivities')->name('admin.school.activities_summary');

    /* School list - SoGD PhongGD*/
    $router->get('/school-list', 'School\SchoolController@schoolList')->name('school.school_list');
    $router->get('/school-list/export', 'School\SchoolController@exportSchoolList')->name('school.export_school_list');

    /* Subject Management */
    $router->group(['prefix' => '{id}/subject', 'as' => 'school.subject.'], function ($router) {
        $router->get("/", "School\SubjectController@index")->name('index');
        $router->any("/create", "School\SubjectController@create")->name('create');
        $router->any("/edit/{subject_id}", "School\SubjectController@edit")->name('edit');
        $router->post("/delete/{subject_id}", "School\SubjectController@delete")->name('delete');
        $router->any("/subject-by-grade", "School\SubjectController@subjectByGrade")->name('subject_by_grade');
    });

    /* Regular Group Management */
    $router->group(['prefix' => '{id}/regular-group', 'as' => 'school.regular_group.'], function ($router) {
        $router->get("/init", "School\RegularGroupController@init")->name('init');
        $router->get("/", "School\RegularGroupController@index")->name('index');
        $router->any("/create", "School\RegularGroupController@create")->name('create');
        $router->any("/plan", "School\RegularGroupController@plan")->name('plan');
        $router->any("/assign-leader", "School\RegularGroupController@assignLeaders")->name('assign_leader');
        $router->any("/edit/{rgId}", "School\RegularGroupController@edit")->name('edit');
        $router->post("/delete/{rgId}", "School\RegularGroupController@delete")->name('delete');
        $router->get("{rgId}/staffs", "School\RegularGroupController@staffs")->name('staffs');
        $router->any("{rgId}/review-teacher-plans", "School\RegularGroupController@reviewTeacherPlans")->name('review_teacher_plans');
        $router->any("{rgId}/teacher-plans", "School\RegularGroupController@teacherPlans")->name('teacher_plans');
        $router->any("{rgId}/review-teacher-lessons", "School\RegularGroupController@reviewTeacherLessons")->name('review_teacher_lessons');


        $router->group(['prefix' => '/{rgId}/group-plan', 'as' => 'plan.'], function ($router) {
            $router->get("/", "School\RegularGroupPlanController@index")->name('index');
            $router->any("/create", "School\RegularGroupPlanController@create")->name('create');
            $router->any("/edit/{planId}", "School\RegularGroupPlanController@edit")->name('edit');
            $router->any("/download/{planId}", "School\RegularGroupPlanController@download")->name('download');
            $router->any("/submit/{planId}", "School\RegularGroupPlanController@submit")->name('submit');

            $router->any("/add-review/{planId}", "School\RegularGroupPlanController@addReview")->name('add_review');
            $router->any("/approve/{planId}", "School\RegularGroupPlanController@approve")->name('approve');

            $router->post("/delete/{planId}", "School\RegularGroupPlanController@delete")->name('delete');
        });
    });

    /* Timetable Management  */
    $router->group(['prefix' => '{id}/timetable', 'as' => 'school.timetable.'], function ($router) {
        $router->get("/", "School\TimetableController@index")->name('index');
        $router->get("/auto-generate-timetable", "School\TimetableController@autoGenerateTimetable")->name('auto_genderate');
        $router->get("/view/{timetableId}", "School\TimetableController@view")->name('view');
        $router->any("/edit/{timetableId}", "School\TimetableController@edit")->name('edit');
        $router->any("/active/{timetableId}", "School\TimetableController@active")->name('active');
        $router->any("/staff/{stafId}", "School\TimetableController@staffTimetable")->name('staff_timetable');
        $router->post("/delete/{timetableId}", "School\TimetableController@delete")->name('delete');
    });

    /* School Plan Management  */
    $router->group(['prefix' => '{id}/school-plan', 'as' => 'school.school_plan.'], function ($router) {
        $router->get("/", "School\SchoolPlanController@index")->name('index');
        $router->any("/create", "School\SchoolPlanController@create")->name('create');
        $router->any("/upload", "School\SchoolPlanController@upload")->name('upload');
        $router->get("/view/{planId}", "School\SchoolPlanController@view")->name('view');
        $router->any("/edit/{planId}", "School\SchoolPlanController@edit")->name('edit');
        $router->any("/download/{planId}", "School\SchoolPlanController@download")->name('download');
        $router->get("/submit/{planId}", "School\SchoolPlanController@submit")->name('submit');
        $router->post("/delete/{planId}", "School\SchoolPlanController@delete")->name('delete');
    });

    /* School Target  */
    $router->group(['prefix' => '{id}/target', 'as' => 'school.target.'], function ($router) {
        $router->get("/", "School\SchoolTargetController@index")->name('index');
        $router->any("/create", "School\SchoolTargetController@create")->name('create');
        $router->any("/edit/{targetId}", "School\SchoolTargetController@edit")->name('edit');
        //$router->any("/result/{targetId}", "School\SchoolTargetController@result")->name('result'); //route to open result.blade
        $router->any("/summary-target/{targetId}", "School\SchoolTargetController@summaryTarget")->name('summary_target');
        $router->any("/assign-staff/{targetId}", "School\SchoolTargetController@assignStaff")->name('assign_staff');
        $router->post("/delete/{targetId}", "School\SchoolTargetController@delete")->name('delete');
        $router->post("/result/{targetId}", "School\SchoolTargetController@result")->name('result');// route to show popup
        $router->post("/result-point/{pointId}", "School\SchoolTargetController@resultPoint")->name('result.point');// route to show popup
        $router->post("/result-main-point/{pointId}", "School\SchoolTargetController@resultMainPoint")->name('result.main.point');// route to show popup
        $router->post('/delete-point/{pointId}', 'School\SchoolTargetController@deletePointById')->name('delete.point');
        $router->get('/get-assign-point/{pointId}', 'School\SchoolTargetController@getAssignPoint')->name('assign.point');
        $router->get('/get-sub-points/{pointId}', 'School\SchoolTargetController@getSubPoints')->name('get.sub.point');
    });


    $router->get('/{id}/view-student-list', 'School\StudentController@viewStudentList')->name('admin.school.view_student_list');
    $router->get('/{id}/import-student', 'School\StudentController@importStudent')->name('admin.school.import_student');
    $router->post('/{id}/import-student', 'School\StudentController@postImportStudent')->name('admin.school.import_student');
    $router->any('/{id}/import-student-smas', 'School\StudentController@importStudentSmas')->name('admin.school.import_student_smas');
    $router->get('/{id}/export-student', 'School\StudentController@exportStudent')->name('admin.school.export_student');
    $router->post('/{id}/student/delete-all-students/{class_id}', 'School\StudentController@deleteAllStudentsByClass')->name('admin.student.delete_all_by_class');  

    $router->get('/student/view/{id}', 'School\StudentController@view')->name('admin.student.view');
    $router->get('/student/edit/{id}', 'School\StudentController@edit')->name('admin.student.edit');
    $router->post('/student/edit/{id}', 'School\StudentController@postEdit')->name('admin.student.edit');
    $router->post('/student/delete', 'School\StudentController@delete')->name('admin.student.delete');
    $router->get('/{school_id}/create-student/', 'School\StudentController@createBySchool')->name('admin.school.create_student');
    $router->post('/{school_id}/create-student/', 'School\StudentController@storeCreateBySchool')->name('admin.school.create_student');
    $router->post('/student/assign-class', 'School\StudentController@assignClass')->name('admin.school.assign_class_student');
    $router->get('/{id}/parent-accounts', 'School\SchoolController@parentAccounts')->name('admin.school.parent_accounts');
});