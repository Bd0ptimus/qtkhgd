<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableLesson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_lesson', function (Blueprint $table) {
            $table->string('ppt')->after('content')->nullable();
            $table->string('video_tbs')->after('content')->nullable();
            $table->string('game_simulator')->after('content')->nullable();
            $table->string('diagram_simulator')->after('content')->nullable();
            $table->longText('homeworks')->after('content')->nullable();
            $table->longText('advanced_exercise')->after('content')->nullable();
            $table->longText('test_question')->after('content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_lesson', function (Blueprint $table) {
            $table->dropColumn('ppt', 'video_tbs', 'game_simulator', 'diagram_simulator', 'homeworks', 'advanced_exercise', 'test_question');
        });
    }
}
