<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTeacherPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('regular_group_id');
            $table->bigInteger('staff_id');
            $table->integer('grade');
            $table->integer('subject_id')->nullable(); //th, thcs, thpt
            $table->integer('month')->nullable(); //mn
            $table->longText('chuyen_de')->nullable();
            $table->longText('additional_tasks')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::create('teacher_lesson', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('teacher_plan_id');
            /* THCS & THPT */
            $table->string('bai_hoc')->nullable();
            $table->string('ten_bai_hoc')->nullable();
            $table->string('tiet_thu')->nullable();
            $table->string('so_tiet')->nullable();
            $table->string('thoi_diem')->nullable();
            $table->string('thiet_bi_day_hoc')->nullable();
            $table->string('dia_diem_day_hoc')->nullable();
            
            /* Tieu hoc */
            $table->string('tuan_thang')->nullable();
            $table->string('chu_de')->nullable();
            $table->string('noi_dung_dieu_chinh')->nullable();
            $table->string('ghi_chu')->nullable();

            /* MN */ //'chu_de','noi_dung', 'phoi_hop', 'ket_qua'
            $table->string('thoi_gian')->nullable();
            $table->string('noi_dung')->nullable();
            $table->string('phoi_hop')->nullable();
            $table->string('ket_qua')->nullable();

            /* Bai Giang */
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_plan_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('teacher_plan_id');
            $table->longText('notes');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_plan');
        Schema::dropIfExists('teacher_plan_history');
        Schema::dropIfExists('teacher_lesson');
    }
}
