<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEbook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ebook', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description');
            $table->string('publisher'); // Nhà xuất bản
            $table->string('publishing_company')->nullable(); // Công ty phát hành
            $table->string('authors'); // Tác giả 
            $table->date('d_o_p'); // date of publishing
            $table->integer('n_o_p'); //number of publishing
            $table->integer('total_page');
            $table->string('assemblage')->nullable();
            $table->tinyInteger('cover_type')->default(1); //1. bìa cứng; 2. Bìa mềm.
            $table->string('size')->nullable(); // kích thước dài rộng cao
            $table->tinyInteger('grade')->nullable();
            $table->bigInteger('subject_id')->nullable();
            $table->bigInteger('total_views')->default(0);
            $table->bigInteger('total_downloads')->default(0);
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
        Schema::dropIfExists('ebook');
    }
}
