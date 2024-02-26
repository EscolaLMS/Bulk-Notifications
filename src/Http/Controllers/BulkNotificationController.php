<?php

namespace EscolaLms\BulkNotifications\Http\Controllers;

use EscolaLms\BulkNotifications\Http\Controllers\Swagger\BulkNotificationControllerSwagger;
use EscolaLms\BulkNotifications\Http\Requests\SendBulkNotificationRequest;
use EscolaLms\BulkNotifications\Http\Resources\BulkNotificationResource;
use EscolaLms\BulkNotifications\Services\Contracts\BulkNotificationServiceContract;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use Illuminate\Http\JsonResponse;

class BulkNotificationController extends EscolaLmsBaseController implements BulkNotificationControllerSwagger
{

    public function __construct(private readonly BulkNotificationServiceContract $bulkNotificationService)
    {
    }

    public function send(SendBulkNotificationRequest $request): JsonResponse
    {
        $bulkNotification = $this->bulkNotificationService->send($request->toDto());

        return $this->sendResponseForResource(
            BulkNotificationResource::make($bulkNotification),
            'Notification sent successfully.'
        );
    }
}