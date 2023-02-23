<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulatorGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulator_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('simulator_id')->unsigned();
            $table->integer('grade');

            $table->foreign('simulator_id')->references('id')->on('simulators')->onDelete('Cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simulator_grades');
    }
}
