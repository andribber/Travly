<?php

namespace App\Models;

use App\Enums\Travel\Status;
use App\Events\Travel\Creating;
use App\Events\Travel\Updated;
use App\Listeners\Travel\FillStatus;
use App\Listeners\Travel\NotifyStatusChange;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'destiny',
        'departure_date',
        'return_date',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => Status::class,
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'creating' => Creating::class,
        'updated' => Updated::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canCancel(): bool
    {
        return $this->status === Status::REQUESTED && $this->created_at <= now()->subDays(7)
            || !$this->departure_date >= now()->subDays(3);
    }
}
