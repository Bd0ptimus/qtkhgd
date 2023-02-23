<?php

/** ---------------------------------------------------------------------------------------------------
 * Email Cron
 * Send emails that are stored in the email queue (database)
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 *      - the scheduler is set to run this every minuted
 *      - the schedler itself is evoked by the signle cronjob set in cpanel (which runs every minute)
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use App\Mail\SendQueued;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailQueue;
use App\Models\EmailLog;

class EmailCron {

    public function __invoke() {

        //log that its run
        Log::info("Cronjob has started", [
            'process' => '[cronjob][email-processing]',
            config('app.debug_ref'),
            'function' => __function__,
            'file' => basename(__FILE__),
            'line' => __line__,
            'path' => __file__
        ]);
        /**
         * Send emails
         *   These emails are being sent every minute. You can set a higher or lower sending limit.
         */
        $limit = 20;
        if ($emails = EmailQueue::Where('type', 'general')->where('status', 'new')->take($limit)->get()) {

            //log that its run
            Log::info("some emails were found", [
                'process' => '[cronjob][email-processing]',
                config('app.debug_ref'),
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'payload' => $emails
            ]);

            //mark all emails in the batch as processing - to avoid batch duplicates/collisions
            foreach ($emails as $email) {
                $email->update([
                    'status' => 'processing',
                    'started_at' => now(),
                ]);
            }

            //now process
            foreach ($emails as $email) {
                //send the email (only to a valid email address)
                if ($email->to != '') {
                    Mail::to($email->to)->send(new SendQueued($email));
                    //log email
                    $log = new EmailLog();
                    $log->email = $email->to;
                    $log->subject = $email->subject;
                    $log->body = $email->message;
                    $log->save();
                }
                //delete email from the queue
                EmailQueue::Where('id', $email->id)->delete();
            }
        }
    }
}