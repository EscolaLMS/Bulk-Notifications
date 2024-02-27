<?php

namespace EscolaLms\BulkNotifications\Services;

use EscolaLms\BulkNotifications\Channels\NotificationChannel;
use EscolaLms\BulkNotifications\Channels\PushNotificationChannel;
use EscolaLms\BulkNotifications\Dtos\OrderDto;
use EscolaLms\BulkNotifications\Dtos\PageDto;
use EscolaLms\BulkNotifications\Dtos\SendBulkNotificationDto;
use EscolaLms\BulkNotifications\Exceptions\UnsupportedNotification;
use EscolaLms\BulkNotifications\Jobs\SendNotification;
use EscolaLms\BulkNotifications\Models\BulkNotification;
use EscolaLms\BulkNotifications\Models\DeviceToken;
use EscolaLms\BulkNotifications\Models\User;
use EscolaLms\BulkNotifications\Repositories\Contracts\BulkNotificationRepositoryContract;
use EscolaLms\BulkNotifications\Repositories\Contracts\DeviceTokenRepositoryContract;
use EscolaLms\BulkNotifications\Services\Contracts\BulkNotificationServiceContract;
use EscolaLms\BulkNotifications\ValueObjects\PushNotification;
use EscolaLms\BulkNotifications\Dtos\CriteriaBulkNotificationDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BulkNotificationService implements BulkNotificationServiceContract
{

    public function __construct(
        private BulkNotificationRepositoryContract $bulkNotificationRepository,
        private DeviceTokenRepositoryContract $deviceTokenRepository
    )
    {
    }

    public function send(SendBulkNotificationDto $dto): BulkNotification
    {
        return DB::transaction(function () use ($dto) {
            /** @var NotificationChannel $channel */
            $channel = $dto->getChannel();

            $bulkNotification = $this->createBulkNotification($dto, $channel::sections());

            $this->process($bulkNotification);

            return $bulkNotification;
        });
    }

    public function list(CriteriaBulkNotificationDto $criteriaDto, PageDto $pageDto, OrderDto $orderDto): LengthAwarePaginator
    {
        return $this->bulkNotificationRepository->findAll(
            $criteriaDto->toArray(),
            $pageDto->getPerPage(),
            $orderDto->getOrderDirection(),
            $orderDto->getOrderBy()
        );
    }

    private function createBulkNotification(SendBulkNotificationDto $dto, Collection $channelSections): BulkNotification
    {
        /** @var BulkNotification $bulkNotification */
        $bulkNotification = $this->bulkNotificationRepository->create($dto->toArray());
        $filteredSections = $dto->getSections()->filter(fn($value, $key) => $channelSections->contains($key));

        foreach ($filteredSections as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $bulkNotification->sections()->create(['key' => $key, 'value' => $value]);
        }

        $bulkNotification->users()->attach($dto->getUserIds());

        return $bulkNotification;
    }

    /**
     * @throws UnsupportedNotification
     */
    private function process(BulkNotification $bulkNotification): void
    {
        match ($bulkNotification->channel) {
            PushNotificationChannel::class => $this->processPushNotifications($bulkNotification),
            default => throw new UnsupportedNotification()
        };
    }

    private function processPushNotifications(BulkNotification $bulkNotification): void
    {
        $sections = $bulkNotification->sections;
        $users = $bulkNotification->users;

        $this->deviceTokenRepository
            ->findUsersTokens($users->pluck('id'))
            ->each(function (DeviceToken $deviceToken) use ($bulkNotification, $users, $sections) {
                $notification = PushNotification::of(
                    $users->filter(fn(User $user) => $user->getKey() === $deviceToken->user->getKey())->first()?->pivot,
                    $sections,
                    $deviceToken->token
                );

                SendNotification::dispatch($notification, $bulkNotification->channel);
            });
    }
}
