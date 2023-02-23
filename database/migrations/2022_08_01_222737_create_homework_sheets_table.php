<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeworkSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homework_sheets', function (Blueprint $table) {
            //
            $table->bigIncrements('id')->autoIncrement();
            $table->string('name');
            $table->tinyInteger('grade');
            $table->bigInteger('subject_id');
            $table->longText('content');
            $table->string('assemblage')->nullable();
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
    
        Schema::dropIfExists('homework_sheets');
    }
}
