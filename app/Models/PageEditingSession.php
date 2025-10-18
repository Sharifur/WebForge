<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * PageEditingSession Model
 *
 * Tracks active editing sessions for page builder to prevent conflicts
 * and enable real-time collaborative editing awareness.
 *
 * @property int $id
 * @property int $page_id
 * @property int $admin_id
 * @property string $session_token
 * @property string $editing_section
 * @property Carbon $last_activity
 * @property Carbon $started_at
 * @property array|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Page $page
 * @property-read Admin $admin
 */
class PageEditingSession extends Model
{
    protected $fillable = [
        'page_id',
        'admin_id',
        'session_token',
        'editing_section',
        'last_activity',
        'started_at',
        'metadata'
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'started_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Get the page that this session is editing
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the admin who owns this editing session
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Generate a unique session token
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Check if this session is still active (within timeout)
     */
    public function isActive(int $timeoutMinutes = 60): bool
    {
        return $this->last_activity->gt(now()->subMinutes($timeoutMinutes));
    }

    /**
     * Update the last activity timestamp
     */
    public function updateActivity(): bool
    {
        return $this->update(['last_activity' => now()]);
    }

    /**
     * Get active sessions for a specific page
     */
    public static function getActiveForPage(int $pageId, int $timeoutMinutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return static::with('admin')
            ->where('page_id', $pageId)
            ->where('last_activity', '>', now()->subMinutes($timeoutMinutes))
            ->orderBy('started_at')
            ->get();
    }

    /**
     * Get active sessions for a specific page and section
     */
    public static function getActiveForSection(int $pageId, string $section, int $timeoutMinutes = 60): \Illuminate\Database\Eloquent\Collection
    {
        return static::with('admin')
            ->where('page_id', $pageId)
            ->where('editing_section', $section)
            ->where('last_activity', '>', now()->subMinutes($timeoutMinutes))
            ->orderBy('started_at')
            ->get();
    }

    /**
     * Check if a page has any active editors
     */
    public static function hasActiveEditors(int $pageId, int $timeoutMinutes = 60): bool
    {
        return static::where('page_id', $pageId)
            ->where('last_activity', '>', now()->subMinutes($timeoutMinutes))
            ->exists();
    }

    /**
     * Get session by token
     */
    public static function findByToken(string $token): ?static
    {
        return static::with(['admin', 'page'])
            ->where('session_token', $token)
            ->first();
    }

    /**
     * Clean up stale sessions
     */
    public static function cleanupStale(int $timeoutMinutes = 60): int
    {
        return static::where('last_activity', '<', now()->subMinutes($timeoutMinutes))
            ->delete();
    }

    /**
     * Start a new editing session
     */
    public static function startSession(int $pageId, int $adminId, string $section = 'full_page', array $metadata = []): static
    {
        return static::create([
            'page_id' => $pageId,
            'admin_id' => $adminId,
            'session_token' => static::generateToken(),
            'editing_section' => $section,
            'started_at' => now(),
            'last_activity' => now(),
            'metadata' => array_merge([
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
            ], $metadata)
        ]);
    }

    /**
     * End the editing session
     */
    public function end(): bool
    {
        return $this->delete();
    }

    /**
     * Get session duration in minutes
     */
    public function getDurationAttribute(): int
    {
        return $this->started_at->diffInMinutes($this->last_activity);
    }

    /**
     * Get formatted session info for API responses
     */
    public function toSessionInfo(): array
    {
        return [
            'id' => $this->id,
            'session_token' => $this->session_token,
            'admin' => [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
                'email' => $this->admin->email
            ],
            'editing_section' => $this->editing_section,
            'started_at' => $this->started_at->toISOString(),
            'last_activity' => $this->last_activity->toISOString(),
            'duration_minutes' => $this->duration,
            'is_active' => $this->isActive()
        ];
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query, int $timeoutMinutes = 60)
    {
        return $query->where('last_activity', '>', now()->subMinutes($timeoutMinutes));
    }

    /**
     * Scope for specific page
     */
    public function scopeForPage($query, int $pageId)
    {
        return $query->where('page_id', $pageId);
    }

    /**
     * Scope for specific section
     */
    public function scopeForSection($query, string $section)
    {
        return $query->where('editing_section', $section);
    }
}