<?php

namespace App\Models;

use App\Enums\Travel\Status;
use App\Listeners\Travel\NotifyStatusChange;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Travel extends Model
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
        'departure_date' => 'timestamp',
        'return_date' => 'timestamp',
    ];

    protected $dispatchesEvents = [
        'updated' => NotifyStatusChange::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
