<?php

namespace App\Models;

use App\Enums\Travel\Status;
use App\Events\Travel\Creating;
use App\Events\Travel\Updated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'status' => Status::class,
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'creating' => Creating::class,
        'updated' => Updated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canCancel(): bool
    {
        return ($this->status->value === Status::REQUESTED->value && $this->created_at >= now()->subDays(7))
            || ($this->status->value === Status::APPROVED->value && $this->departure_date >= now()->addDays(3));
    }
}
