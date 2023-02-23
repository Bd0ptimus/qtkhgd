<?php

/** --------------------------------------------------------------------------------
 * SendQueued
 * Send emails that are stored in the email queue (database)
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendQueued extends Mailable {
    use Queueable, SerializesModels;

    public $data;

    public $attachment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $attachment = '') {
        //
        $this->data = $data;
        $this->attachment = $attachment;
    }

    /**
     * Nextloop: This will send the email that has been saved in the database (as sent by the cronjob)
     *
     * @return $this
     */
    public function build() {
        //validate
        if (!$this->data instanceof \App\Models\EmailQueue) return;

        //[attachement] send email with an attahments
        if (is_array($this->attachment)) {
            return $this->from(config('constants.email_system'))
                ->subject($this->data->subject)
                ->with([
                    'content' => $this->data->message,
                ])
                ->view('templates.email')
                ->attach($this->attachment['filepath'], [
                    'as' => $this->attachment['filename'],
                    'mime' => 'application/pdf',
                ]);
        } else {
            //[no attachment] send email without any attahments
            return $this->from(config('constants.email_system'))
                ->subject($this->data->subject)
                ->with([
                    'content' => $this->data->message,
                ])
                ->view('templates.email');
        }
    }
}
