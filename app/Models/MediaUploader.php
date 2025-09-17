<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaUploader extends Model
{
    protected $table = 'media_uploader';

    protected $fillable = [
        'filename',
        'stored_filename',
        'path',
        'url',
        'mime_type',
        'size',
        'width',
        'height',
        'alt',
        'title',
        'description',
        'caption',
        'uploaded_by',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who uploaded this media
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    /**
     * Scope for active media only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes === 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    /**
     * Check if the media is an image
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Delete the media file from storage
     */
    public function deleteFile(): bool
    {
        if (Storage::exists($this->path)) {
            return Storage::delete($this->path);
        }
        return true;
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        // Delete physical file when model is deleted
        static::deleted(function ($media) {
            $media->deleteFile();
        });
    }
}
