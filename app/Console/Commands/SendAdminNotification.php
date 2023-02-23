<?php

namespace App\Console\Commands;

use App\Library\Helpers\Firebase;
use App\Models\NotificationAdmin;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SendAdminNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check to send notification for managers';

    /**
     * Execute the console command.
     *
     * @param Firebase $firebase
     * @throws \Kreait\Firebase\Exception\FirebaseException
     * @throws \Kreait\Firebase\Exception\MessagingException
     */
    public function handle(Firebase $firebase)
    {
        $this->info("SendAdminNotification start");
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            /*$notificationsData = $this->getAbnormalNotifications($now);*/

            // get notification task
            $notificationsTask = $this->getTaskNotifications($now);

            $adminNotifications = $notificationsTask->get('admin_notifications');

            $adminNotifications = $adminNotifications->unique(function ($value) {
                return $value['user_id'] . $value['data'] . $value['type'];
            })->values();

            // Notification for admin
            foreach ($adminNotifications->chunk(1000) as $adminNotificationsChunk) {
                NotificationAdmin::insert($adminNotificationsChunk->toArray());
            }


            $this->info("SendAdminNotification success");
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            $this->error("SendAdminNotification error");
        }
    }

    /**
     * Get task notifications
     *
     * @param $now
     * @return Collection
     */
    private function getTaskNotifications($now): Collection
    {
        $adminNotifications = collect();
        // get task today
        $taskDays = Task::with([
            'assigned',
            'creator',
        ])->whereDate('created_at', $now)
            ->orderBy('created_at')
            ->groupBy('created_at')
            ->get();
        $title = "Nhiệm vụ";

        foreach ($taskDays as $task) {
            $content = "[Nhiệm vụ] - [".PRIORITY_VALUE[$task->priority]."] - [ " . $task->getOriginal('start_date') . "]" . ' - ' . $task->title;
            // each user assigned
            foreach ($task->assigned as $user) {
                //check user config web_notification
                if ($user && $user->web_notification) {
                    $existsData = NotificationAdmin::where('type', NotificationAdmin::TYPE["nhiem_vu"])
                        ->whereJsonContains('data->task_id', intval($task->id))
                        ->whereJsonContains('data->creator_id', intval($user->id))
                        ->whereJsonContains('data->priority', $task->priority)
                        ->first();

                    if (empty($existsData)) {
                        $adminNotifications->push([
                            'user_id' => $user->id,
                            'title' => $title,
                            'content' => $content,
                            'type' => NotificationAdmin::TYPE["nhiem_vu"],
                            'data' => json_encode([
                                'task_id' => $task->id,
                                'task_title' => $task->title,
                                'creator' => $task->creator->name,
                                'creator_id' => $task->creator_id,
                                'status' => $task->status,
                                'priority' => $task->priority,
                                'start_date' => $task->getOriginal('start_date'),
                                'due_date' => $task->getOriginal('due_date')
                            ]),
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }
                }
            }
        }

        return collect([
            'admin_notifications' => $adminNotifications,
        ]);
    }
}
