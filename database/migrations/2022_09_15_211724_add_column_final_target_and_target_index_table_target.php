<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFinalTargetAndTargetIndexTableTarget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target', function (Blueprint $table) {
            $table->integer('final_target')->after('solution');
            $table->integer('target_index')->nullable()->after('final_target');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('target', function (Blueprint $table) {
            $table->dropColumn('final_target');
            $table->dropColumn('target_index');
        });
    }
}
