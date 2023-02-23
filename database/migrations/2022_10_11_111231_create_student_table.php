<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        try{ 
            Schema::create('student', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('student_code')->unique();
                $table->bigInteger('school_id'); // Trường
                $table->bigInteger('school_branch_id')->nullable(); // Điểm Trường
                $table->integer('grade'); // Khối
                $table->bigInteger('class_id'); // Lớp
                $table->string('fullname');
                $table->date('dob');
                $table->tinyInteger('gender');
                $table->integer('ethnic')->default(1); // Dân tộc
                $table->integer('religion')->nullable(); // Tôn giáo
                $table->integer('nationality')->default(1); //Quốc tịch
                $table->string('address')->nullable();
                $table->string('parent_id');  //ID của user phụ huynh
                $table->string('father_name')->nullable();
                $table->string('father_phone')->nullable();
                $table->string('father_email')->nullable();
                $table->string('mother_name')->nullable();
                $table->string('mother_phone')->nullable();
                $table->string('mother_email')->nullable();
                
                $table->integer('disabilities')->nullable(); // Dạng Khuyết tật 
                $table->integer('fptp')->nullable(); // Family in/under preferential treatment policy
                $table->integer('child_no')->nullable();
                $table->integer('total_childs')->nullable();
                $table->integer('health_history')->nullable(); // Lịch sử sức khoẻ
                $table->integer('born_history')->nullable(); // Lịch sử sinh sản
                $table->integer('disease_history')->nullable(); // Lịch sử bênh tật
                $table->tinyInteger('treating_disease')->default(0);

                $table->tinyInteger('tc_bcg')->default(0);
                $table->tinyInteger('tc_bhhguv_m1')->default(0);
                $table->tinyInteger('tc_bhhguv_m2')->default(0);
                $table->tinyInteger('tc_bhhguv_m3')->default(0);
                $table->tinyInteger('tc_bailiet_m1')->default(0);
                $table->tinyInteger('tc_bailiet_m2')->default(0);
                $table->tinyInteger('tc_bailiet_m3')->default(0);
                $table->tinyInteger('tc_viemganb_m1')->default(0);
                $table->tinyInteger('tc_viemganb_m2')->default(0);
                $table->tinyInteger('tc_viemganb_m3')->default(0);
                $table->tinyInteger('tc_soi')->default(0);
                $table->tinyInteger('tc_viemnaonb')->default(0);

                $table->timestamps();
            });
        } catch(Exception $e) {
            dd($e);
            DB::rollback();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student');
    }
}
