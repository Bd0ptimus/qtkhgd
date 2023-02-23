<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
            $table->bigInteger('creator_id')->comment('user create task');
            $table->bigInteger('position')->nullable();
            $table->string('priority')->default('normal')->comment('normal | high | urgent');
            $table->tinyInteger('status')->default(1);
            $table->string('active_state')->default('active')->comment('active|archived');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('visibility')->default('visible');
            $table->string('object_type')->nullable();
            $table->tinyInteger('object_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('tasks');
    }
}
