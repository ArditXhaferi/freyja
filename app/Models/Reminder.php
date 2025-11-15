<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'advisor_id',
        'meeting_id',
        'reminder_type',
        'message',
        'remind_at',
        'reminder_active',
        'delivered_at',
        'seen_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'advisor_id' => 'integer',
            'meeting_id' => 'integer',
            'remind_at' => 'datetime',
            'reminder_active' => 'boolean',
            'delivered_at' => 'datetime',
            'seen_at' => 'datetime',
        ];
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}


