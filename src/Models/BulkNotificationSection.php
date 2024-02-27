<?php

namespace EscolaLms\BulkNotifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
 * Class BulkNotificationSection
 *
 * @package EscolaLms\BulkNotifications\Models
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $update_at
 *
 */
class BulkNotificationSection extends Model
{
    protected $guarded = ['id'];
}

