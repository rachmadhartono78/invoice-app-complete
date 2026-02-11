<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserIdentifier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'value',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns this identifier
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get identifiers by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get verified identifiers
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Check if identifier is verified
     */
    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }

    /**
     * Mark identifier as verified
     */
    public function markAsVerified(): bool
    {
        return $this->update(['verified_at' => now()]);
    }
}
