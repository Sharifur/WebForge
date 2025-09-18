<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * PageBuilderContent Model
 * 
 * Stores page builder layout structure (containers and columns)
 * Widget data is now stored separately in the page_builder_widgets table
 *
 * @property int $id
 * @property int $page_id
 * @property array|null $content
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PageBuilderWidget[] $widgets
 */
class PageBuilderContent extends Model
{
    protected $table = 'page_builder_content';
    
    protected $fillable = [
        'page_id',
        'content',
        'version',
        'is_published',
        'published_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'content' => 'array',
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
     * Get all widgets for this page builder content
     */
    public function widgets(): HasMany
    {
        return $this->hasMany(PageBuilderWidget::class, 'page_id', 'page_id')
                    ->ordered();
    }

    /**
     * Get visible widgets only
     */
    public function visibleWidgets(): HasMany
    {
        return $this->widgets()->visible()->enabled();
    }

    /**
     * Get widgets by type
     */
    public function widgetsByType(string $type): HasMany
    {
        return $this->widgets()->ofType($type);
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
     * Get the total count of widgets for this page
     */
    public function getWidgetCountAttribute(): int
    {
        return $this->widgets()->count();
    }

    /**
     * Get the count of visible widgets
     */
    public function getVisibleWidgetCountAttribute(): int
    {
        return $this->visibleWidgets()->count();
    }

    /**
     * Get widget analytics summary
     */
    public function getWidgetAnalytics(): array
    {
        $widgets = $this->widgets;

        return [
            'total_widgets' => $widgets->count(),
            'visible_widgets' => $widgets->where('is_visible', true)->count(),
            'enabled_widgets' => $widgets->where('is_enabled', true)->count(),
            'total_views' => $widgets->sum('view_count'),
            'total_interactions' => $widgets->sum('interaction_count'),
            'widgets_by_type' => $widgets->groupBy('widget_type')
                                       ->map(fn($group) => $group->count())
                                       ->toArray(),
            'cached_widgets' => $widgets->filter(fn($w) => $w->isCacheValid())->count()
        ];
    }

    /**
     * Clear all widget caches for this page
     */
    public function clearAllWidgetCaches(): int
    {
        $count = 0;
        $this->widgets->each(function($widget) use (&$count) {
            if ($widget->isCacheValid()) {
                $widget->clearCache();
                $count++;
            }
        });

        return $count;
    }

    /**
     * Sync widgets with content structure
     * Updates widget positions based on current content layout
     */
    public function syncWidgetPositions(): void
    {
        $content = $this->content ?? ['containers' => []];
        $positionMap = [];

        // Build position map from content structure
        foreach ($content['containers'] ?? [] as $containerIndex => $container) {
            $containerId = $container['id'] ?? "container_{$containerIndex}";

            foreach ($container['columns'] ?? [] as $columnIndex => $column) {
                $columnId = $column['id'] ?? "column_{$columnIndex}";

                foreach ($column['widgets'] ?? [] as $widgetIndex => $widget) {
                    $widgetId = $widget['id'] ?? null;

                    if ($widgetId) {
                        $positionMap[$widgetId] = [
                            'container_id' => $containerId,
                            'column_id' => $columnId,
                            'sort_order' => $widgetIndex
                        ];
                    }
                }
            }
        }

        // Update widget positions
        foreach ($this->widgets as $widget) {
            if (isset($positionMap[$widget->widget_id])) {
                $position = $positionMap[$widget->widget_id];
                $widget->moveTo(
                    $position['container_id'],
                    $position['column_id'],
                    $position['sort_order']
                );
            }
        }
    }
}
