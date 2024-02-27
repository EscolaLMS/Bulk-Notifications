<?php

namespace EscolaLms\BulkNotifications\ValueObjects;

use EscolaLms\BulkNotifications\Models\BulkNotificationSection;
use EscolaLms\BulkNotifications\Models\BulkNotificationUser;
use Illuminate\Support\Collection;

class PushNotification extends Notification
{
    public function __construct(
        protected BulkNotificationUser $bulkNotificationUser,
        protected string $deviceToken,
        protected string $title,
        protected string $body,
        protected ?string $imageUrl = null,
        protected ?string $redirectUrl = null,
        protected ?array $data = []
    )
    {
        parent::__construct($bulkNotificationUser);
    }

    public static function of(BulkNotificationUser $bulkNotificationUser, Collection $sections, string $destination): PushNotification
    {
        $sections = $sections->mapWithKeys(fn(BulkNotificationSection $section) => [
            $section->key => $section->value
        ]);

        $title = $sections->get('title');
        $body = $sections->get('body');
        $imageUrl = $sections->get('image_url') ?? null;
        $redirectUrl = $sections->get('redirect_url') ?? null;
        $data = json_decode($sections->get('data'), true) ?? [];

        return new self(
            $bulkNotificationUser,
            $destination,
            $title,
            $body,
            $imageUrl,
            $redirectUrl,
            $data,
        );
    }

    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function toPayload(): array
    {
        return [
            'token' => $this->getDeviceToken(),
            'notification' => [
                'title' => $this->getTitle(),
                'body' => $this->getBody(),
                'image' => $this->getImageUrl(),
            ],
            'data' => array_merge($this->getData(), ['redirect_url' => $this->getRedirectUrl()])
        ];
    }
}
