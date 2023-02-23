<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableStaffLinkingSchollAddColumnWorkingSlotsField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_linking_school', function (Blueprint $table) {
            $table->string('working_slots')->after('working_days')->nullable(); //json encode array mon, tue, wed ....
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_linking_school', function (Blueprint $table) {
            $table->dropColumn(['working_slots']);
        });
    }
}
