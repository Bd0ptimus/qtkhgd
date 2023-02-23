<?php

namespace App\Http\Livewire\Components\Notify;

use Livewire\Component;
use App\Admin\Admin;
use App\Models\NotificationAdmin;
use App\Admin\Services\AdminNotiService;

class ItemNavbar extends Component
{
    public function mount()
    {
        //
    }

    public function handleReadNotification($notification, AdminNotiService $adminNotiService)
    {
        if ($update = $adminNotiService->update($notification['id'], ['read' => 1])) {
            $data = json_decode($notification['data'], true);

            if($notification['type'] == NotificationAdmin::TYPE['nhiem_vu']) {
                return redirect()->route('tasks.show', ['id' => $data['task_id']]);
            }

            if($notification['type'] == NotificationAdmin::TYPE['teacher_plan']) {
                //return redirect()->route('tasks.show', ['id' => $data['task_id']]);
            }
        }
    }

    public function render()
    {
        return view('livewire.components.notify.item-navbar', [
            "adminNotifications" => Admin::user()->adminNotifications
        ]);
    }
}
