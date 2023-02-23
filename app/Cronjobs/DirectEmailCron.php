<?php

/** ------------------------------------------------------------------------------------------------------------------
 * Email Cron
 * Send emails that were composed in the CRM (e.g. client email)  emails that are stored in the email queue (database)
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 *      - the scheduler is set to run this every minuted
 *      - the schedler itself is evoked by the signle cronjob set in cpanel (which runs every minute)
 * @package    Grow CRM
 * @author     NextLoop
 *-------------------------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use App\Mail\DirectCRMEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailQueue;
use App\Models\EmailLog;

class DirectEmailCron {

    public function __invoke() {

        /**
         * Send emails
         *   These emails are being sent every minute. You can set a higher or lower sending limit.
         *   Just note that if there are attachments, a high limit can cause server timeouts
         */
        $limit = 5;
        if ($emails = EmailQueue::Where('type', 'direct')->where('status', 'new')->take($limit)->get()) {

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

                    /** ----------------------------------------------
                     * send email to client
                     * ----------------------------------------------*/
                    $data = [
                        'to' => $email->to,
                        'subject' => $email->subject,
                        'body' => $email->message,
                        'from_email' => $email->from_email,
                        'from_name' => $email->from_name,
                    ];

                    //add any attachments
                    if ($email->attachments != '') {
                        $attachments = json_decode($email->attachments, true);
                        if (is_array($attachments)) {
                            $data['attachments'] = $attachments;
                        }
                    }
                    Mail::to($email->to)->send(new DirectCRMEmail($data));

                    //log that we sent email
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