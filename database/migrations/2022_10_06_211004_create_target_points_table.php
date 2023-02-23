<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("target_id");
            $table->string("content");
            $table->float("final_point",5,2)->nullable(true);
            $table->integer("index_point");
            $table->float("result",5,2)->nullable(true);
            $table->unsignedBigInteger("staff_id")->nullable(true);
            $table->unsignedBigInteger("class_id")->nullable(true);
            $table->unsignedBigInteger("main_point")->nullable(true);
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
        Schema::dropIfExists('target_points');
    }
}
