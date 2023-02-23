<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldGradesTableTarget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target', function (Blueprint $table) {
            $table->tinyInteger('school_type')->after('type')->nullable(); //Cấp học
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('target', 'grades')) {
            Schema::table('target', function (Blueprint $table) {
                $table->dropColumn('grades');
            });
        }
        
        Schema::table('target', function (Blueprint $table) {
            $table->dropColumn('school_type');
        });
    }
}
