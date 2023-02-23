<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableLessonsample extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_sample', function (Blueprint $table) {
            
            $table->bigInteger('exercise_id')->after('video_thiet_bi_so')->nullable(true);
            $table->bigInteger('homesheet_id')->after('video_thiet_bi_so')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_sample', function (Blueprint $table) {
            $table->dropColumn(['exercise_id','homesheet_id']);
        });
    }
}
