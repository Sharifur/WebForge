<?php

namespace App\Console\Commands;

use App\Models\PageEditingSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupStaleEditingSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'editing:cleanup
                            {--timeout=60 : Session timeout in minutes}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--details : Show detailed information about deleted sessions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up stale page editing sessions that have exceeded the timeout period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timeoutMinutes = (int) $this->option('timeout');
        $dryRun = $this->option('dry-run');
        $details = $this->option('details');

        $this->info("🧹 Cleaning up stale editing sessions...");
        $this->info("⏰ Timeout: {$timeoutMinutes} minutes");

        if ($dryRun) {
            $this->warn("🔍 DRY RUN MODE - No sessions will be deleted");
        }

        try {
            // Get stale sessions before deleting them
            $staleSessions = PageEditingSession::with(['admin', 'page'])
                ->where('last_activity', '<', now()->subMinutes($timeoutMinutes))
                ->get();

            if ($staleSessions->isEmpty()) {
                $this->info("✅ No stale editing sessions found");
                return self::SUCCESS;
            }

            $this->info("🎯 Found {$staleSessions->count()} stale editing sessions");

            if ($details || $dryRun) {
                $this->table(
                    ['ID', 'Page', 'Admin', 'Section', 'Started', 'Last Activity', 'Duration (min)'],
                    $staleSessions->map(function ($session) {
                        return [
                            $session->id,
                            $session->page->title ?? 'Unknown',
                            $session->admin->name ?? 'Unknown',
                            $session->editing_section,
                            $session->started_at->format('Y-m-d H:i:s'),
                            $session->last_activity->format('Y-m-d H:i:s'),
                            $session->started_at->diffInMinutes($session->last_activity)
                        ];
                    })->toArray()
                );
            }

            if (!$dryRun) {
                // Delete stale sessions
                $deletedCount = PageEditingSession::cleanupStale($timeoutMinutes);

                $this->info("🗑️  Successfully deleted {$deletedCount} stale editing sessions");

                // Log the cleanup activity
                Log::info('Scheduled cleanup of stale editing sessions', [
                    'deleted_count' => $deletedCount,
                    'timeout_minutes' => $timeoutMinutes,
                    'command' => 'editing:cleanup'
                ]);

                // Show summary
                $this->newLine();
                $this->info("📊 Cleanup Summary:");
                $this->line("   • Sessions deleted: {$deletedCount}");
                $this->line("   • Timeout threshold: {$timeoutMinutes} minutes");
                $this->line("   • Completed at: " . now()->format('Y-m-d H:i:s'));
            } else {
                $this->info("💡 Would delete {$staleSessions->count()} sessions (use without --dry-run to actually delete)");
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Failed to cleanup stale editing sessions: {$e->getMessage()}");

            Log::error('Failed to cleanup stale editing sessions', [
                'error' => $e->getMessage(),
                'timeout_minutes' => $timeoutMinutes,
                'command' => 'editing:cleanup'
            ]);

            return self::FAILURE;
        }
    }
}