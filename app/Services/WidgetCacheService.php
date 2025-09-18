<?php

namespace App\Services;

use App\Models\PageBuilderWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Widget Cache Service
 *
 * Handles caching and optimization for page builder widgets
 * to improve performance and reduce database queries.
 */
class WidgetCacheService
{
    const CACHE_PREFIX = 'widget_cache_';
    const CSS_CACHE_PREFIX = 'widget_css_';
    const HTML_CACHE_PREFIX = 'widget_html_';
    const PAGE_WIDGETS_PREFIX = 'page_widgets_';

    const DEFAULT_TTL = 3600; // 1 hour
    const CSS_TTL = 7200;     // 2 hours
    const HTML_TTL = 1800;    // 30 minutes
    const PAGE_TTL = 3600;    // 1 hour

    /**
     * Cache widget HTML output
     */
    public function cacheWidgetHTML(string $widgetId, string $html, int $ttl = null): bool
    {
        try {
            $cacheKey = self::HTML_CACHE_PREFIX . $widgetId;
            $expiresAt = now()->addSeconds($ttl ?? self::HTML_TTL);

            // Store in cache
            $cached = Cache::put($cacheKey, [
                'html' => $html,
                'cached_at' => now()->toISOString(),
                'expires_at' => $expiresAt->toISOString()
            ], $expiresAt);

            // Update widget record
            $widget = PageBuilderWidget::where('widget_id', $widgetId)->first();
            if ($widget) {
                $widget->update([
                    'cached_html' => $html,
                    'cache_expires_at' => $expiresAt
                ]);
            }

            return $cached;

        } catch (\Exception $e) {
            Log::error('Failed to cache widget HTML', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get cached widget HTML
     */
    public function getCachedWidgetHTML(string $widgetId): ?string
    {
        try {
            $cacheKey = self::HTML_CACHE_PREFIX . $widgetId;
            $cached = Cache::get($cacheKey);

            if ($cached && isset($cached['html'])) {
                return $cached['html'];
            }

            // Fallback to database cache
            $widget = PageBuilderWidget::where('widget_id', $widgetId)->first();
            if ($widget && $widget->cached_html && $widget->cache_expires_at > now()) {
                return $widget->cached_html;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get cached widget HTML', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cache widget CSS
     */
    public function cacheWidgetCSS(string $widgetId, string $css, int $ttl = null): bool
    {
        try {
            $cacheKey = self::CSS_CACHE_PREFIX . $widgetId;
            $expiresAt = now()->addSeconds($ttl ?? self::CSS_TTL);

            // Store in cache
            $cached = Cache::put($cacheKey, [
                'css' => $css,
                'cached_at' => now()->toISOString(),
                'expires_at' => $expiresAt->toISOString()
            ], $expiresAt);

            // Update widget record
            $widget = PageBuilderWidget::where('widget_id', $widgetId)->first();
            if ($widget) {
                $widget->update([
                    'cached_css' => $css
                ]);
            }

            return $cached;

        } catch (\Exception $e) {
            Log::error('Failed to cache widget CSS', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get cached widget CSS
     */
    public function getCachedWidgetCSS(string $widgetId): ?string
    {
        try {
            $cacheKey = self::CSS_CACHE_PREFIX . $widgetId;
            $cached = Cache::get($cacheKey);

            if ($cached && isset($cached['css'])) {
                return $cached['css'];
            }

            // Fallback to database cache
            $widget = PageBuilderWidget::where('widget_id', $widgetId)->first();
            if ($widget && $widget->cached_css) {
                return $widget->cached_css;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get cached widget CSS', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cache page widgets data
     */
    public function cachePageWidgets(int $pageId, array $widgets, int $ttl = null): bool
    {
        try {
            $cacheKey = self::PAGE_WIDGETS_PREFIX . $pageId;
            $expiresAt = now()->addSeconds($ttl ?? self::PAGE_TTL);

            return Cache::put($cacheKey, [
                'widgets' => $widgets,
                'cached_at' => now()->toISOString(),
                'expires_at' => $expiresAt->toISOString(),
                'count' => count($widgets)
            ], $expiresAt);

        } catch (\Exception $e) {
            Log::error('Failed to cache page widgets', [
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get cached page widgets
     */
    public function getCachedPageWidgets(int $pageId): ?array
    {
        try {
            $cacheKey = self::PAGE_WIDGETS_PREFIX . $pageId;
            $cached = Cache::get($cacheKey);

            if ($cached && isset($cached['widgets'])) {
                return $cached['widgets'];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get cached page widgets', [
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Invalidate widget cache
     */
    public function invalidateWidgetCache(string $widgetId): bool
    {
        try {
            $keys = [
                self::HTML_CACHE_PREFIX . $widgetId,
                self::CSS_CACHE_PREFIX . $widgetId
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Clear database cache
            $widget = PageBuilderWidget::where('widget_id', $widgetId)->first();
            if ($widget) {
                $widget->update([
                    'cached_html' => null,
                    'cached_css' => null,
                    'cache_expires_at' => null
                ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to invalidate widget cache', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Invalidate page cache
     */
    public function invalidatePageCache(int $pageId): bool
    {
        try {
            $cacheKey = self::PAGE_WIDGETS_PREFIX . $pageId;
            Cache::forget($cacheKey);

            // Also invalidate all widgets on this page
            $widgets = PageBuilderWidget::where('page_id', $pageId)->pluck('widget_id');
            foreach ($widgets as $widgetId) {
                $this->invalidateWidgetCache($widgetId);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to invalidate page cache', [
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Warm up cache for a page
     */
    public function warmupPageCache(int $pageId): bool
    {
        try {
            // Get all widgets for the page
            $widgets = PageBuilderWidget::where('page_id', $pageId)
                ->where('is_enabled', true)
                ->orderBy('sort_order')
                ->get();

            $widgetData = [];
            foreach ($widgets as $widget) {
                $widgetData[] = [
                    'id' => $widget->widget_id,
                    'type' => $widget->widget_type,
                    'settings' => $widget->general_settings,
                    'style' => $widget->style_settings,
                    'advanced' => $widget->advanced_settings,
                    'responsive' => $widget->responsive_settings
                ];

                // Pre-generate and cache CSS if not exists
                if (!$this->getCachedWidgetCSS($widget->widget_id)) {
                    $css = $this->generateWidgetCSS($widget);
                    if ($css) {
                        $this->cacheWidgetCSS($widget->widget_id, $css);
                    }
                }
            }

            // Cache the complete page widget data
            return $this->cachePageWidgets($pageId, $widgetData);

        } catch (\Exception $e) {
            Log::error('Failed to warm up page cache', [
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clean expired cache entries
     */
    public function cleanExpiredCache(): int
    {
        try {
            $cleaned = 0;

            // Clean expired database cache
            $expiredWidgets = PageBuilderWidget::where('cache_expires_at', '<', now())
                ->whereNotNull('cache_expires_at')
                ->get();

            foreach ($expiredWidgets as $widget) {
                $widget->update([
                    'cached_html' => null,
                    'cached_css' => null,
                    'cache_expires_at' => null
                ]);
                $cleaned++;
            }

            Log::info('Cleaned expired widget cache entries', ['count' => $cleaned]);
            return $cleaned;

        } catch (\Exception $e) {
            Log::error('Failed to clean expired cache', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            $totalWidgets = PageBuilderWidget::count();
            $cachedWidgets = PageBuilderWidget::whereNotNull('cached_html')->count();
            $expiredCache = PageBuilderWidget::where('cache_expires_at', '<', now())
                ->whereNotNull('cache_expires_at')
                ->count();

            return [
                'total_widgets' => $totalWidgets,
                'cached_widgets' => $cachedWidgets,
                'cache_hit_rate' => $totalWidgets > 0 ? round(($cachedWidgets / $totalWidgets) * 100, 2) : 0,
                'expired_cache' => $expiredCache,
                'cache_size' => $this->estimateCacheSize()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get cache stats', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Estimate cache size
     */
    private function estimateCacheSize(): string
    {
        try {
            $size = PageBuilderWidget::whereNotNull('cached_html')
                ->orWhereNotNull('cached_css')
                ->get()
                ->sum(function ($widget) {
                    return strlen($widget->cached_html ?? '') + strlen($widget->cached_css ?? '');
                });

            return $this->formatBytes($size);

        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $base = 1024;
        $unitIndex = 0;

        while ($size >= $base && $unitIndex < count($units) - 1) {
            $size /= $base;
            $unitIndex++;
        }

        return round($size, $precision) . ' ' . $units[$unitIndex];
    }

    /**
     * Generate CSS for widget (placeholder - would integrate with actual CSS generation)
     */
    private function generateWidgetCSS(PageBuilderWidget $widget): ?string
    {
        // This would integrate with the actual CSS generation service
        // For now, return a placeholder
        return "/* CSS for widget {$widget->widget_id} */";
    }

    /**
     * Clear all widget cache
     */
    public function clearAllCache(): bool
    {
        try {
            // Clear Laravel cache
            $prefixes = [
                self::HTML_CACHE_PREFIX,
                self::CSS_CACHE_PREFIX,
                self::PAGE_WIDGETS_PREFIX
            ];

            foreach ($prefixes as $prefix) {
                Cache::flush(); // Note: This clears ALL cache, you might want to be more selective
            }

            // Clear database cache
            PageBuilderWidget::query()->update([
                'cached_html' => null,
                'cached_css' => null,
                'cache_expires_at' => null
            ]);

            Log::info('Cleared all widget cache');
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to clear all cache', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}