<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPptForLessonSampleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_sample', function (Blueprint $table) {
            $table->String('game_simulator')->after('content')->nullable(true);
            $table->String('diagram_simulator')->after('content')->nullable(true);

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
            $table->dropColumn(['game_simulator','diagram_simulator']);
        });
    }
}
