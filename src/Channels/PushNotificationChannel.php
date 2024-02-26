<?php

namespace EscolaLms\BulkNotifications\Channels;

use EscolaLms\BulkNotifications\Exceptions\UnsupportedNotification;
use EscolaLms\BulkNotifications\ValueObjects\Notification;
use EscolaLms\BulkNotifications\ValueObjects\PushNotification;
use Illuminate\Support\Collection;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;

class PushNotificationChannel implements NotificationChannel
{
    public function __construct(
        private readonly Messaging $messaging
    )
    {
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     * @throws UnsupportedNotification
     */
    public function send(Notification $notification): void
    {
        $message = $this->makeCloudMessage($notification);
        $this->messaging->send($message);
    }

    /**
     * @throws UnsupportedNotification
     */
    private function makeCloudMessage(Notification $notification): CloudMessage
    {
        if (!$notification instanceof PushNotification) {
            throw new UnsupportedNotification();
        }

        return CloudMessage::fromArray($notification->toPayload());
    }

    public static function sections(): Collection
    {
        return collect([
            'title',
            'body',
            'image_url',
            'redirect_url',
            'data',
        ]);
    }

    public static function requiredSections(): Collection
    {
        return collect([
            'title',
            'body',
        ]);
    }
}
