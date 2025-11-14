<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisorNote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'advisor_id',
        'meeting_prep_id',
        'notes',
        'follow_up_required',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'advisor_id' => 'integer',
            'meeting_prep_id' => 'integer',
            'follow_up_required' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Advisor::class);
    }

    public function meetingPrep(): BelongsTo
    {
        return $this->belongsTo(MeetingPrep::class);
    }
}
