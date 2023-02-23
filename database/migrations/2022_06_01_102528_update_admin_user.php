<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_user', function (Blueprint $table) {
            $table->string('email')->after('phone_number')->nullable();
            $table->tinyInteger('email_notification')->after('created_by')->default(1);
            $table->tinyInteger('web_notification')->after('created_by')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_user', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('email_notification');
            $table->dropColumn('web_notification');
        });
    }
}
