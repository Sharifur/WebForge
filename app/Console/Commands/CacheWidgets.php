<?php

namespace App\Console\Commands;

use App\Services\WidgetCacheService;
use App\Models\Page;
use Illuminate\Console\Command;

class CacheWidgets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:widgets
                           {action : Action to perform (warmup|clean|clear|stats)}
                           {--page-id= : Specific page ID to cache}
                           {--all : Process all pages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage widget caching for page builder';

    /**
     * Widget cache service
     */
    protected WidgetCacheService $cacheService;

    /**
     * Create a new command instance.
     */
    public function __construct(WidgetCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'warmup':
                return $this->warmupCache();

            case 'clean':
                return $this->cleanExpiredCache();

            case 'clear':
                return $this->clearCache();

            case 'stats':
                return $this->showStats();

            default:
                $this->error("Invalid action: {$action}");
                $this->info('Available actions: warmup, clean, clear, stats');
                return 1;
        }
    }

    /**
     * Warm up widget cache
     */
    protected function warmupCache(): int
    {
        if ($pageId = $this->option('page-id')) {
            $this->info("Warming up cache for page {$pageId}...");
            $success = $this->cacheService->warmupPageCache((int) $pageId);

            if ($success) {
                $this->info("✅ Cache warmed up for page {$pageId}");
                return 0;
            } else {
                $this->error("❌ Failed to warm up cache for page {$pageId}");
                return 1;
            }
        }

        if ($this->option('all')) {
            $pages = Page::where('status', 'published')->pluck('id');
            $this->info("Warming up cache for {$pages->count()} pages...");

            $bar = $this->output->createProgressBar($pages->count());
            $bar->start();

            $success = 0;
            $failed = 0;

            foreach ($pages as $pageId) {
                if ($this->cacheService->warmupPageCache($pageId)) {
                    $success++;
                } else {
                    $failed++;
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            $this->info("✅ Cache warmed up for {$success} pages");
            if ($failed > 0) {
                $this->warn("⚠️  Failed to warm up cache for {$failed} pages");
            }

            return $failed > 0 ? 1 : 0;
        }

        $this->error('Please specify --page-id or --all option');
        return 1;
    }

    /**
     * Clean expired cache entries
     */
    protected function cleanExpiredCache(): int
    {
        $this->info('Cleaning expired cache entries...');
        $cleaned = $this->cacheService->cleanExpiredCache();

        $this->info("✅ Cleaned {$cleaned} expired cache entries");
        return 0;
    }

    /**
     * Clear all cache
     */
    protected function clearCache(): int
    {
        if (!$this->confirm('Are you sure you want to clear ALL widget cache?')) {
            $this->info('Operation cancelled');
            return 0;
        }

        $this->info('Clearing all widget cache...');
        $success = $this->cacheService->clearAllCache();

        if ($success) {
            $this->info('✅ All widget cache cleared');
            return 0;
        } else {
            $this->error('❌ Failed to clear cache');
            return 1;
        }
    }

    /**
     * Show cache statistics
     */
    protected function showStats(): int
    {
        $this->info('Widget Cache Statistics');
        $this->line('========================');

        $stats = $this->cacheService->getCacheStats();

        if (empty($stats)) {
            $this->error('❌ Failed to retrieve cache statistics');
            return 1;
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Widgets', $stats['total_widgets']],
                ['Cached Widgets', $stats['cached_widgets']],
                ['Cache Hit Rate', $stats['cache_hit_rate'] . '%'],
                ['Expired Cache Entries', $stats['expired_cache']],
                ['Estimated Cache Size', $stats['cache_size']]
            ]
        );

        // Performance recommendations
        $this->newLine();
        $this->info('Performance Recommendations:');

        if ($stats['cache_hit_rate'] < 50) {
            $this->warn('• Low cache hit rate. Consider running cache:widgets warmup --all');
        }

        if ($stats['expired_cache'] > 0) {
            $this->warn("• {$stats['expired_cache']} expired cache entries. Run cache:widgets clean");
        }

        if ($stats['cache_hit_rate'] >= 80) {
            $this->info('• ✅ Good cache performance');
        }

        return 0;
    }
}
