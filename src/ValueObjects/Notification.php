<?php

namespace EscolaLms\BulkNotifications\ValueObjects;

use EscolaLms\BulkNotifications\Models\BulkNotificationUser;
use Illuminate\Support\Collection;

abstract class Notification
{

    public function __construct(protected BulkNotificationUser $bulkNotificationUser)
    {
    }

    public function getBulkNotificationUser(): BulkNotificationUser
    {
        return $this->bulkNotificationUser;
    }

    abstract public static function of(BulkNotificationUser $bulkNotificationUser, Collection $sections, string $destination): Notification;

    abstract public function toPayload();
}
