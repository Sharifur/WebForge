<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug', 
        'content',
        'show_breadcrumb',
        'use_page_builder',
        'status',
        'created_by',
        'updated_by'
    ];

    protected function casts(): array
    {
        return [
            'show_breadcrumb' => 'boolean',
            'use_page_builder' => 'boolean',
            'content' => 'json',
        ];
    }

    public function metaInformation()
    {
        return $this->morphOne(MetaInformation::class, 'metable');
    }
    
    public function pageBuilderContent()
    {
        return $this->hasOne(PageBuilderContent::class);
    }

    public function getPageBuilderContentAttribute()
    {
        return $this->pageBuilderContent()->latest()->first();
    }

    /**
     * Get all widgets for this page
     */
    public function widgets()
    {
        return $this->hasMany(PageBuilderWidget::class)->ordered();
    }

    /**
     * Get visible and enabled widgets only
     */
    public function activeWidgets()
    {
        return $this->widgets()->visible()->enabled();
    }

    /**
     * Get widgets by type
     */
    public function widgetsByType(string $type)
    {
        return $this->widgets()->ofType($type);
    }

    /**
     * Get widgets in a specific container
     */
    public function widgetsInContainer(string $containerId)
    {
        return $this->widgets()->inContainer($containerId);
    }

    /**
     * Get widgets in a specific column
     */
    public function widgetsInColumn(string $columnId)
    {
        return $this->widgets()->inColumn($columnId);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}