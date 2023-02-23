<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('tasks', 'overdue_notification_sent')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('overdue_notification_sent')->default('no')->comment('yes|no');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('tasks', 'overdue_notification_sent')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('overdue_notification_sent');
            });
        }
    }
}
