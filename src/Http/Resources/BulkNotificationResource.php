<?php

namespace EscolaLms\BulkNotifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *      schema="BulkNotificationResource",
 *      required={"id", "channel", "sections"},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="channel",
 *          description="channel",
 *          type="string"
 *      ),
 *      @OA\Property(
 *           property="sections",
 *           type="array",
 *           @OA\Items(ref="#/components/schemas/BulkNotificationSectionResource")
 *       ),
 * )
 *
 */
class BulkNotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'channel' => $this->resource->channel,
            'sections' => BulkNotificationSectionResource::collection($this->resource->sections)
        ];
    }
}
