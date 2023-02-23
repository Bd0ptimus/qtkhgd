<?php

namespace App\Modules\Api\Controllers;

use App\Models\Notification;
use App\Models\UserDevice;
use App\Modules\Api\Requests\Notification\TokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    /**
     * Get All notifications
     * @return JsonResponse
     */
    public function getNotifications()
    {
        $auth = auth()->user();
        $notifications = Notification::where('user_id', $auth->id)->orderBy('created_at', 'desc')->paginate(10);

        return $this->respSuccess($notifications);
    }

    /**
     * Set new token for user
     * @param TokenRequest $request
     * @return JsonResponse
     */
    public function setToken(TokenRequest $request)
    {
        $auth = auth()->user();
        $data = $request->only(['token', 'device_id']);
        UserDevice::updateOrCreate(
            ['device_id' => $data['device_id'], 'user_id' => $auth->id],
            ['fcm_token' => $data['token']]
        );

        return $this->respSuccess(null);
    }

    /**
     * Read notification
     * @param Request $request
     * @return JsonResponse
     */
    public function readNotification($id)
    {
        $auth = auth()->user();
        $notification = Notification::where(['user_id' => $auth->id, 'id' => $id])->first();
        if (is_null($notification)) {
            return $this->respError('Dont have data');
        }

        $notification->update(['read' => 1]);
        return $this->respSuccess($notification);
    }
}