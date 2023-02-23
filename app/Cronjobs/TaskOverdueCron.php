<?php

/** -------------------------------------------------------------------------------------------------
 * TEMPLATE
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;

use Illuminate\Support\Facades\Log;
use App\Models\Task;

class TaskOverdueCron {

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

        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $nextDay = \Carbon\Carbon::now()->addDay()->format('Y-m-d');
        // hạn hoàn thành là ngày hôm nay, hoặc là ngày mai
        $tasks = Task::where(function($q) use($today, $nextDay) {
                return $q->where('due_date', '<', $today)
                    ->orWhere('due_date', $nextDay);
            })
            ->where('overdue_notification_sent', 'no')
            ->whereIn('status', [1, 2, 3]) //'new', 'in progress', 'testing'
            ->get();

        //process each task
        foreach ($tasks as $task) {
            //all signed users
            $assigned = $task->assigned;

            //queue email
            foreach ($assigned as $user) {
                $mail = new \App\Mail\OverdueTask($user, [], $task);
                $mail->build();
            }

            //update task
            $task->overdue_notification_sent = 'yes';
            $task->save();
        }
    }
}