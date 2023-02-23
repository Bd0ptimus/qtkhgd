<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTarget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title'); //Nội dung
            $table->tinyInteger('type'); //Chỉ tiêu có phân loại: chỉ tiêu giảng dạy, đào tạo, cở sở vật chất, chuyển đổi số ....
            $table->text('description')->nullable();   // Nội dung chỉ tiêu
            $table->text('solution')->nullable();  // Giải pháp
            $table->integer('result')->default(0);  // Kết quả
            $table->bigInteger('school_id')->nullable();
            $table->bigInteger('staff_id')->nullable();
            $table->bigInteger('main_target')->nullable();    //Target tổng

            $table->bigInteger('created_by');

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
        Schema::dropIfExists('target');
    }
}
