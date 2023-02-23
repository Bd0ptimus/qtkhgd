<?php

use App\Models\School;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /* Danh sách môn học  */
        Schema::create('subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->bigInteger('school_id')->nullable(); // subject for special school
            $table->softDeletes();
            $table->timestamps();
        });

        /* Phân bổ môn học theo khối */
        Schema::create('grade_subject', function (Blueprint $table) {
            $table->bigInteger('grade');
            $table->bigInteger('subject_id');
        });

        /* DS tổ chuyên môn */
        Schema::create('regular_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->tinyInteger('school_level');
            $table->bigInteger('school_id')->nullable(); // group for special school
            $table->softDeletes();
            $table->timestamps();
        });

        /* Phân bổ lớp học theo tổ chuyên môn */
        Schema::create('regular_group_subject', function (Blueprint $table) {
            $table->bigInteger('regular_group_id');
            $table->bigInteger('subject_id');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject');
        Schema::dropIfExists('grade_subject');
        Schema::dropIfExists('regular_group');
        Schema::dropIfExists('regular_group_subject');
    }
}
