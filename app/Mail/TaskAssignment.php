<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [assign task] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\EmailTemplate;
use App\Models\EmailQueue;
use App\Models\Task;
use App\Admin\Models\AdminUser as User;

class TaskAssignment extends Mailable
{
    use Queueable;

    /**
     * The data for merging into the email
     */
    public $data;

    /**
     * Model instance
     */
    public $obj;

    /**
     * Model instance
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user = [], $data = [], $obj = [])
    {
        $this->data = $data;
        $this->user = $user;
        $this->obj = $obj;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //email template
        $template = EmailTemplate::Where('name', 'Task Assignment')->first();
        if (!$template) return false;

        //validate
        if (!$this->obj instanceof Task || !$this->user instanceof User) return false;

        //only active templates
        if ($template->status != 'enabled') return false;

        //set template variables
        $payload = [
            'name' => $this->user->name,
            'assigned_by_name' =>$this->obj->creator->name,
            'id' => $this->obj->id,
            'title' => $this->obj->title,
            'created_at' => runtimeDate($this->obj->created_at),
            'start_date' => runtimeDate($this->obj->start_date),
            'description' => $this->obj->description,
            'due_date' => runtimeDate($this->obj->due_date),
            'status' => ($this->obj->currentStatus->title),
            'url' => url('portal/common/tasks/' . $this->obj->id),
        ];

        //save in the database queue
        $queue = new EmailQueue();
        $queue->to = $this->user->email;
        $queue->subject = $template->parse('subject', $payload);
        $queue->message = $template->parse('body', $payload);
        $queue->save();
    }
}
