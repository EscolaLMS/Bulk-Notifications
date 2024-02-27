<?php

namespace EscolaLms\BulkNotifications\Tests;

use EscolaLms\BulkNotifications\Channels\PushNotificationChannel;
use EscolaLms\BulkNotifications\Dtos\SendBulkNotificationDto;
use EscolaLms\BulkNotifications\Models\BulkNotification;
use EscolaLms\BulkNotifications\Models\BulkNotificationSection;
use EscolaLms\BulkNotifications\Models\DeviceToken;
use Illuminate\Foundation\Testing\WithFaker;

trait BulkNotificationTesting
{
    use WithFaker;

    public function bulkNotificationTable(): string
    {
        return with(new BulkNotification())->getTable();
    }

    public function bulkNotificationSectionTable(): string
    {
        return with(new BulkNotificationSection())->getTable();
    }

    public function bulkNotificationUserTable(): string
    {
        return 'bulk_notification_user';
    }

    public function makeBulkNotificationPayload(?string $channel, ?array $data = []): array
    {
        if (!$channel) {
            return [
                'channel' => $channel
            ];
        }

        $payload = [
            'channel' => $channel,
            'sections' => [
                ...$channel::sections()
                    ->mapWithKeys(fn(string $section) => [$section => $this->faker->words(5, true)])
            ],
            'user_ids' => DeviceToken::factory()
                ->count(rand(1, 10))
                ->create()
                ->pluck('user_id')
                ->toArray()
        ];

        return array_merge($payload, $data);
    }

    public function makeSendBulkNotificationDto(string $channel, ?array $sections = []): SendBulkNotificationDto
    {
        return new SendBulkNotificationDto(
            $channel,
            $channel::sections()
                ->mapWithKeys(fn(string $section) => [$section => 'value'])
                ->merge($sections)
                ->toArray(),
            DeviceToken::factory()
                ->count(rand(1, 10))
                ->create()
                ->pluck('user_id')
                ->toArray()
        );
    }

    public function assertBulkNotification(int $bulkNotificationId, string $channel): void
    {
        $this->assertDatabaseHas($this->bulkNotificationTable(), [
            'id' => $bulkNotificationId,
            'channel' => $channel,
        ]);
    }

    public function assertBulkNotificationHasSections(array $data): void
    {
        $sections = collect($data)->map(fn($value, $key) => [
            'key' => $key,
            'value' => $value
        ]);

        $sections->each(fn(array $section) => $this->assertDatabaseHas($this->bulkNotificationSectionTable(), $section));

        $this->assertDatabaseCount($this->bulkNotificationSectionTable(), $sections->count());
    }

    public function assertBulkNotificationHasUsers(int $bulkNotificationId, array $data): void
    {
        $users = collect($data)->map(fn($value) => [
            'bulk_notification_id' => $bulkNotificationId,
            'user_id' => $value
        ]);

        $users->each(fn(array $user) => $this->assertDatabaseHas($this->bulkNotificationUserTable(), $user));

        $this->assertDatabaseCount($this->bulkNotificationUserTable(), $users->count());
    }
}
