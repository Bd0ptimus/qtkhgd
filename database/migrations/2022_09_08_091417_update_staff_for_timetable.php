<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStaffForTimetable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_staff', function (Blueprint $table) {
            $table->tinyInteger('has_baby')->after('status')->default(0);
            $table->tinyInteger('has_pregnant')->after('status')->default(0);
            $table->tinyInteger('is_linking_staff')->after('status')->nullable(0);
        });

        Schema::create('staff_linking_school', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('staff_id');
            $table->bigInteger('primary_school_id');
            $table->bigInteger('additional_school_id');
            $table->string('working_days'); //json encode array mon, tue, wed ....
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
        Schema::table('school_staff', function (Blueprint $table) {
            $table->dropColumn(['has_baby', 'has_pregnant', 'is_linking_staff']);
        });

        Schema::dropIfExists('staff_linking_school');
    }
}
