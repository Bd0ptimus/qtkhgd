<?php

namespace App\Library\Helpers;

use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Firebase
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Get cloud message instance
     *
     * @return CloudMessage
     */
    public function cloudMessage()
    {
        $androidConfig = AndroidConfig::fromArray([
            'priority' => 'high',
        ]);

        $iosConfig = ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'content-available' => 1,
                ],
            ],
        ]);

        return CloudMessage::new()
            ->withApnsConfig($iosConfig)
            ->withAndroidConfig($androidConfig)
            ->withDefaultSounds();
    }

    /**
     * @param array $data
     * @return Notification
     */
    public function createNotification(array $data)
    {
        return Notification::fromArray($data);
    }

    /**
     * @return Messaging
     */
    public function getMessaging()
    {
        return $this->messaging;
    }
}