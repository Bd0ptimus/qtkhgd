<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regular_group_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('regular_group_id');
            $table->integer('grade');
            $table->integer('subject')->nullable(); //th, trung hoc
            $table->integer('month')->nullable(); //mamnon
            $table->longText('can_cu_xay_dung')->nullable(); //tieu hoc
            $table->longText('dieu_kien_thuc_hien')->nullable(); //tieu hoc
            $table->longText('to_chuc_thuc_hien')->nullable(); //tieu hoc
            $table->longText('content')->nullable(); //trung hoc, mamnon
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::create('group_subject_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_plan_id');
            $table->bigInteger('subject_id');  // get subject by grade
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        Schema::create('group_plan_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_plan_id');
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
        Schema::dropIfExists('regular_group_plan');
        Schema::dropIfExists('group_subject_plan');
        Schema::dropIfExists('group_plan_history');
    }
}
