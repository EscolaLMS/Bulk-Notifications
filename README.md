# Bulk-Notifications

[![swagger](https://img.shields.io/badge/documentation-swagger-green)](https://escolalms.github.io/Bulk-Notifications/)
[![codecov](https://codecov.io/gh/EscolaLMS/Bulk-Notifications/branch/main/graph/badge.svg?token=NRAN4R8AGZ)](https://codecov.io/gh/EscolaLMS/Bulk-Notifications)
[![phpunit](https://github.com/EscolaLMS/Bulk-Notifications/actions/workflows/test.yml/badge.svg)](https://github.com/EscolaLMS/Bulk-Notifications/actions/workflows/test.yml)
[![downloads](https://img.shields.io/packagist/dt/escolalms/Bulk-Notifications)](https://packagist.org/packages/escolalms/Bulk-Notifications)
[![downloads](https://img.shields.io/packagist/v/escolalms/Bulk-Notifications)](https://packagist.org/packages/escolalms/Bulk-Notifications)
[![downloads](https://img.shields.io/packagist/l/escolalms/Bulk-Notifications)](https://packagist.org/packages/escolalms/Bulk-Notifications)


## What does it do
This package is used to send bulk notifications through various channels. The available channels are PushNotification.

#### PushNotifications
Sending push notifications is through FCM (Firebase Cloud Messaging).
Push messages can be sent to a list of users or to all users of the system. Messages are sent to registered FCM tokens.

## Installing
- `composer require escolalms/bulk-notifications`
- `php artisan migrate`
- `php artisan db:seed --class="EscolaLms\BulkNotifications\Database\Seeders\BulkNotificationPermissionSeeder"`

## Configuration
The config.php configuration file is divided into channels.

For the push channel, you can configure:
- `service_account` - FCM access key 
- `base_redirect_url` - base url set in notifications for the redirect_url field

```php
[
    'push' => [
        'service_account' => [],
        'base_redirect_url' => null
    ]
];
```


## Endpoints
All the endpoints are defined in swagger
[![swagger](https://img.shields.io/badge/documentation-swagger-green)](https://escolalms.github.io/Bulk-Notifications/)

Test details
[![codecov](https://codecov.io/gh/EscolaLMS/Bulk-Notifications/branch/main/graph/badge.svg?token=O91FHNKI6R)](https://codecov.io/gh/EscolaLMS/Bulk-Notifications)
![Tests PHPUnit in environments](https://github.com/EscolaLMS/Bulk-Notifications/actions/workflows/test.yml/badge.svg)


## Events
- `NotificationSent` - The notification has been created. 

The event is listened to by the [escolalms/notifications](https://github.com/EscolaLMS/Notifications) package.

## Permissions
Permissions are defined in [seeder](database/seeders/BulkNotificationPermissionSeeder.php)
