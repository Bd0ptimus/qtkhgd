<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassidSubjectid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target', function (Blueprint $table) {
            $table->integer('class_id')->after('main_target')->nullable(); 
            $table->integer('subject_id')->after('main_target')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    //
    public function down()
    {
        Schema::table('target', function (Blueprint $table) {
            $table->dropColumn(['class_id', 'subject_id']);
        });

    }
}
