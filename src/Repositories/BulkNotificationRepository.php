<?php

namespace EscolaLms\BulkNotifications\Repositories;

use EscolaLms\BulkNotifications\Models\BulkNotification;
use EscolaLms\BulkNotifications\Repositories\Contracts\BulkNotificationRepositoryContract;
use EscolaLms\Core\Repositories\BaseRepository;

class BulkNotificationRepository extends BaseRepository implements BulkNotificationRepositoryContract
{

    public function getFieldsSearchable(): array
    {
        return [];
    }

    public function model(): string
    {
        return BulkNotification::class;
    }
}
