<?php

namespace EscolaLms\BulkNotifications\Services\Contracts;

use EscolaLms\BulkNotifications\Dtos\SendBulkNotificationDto;
use EscolaLms\BulkNotifications\Models\BulkNotification;

interface BulkNotificationServiceContract
{
    public function send(SendBulkNotificationDto $dto): BulkNotification;
}
