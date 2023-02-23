<?php

namespace App\Console;

use App\Console\Commands\SendAdminNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //SendAdminNotification::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$this->checkAdminNotification($schedule);
        //$this->checkCovidNotification($schedule);

        //send [regular] queued emails
        $schedule->call(new \App\Cronjobs\EmailCron)->everyMinute();

        //send [direct] queued emails
        $schedule->call(new \App\Cronjobs\DirectEmailCron)->everyMinute();

        //Overdue task
        $schedule->call(new \App\Cronjobs\TaskOverdueCron)->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    public function checkAdminNotification(Schedule $schedule) {
        $schedule->command('notification:check')->everyMinute()
            ->before(function () {
                echo "START 1";
            })
            ->after(function () {
                echo "DONE 1";
            })
            ->onSuccess(function () {
                echo "SUCCESS 1";
            })
            ->onFailure(function () {
                echo "FAIL 1";
            });
    }
}
