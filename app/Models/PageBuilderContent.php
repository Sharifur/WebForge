<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * PageBuilderContent Model
 * 
 * Stores page builder content structure and widget data separately from pages
 * 
 * @property int $id
 * @property int $page_id
 * @property array|null $content
 * @property array|null $widgets_data
 * @property string $version
 * @property bool $is_published
 * @property \Carbon\Carbon|null $published_at
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Page $page
 * @property-read \App\Models\Admin $creator
 * @property-read \App\Models\Admin|null $updater
 */
class PageBuilderContent extends Model
{
    protected $table = 'page_builder_content';
    
    protected $fillable = [
        'page_id',
        'content',
        'widgets_data',
        'version',
        'is_published',
        'published_at',
        'created_by',
        'updated_by'
    ];
    
    protected $casts = [
        'content' => 'array',
        'widgets_data' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime'
    ];
    
    /**
     * Get the page that owns this content
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
    
    /**
     * Get the admin who created this content
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    
    /**
     * Get the admin who last updated this content
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
    
    /**
     * Get the content with fallback to empty structure
     */
    protected function content(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? json_decode($value, true) : ['containers' => []],
            set: fn ($value) => json_encode($value ?? ['containers' => []])
        );
    }
    
    /**
     * Get the widgets data with fallback to empty array
     */
    protected function widgetsData(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? json_decode($value, true) : [],
            set: fn ($value) => json_encode($value ?? [])
        );
    }
    
    /**
     * Scope to get published content
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
    
    /**
     * Scope to get draft content
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }
    
    /**
     * Mark content as published
     */
    public function publish()
    {
        $this->update([
            'is_published' => true,
            'published_at' => now()
        ]);
    }
    
    /**
     * Mark content as draft
     */
    public function unpublish()
    {
        $this->update([
            'is_published' => false,
            'published_at' => null
        ]);
    }
    
    /**
     * Extract individual widget data from content structure
     */
    public function extractWidgetsData(): array
    {
        $widgets = [];
        $content = $this->content ?? ['containers' => []];
        
        foreach ($content['containers'] ?? [] as $container) {
            foreach ($container['columns'] ?? [] as $column) {
                foreach ($column['widgets'] ?? [] as $widget) {
                    $widgets[$widget['id']] = [
                        'type' => $widget['type'],
                        'content' => $widget['content'] ?? [],
                        'style' => $widget['style'] ?? [],
                        'advanced' => $widget['advanced'] ?? [],
                        'container_id' => $container['id'] ?? null,
                        'column_id' => $column['id'] ?? null
                    ];
                }
            }
        }
        
        return $widgets;
    }
    
    /**
     * Update widgets data when content changes
     */
    public function updateWidgetsData()
    {
        $this->widgets_data = $this->extractWidgetsData();
        $this->save();
    }
}
