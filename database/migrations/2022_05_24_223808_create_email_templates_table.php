<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('lang')->nullable()->comment('to match to language');
            $table->string('type')->nullable()->comment('everyone|admin|client');
            $table->string('category')->nullable()->comment('users|projects|tasks|leads|tickets|estimates|other');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->text('variables')->nullable();
            $table->string('status')->default('enabled')->comment('enabled|disabled');
            $table->string('language')->nullable();
            $table->string('real_template')->default('yes')->comment('yes|no');
            $table->string('show_enabled')->default('yes')->comment('yes|no');
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
        Schema::dropIfExists('email_templates');
    }
}
