<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulators', function (Blueprint $table) {
            $table->increments('id');
            $table->String('name_simulator')->nullable(false);
            $table->Integer('subject_id')->nullable(false);
            $table->String('related_lesson');
            $table->longText('user_guide')->nullable(false);
            $table->String('url_simulator');
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
        Schema::dropIfExists('simulators');
    }
}
