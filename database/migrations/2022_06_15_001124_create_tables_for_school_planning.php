<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesForSchoolPlanning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /* 1
        Chỉ định các môn học mà giáo viên giảng dạy  */
        Schema::create('staff_subject', function (Blueprint $table) {
            $table->bigInteger(('staff_id'));
            $table->integer('subject_id');
        });

        /* 2
        Chỉ định giáo viên dạy những cấp học nào */
        Schema::create('staff_grade', function(Blueprint $table) {
            $table->bigInteger(('staff_id'));
            $table->integer('grade');
        });

        /* 3
        Chỉ định các tổ chuyên môn mà giáo viên là thành viên */
        Schema::create('regular_group_staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('regular_group_id');
            $table->bigInteger('staff_id');
            $table->tinyInteger('member_role')->default(3);// 1 To truong 2 To pho 3 Thanh vien 
            $table->timestamps();
        });

        /* 4
        phân loại tổ bộ môn theo khối học */
        Schema::create('regular_group_grade', function (Blueprint $table) {
            $table->bigInteger(('regular_group_id'));
            $table->integer('grade');
        });

        /* 5 
        Phân công môn học cho từng lớp */
        Schema::create('class_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('class_id');
            $table->bigInteger('subject_id'); //get default by grade
            $table->bigInteger('staff_id')->nullable();
            $table->integer('lesson_per_week')->default(1); //Tổng số tiết học ko được vượt quá 30 tiết / tuần 
            $table->timestamps();
        });

        /* 6
         Thời khoá biểu */
        Schema::create('timetable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('school_id');
            $table->bigInteger('school_brand_id');
            $table->date('from_date');
            $table->date('to_date')->nullable(); //get default by grade
            $table->tinyInteger('is_actived')->default(0);
            $table->timestamps();
        });

        /* 7
         TKB phân rã tiết học cho từng lớp  */
         Schema::create('class_lesson', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('class_id');
            $table->bigInteger('timetable_id');
            $table->bigInteger('class_subject_id');
            $table->string('slot')->nullable();
            $table->timestamps();
        });

        /* 8 
        Bổ sung giáo viên chủ nhiệm của lớp */
        Schema::table('class', function (Blueprint $table) {
            $table->bigInteger('homeroom_teacher')->after('class_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_subject');
        Schema::dropIfExists('staff_grade');
        Schema::dropIfExists('regular_group_staff');
        Schema::dropIfExists('regular_group_grade');
        Schema::dropIfExists('class_subject');
        Schema::dropIfExists('timetable');
        Schema::dropIfExists('class_lesson');
        Schema::table('class', function (Blueprint $table) {
            $table->dropColumn('homeroom_teacher');
        });
    }
}
