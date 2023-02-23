<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTeacherLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_lesson', function (Blueprint $table) {
            $table->date('month_year')->after('content')->nullable();
            $table->date('start_date')->after('month_year')->nullable();
            $table->date('end_date')->after('start_date')->nullable();
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
            $table->dropColumn(['month_year', 'start_date', 'end_date']);
        });
    }
}
