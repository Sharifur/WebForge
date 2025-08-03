<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * ButtonPreset Model
 * 
 * Stores custom and built-in button presets for the enhanced button widget
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $category
 * @property array $style_settings
 * @property bool $is_public
 * @property bool $is_builtin
 * @property string|null $preview_image
 * @property array|null $tags
 * @property int|null $created_by
 * @property int $usage_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Admin|null $creator
 */
class ButtonPreset extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'style_settings',
        'is_public',
        'is_builtin',
        'preview_image',
        'tags',
        'created_by',
        'usage_count'
    ];
    
    protected $casts = [
        'style_settings' => 'array',
        'tags' => 'array',
        'is_public' => 'boolean',
        'is_builtin' => 'boolean',
        'usage_count' => 'integer'
    ];
    
    /**
     * Get the admin who created this preset
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    
    /**
     * Automatically generate slug from name
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($preset) {
            if (empty($preset->slug)) {
                $preset->slug = Str::slug($preset->name);
            }
        });
        
        static::updating(function ($preset) {
            if ($preset->isDirty('name') && empty($preset->slug)) {
                $preset->slug = Str::slug($preset->name);
            }
        });
    }
    
    /**
     * Scope to get public presets
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
    
    /**
     * Scope to get built-in presets
     */
    public function scopeBuiltin($query)
    {
        return $query->where('is_builtin', true);
    }
    
    /**
     * Scope to get custom presets
     */
    public function scopeCustom($query)
    {
        return $query->where('is_builtin', false);
    }
    
    /**
     * Scope to get presets by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
    
    /**
     * Scope to search presets by name or tags
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereJsonContains('tags', $search);
        });
    }
    
    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
    
    /**
     * Get popular presets
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderByDesc('usage_count')->limit($limit);
    }
    
    /**
     * Get recent presets
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }
    
    /**
     * Check if preset can be edited by user
     */
    public function canEdit(?int $userId = null): bool
    {
        if ($this->is_builtin) {
            return false;
        }
        
        if ($userId === null) {
            return false;
        }
        
        return $this->created_by === $userId;
    }
    
    /**
     * Get formatted style settings for frontend
     */
    public function getFormattedStyleSettings(): array
    {
        $settings = $this->style_settings ?? [];
        
        // Ensure all required fields have default values
        return array_merge([
            'background_color' => '#3B82F6',
            'text_color' => '#FFFFFF',
            'border_width' => 0,
            'border_color' => '#3B82F6',
            'border_radius' => 6,
            'padding' => ['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24],
            'font_size' => 16,
            'font_weight' => '600',
            'transition_duration' => 200
        ], $settings);
    }
}
