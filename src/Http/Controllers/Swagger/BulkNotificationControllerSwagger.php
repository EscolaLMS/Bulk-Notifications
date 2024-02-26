<?php

namespace EscolaLms\BulkNotifications\Http\Controllers\Swagger;

use EscolaLms\BulkNotifications\Http\Requests\SendBulkNotificationRequest;
use Illuminate\Http\JsonResponse;

interface BulkNotificationControllerSwagger
{

    /**
     * @OA\Post(
     *      path="/api/admin/notifications/send",
     *      summary="Store a newly notification",
     *      tags={"Admin Bulk Notifications"},
     *      description="Store Bulk Notification",
     *      security={
     *          {"passport": {}},
     *      },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/SendBulkNotificationRequest")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successfull operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="success",
     *                      type="boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/BulkNotificationResource"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string"
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function send(SendBulkNotificationRequest $request): JsonResponse;
}
