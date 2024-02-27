<?php

namespace EscolaLms\BulkNotifications\Services\Contracts;

use EscolaLms\BulkNotifications\Dtos\OrderDto;
use EscolaLms\BulkNotifications\Dtos\PageDto;
use EscolaLms\BulkNotifications\Dtos\SendBulkNotificationDto;
use EscolaLms\BulkNotifications\Models\BulkNotification;
use EscolaLms\BulkNotifications\Dtos\CriteriaBulkNotificationDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BulkNotificationServiceContract
{
    public function send(SendBulkNotificationDto $dto): BulkNotification;

    public function list(CriteriaBulkNotificationDto $criteriaDto, PageDto $pageDto, OrderDto $orderDto): LengthAwarePaginator;
}
