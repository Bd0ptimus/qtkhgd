<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('to')->nullable();
            $table->string('from_email')->nullable()->comment('optional (used in sending client direct email)');
            $table->string('from_name')->nullable()->comment('optional (used in sending client direct email)');
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('type')->default('general')->comment('general|pdf (used for emails that need to generate a pdf)');
            $table->text('attachments')->nullable()->comment('json of request(attachments)');
            $table->string('resourcetype')->nullable()->comment('e.g. invoice. Used mainly for deleting records, when resource has been deleted');
            $table->bigInteger('resourceid')->nullable();
            $table->string('pdf_resource_type')->nullable()->comment('estimate|invoice');
            $table->bigInteger('pdf_resource_id')->nullable()->comment('resource id (e.g. estimate id)');
            $table->string('status')->default('new')->comment('new|processing (set to processing by the cronjob, to avoid duplicate processing)');
            $table->dateTime('started_at')->nullable()->comment('timestamp of when processing started');
            $table->softDeletes();
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
        Schema::dropIfExists('email_queues');
    }
}
