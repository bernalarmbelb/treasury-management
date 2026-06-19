<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an activity entry, defaulting to the currently logged-in user.
     */
    public static function record(string $action, ?User $user = null): self
    {
        return self::create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
        ]);
    }
}
