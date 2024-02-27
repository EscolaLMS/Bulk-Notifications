<?php

namespace EscolaLms\BulkNotifications\Tests\Feature;

use EscolaLms\BulkNotifications\Channels\PushNotificationChannel;
use EscolaLms\BulkNotifications\Database\Seeders\BulkNotificationPermissionSeeder;
use EscolaLms\BulkNotifications\Dtos\SendBulkNotificationDto;
use EscolaLms\BulkNotifications\Events\NotificationSent;
use EscolaLms\BulkNotifications\Exceptions\UnsupportedNotification;
use EscolaLms\BulkNotifications\Services\Contracts\BulkNotificationServiceContract;
use EscolaLms\BulkNotifications\Tests\BulkNotificationTesting;
use EscolaLms\BulkNotifications\Tests\FakeNotificationChannel;
use EscolaLms\BulkNotifications\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;

class BulkNotificationServiceTest extends TestCase
{
    use BulkNotificationTesting;

    private BulkNotificationServiceContract $bulkNotificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BulkNotificationPermissionSeeder::class);

        $this->bulkNotificationService = app(BulkNotificationServiceContract::class);

        Event::fake([NotificationSent::class]);
    }

    /**
     * @dataProvider channelDataProvider
     */
    public function testSendNotificationViaChannel(string $channel): void
    {
        $dto = $this->makeSendBulkNotificationDto($channel);
        $amount = $dto->getUserIds()->count();

        $this->mock($channel, fn (MockInterface $mock) => $mock->shouldReceive('send')->times($amount));

        $bulkNotification = $this->bulkNotificationService->send($dto);

        $this->assertBulkNotification($bulkNotification->getKey(), $channel);
        $this->assertBulkNotificationHasSections($dto->getSections()->toArray());
        $this->assertBulkNotificationHasUsers($bulkNotification->getKey(), $dto->getUserIds()->toArray());

        Event::assertDispatchedTimes(NotificationSent::class, $amount);
        Event::assertDispatched(NotificationSent::class, function (NotificationSent $event) use ($bulkNotification, $dto) {
            return $dto->getUserIds()->contains($event->getUser()->getKey()) && $event->getNotification()->getKey() === $bulkNotification->getKey();
        });
    }

    /**
     * @dataProvider channelDataProvider
     */
    public function testSendNotificationViaChannelFilterSections(string $channel): void
    {
        $dto = $this->makeSendBulkNotificationDto($channel, ['invalid_section_key_1' => 'value', 'invalid_section_key_2' => 'value']);
        $amount = $dto->getUserIds()->count();

        $this->mock($channel, fn (MockInterface $mock) => $mock->shouldReceive('send')->times($amount));

        $bulkNotification = $this->bulkNotificationService->send($dto);

        $this->assertBulkNotification($bulkNotification->getKey(), $channel);
        $this->assertBulkNotificationHasSections($dto->getSections()->except('invalid_section_key_1', 'invalid_section_key_2')->toArray());
        $this->assertBulkNotificationHasUsers($bulkNotification->getKey(), $dto->getUserIds()->toArray());

        $this->assertDatabaseMissing($this->bulkNotificationSectionTable(), [
            'bulk_notification_id' => $bulkNotification->getKey(),
            'key' => 'invalid_section_key_1',
            'value' => 'value'
        ]);
        $this->assertDatabaseMissing($this->bulkNotificationSectionTable(), [
            'bulk_notification_id' => $bulkNotification->getKey(),
            'key' => 'invalid_section_key_2',
            'value' => 'value'
        ]);

        Event::assertDispatchedTimes(NotificationSent::class, $amount);
    }
    public function testSendNotificationUnsupportedChannel(): void
    {
        $fakeChannelClass = FakeNotificationChannel::class;
        $dto = new SendBulkNotificationDto($fakeChannelClass, [], []);

        $this->expectException(UnsupportedNotification::class);

        $this->bulkNotificationService->send($dto);

        Event::assertNotDispatched(NotificationSent::class);
    }

    public function channelDataProvider(): array
    {
        return [
            ['channel' => PushNotificationChannel::class]
        ];
    }
}
