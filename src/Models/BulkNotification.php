<?php

namespace EscolaLms\BulkNotifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;


/**
 *
 * Class BulkNotification
 *
 * @package EscolaLms\BulkNotifications\Models
 *
 * @property int $id
 * @property string $title
 * @property string $channel
 * @property Carbon $created_at
 * @property Carbon $update_at
 *
 * @property Collection|BulkNotificationSection[] $sections
 * @property Collection|User[] $users
 *
 */
class BulkNotification extends Model
{

    protected $guarded = ['id'];

    public function sections(): HasMany
    {
        return $this->hasMany(BulkNotificationSection::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(BulkNotificationUser::class);
    }
}

