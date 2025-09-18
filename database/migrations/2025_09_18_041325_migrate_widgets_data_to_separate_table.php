<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration extracts existing widget data from the page_builder_content.widgets_data
     * JSON field and migrates it to the new page_builder_widgets table structure.
     */
    public function up(): void
    {
        // First, ensure the new table exists
        if (!Schema::hasTable('page_builder_widgets')) {
            throw new \Exception('page_builder_widgets table must exist before running this migration');
        }

        // Get all page builder content with widgets data
        $pageBuilderContents = DB::table('page_builder_content')
            ->whereNotNull('widgets_data')
            ->get();

        $migratedCount = 0;
        $skippedCount = 0;

        foreach ($pageBuilderContents as $pageContent) {
            try {
                $widgetsData = json_decode($pageContent->widgets_data, true);

                if (!is_array($widgetsData) || empty($widgetsData)) {
                    $skippedCount++;
                    continue;
                }

                // Extract widgets from the content structure for positioning info
                $contentData = json_decode($pageContent->content, true);
                $positionMap = $this->buildPositionMap($contentData);

                foreach ($widgetsData as $widgetId => $widgetData) {
                    // Skip if widget data is not properly structured
                    if (!isset($widgetData['type'])) {
                        $skippedCount++;
                        continue;
                    }

                    // Get position info from content structure
                    $positionInfo = $positionMap[$widgetId] ?? null;

                    // Prepare widget data for new structure
                    $newWidgetData = [
                        'page_id' => $pageContent->page_id,
                        'widget_id' => $widgetId,
                        'widget_type' => $widgetData['type'],
                        'container_id' => $positionInfo['container_id'] ?? null,
                        'column_id' => $positionInfo['column_id'] ?? null,
                        'sort_order' => $positionInfo['sort_order'] ?? 0,
                        'general_settings' => json_encode($widgetData['content'] ?? []),
                        'style_settings' => json_encode($widgetData['style'] ?? []),
                        'advanced_settings' => json_encode($widgetData['advanced'] ?? []),
                        'is_visible' => true,
                        'is_enabled' => true,
                        'version' => '1.0.0',
                        'view_count' => 0,
                        'interaction_count' => 0,
                        'created_by' => $pageContent->created_by,
                        'updated_by' => $pageContent->updated_by,
                        'created_at' => $pageContent->created_at,
                        'updated_at' => $pageContent->updated_at
                    ];

                    // Insert into new table
                    DB::table('page_builder_widgets')->insert($newWidgetData);
                    $migratedCount++;
                }

            } catch (\Exception $e) {
                // Log error but continue with other records
                \Log::warning("Failed to migrate widgets for page {$pageContent->page_id}: " . $e->getMessage());
                $skippedCount++;
            }
        }

        // Log migration results
        \Log::info("Widget data migration completed. Migrated: {$migratedCount}, Skipped: {$skippedCount}");
    }

    /**
     * Build a map of widget positions from the content structure
     */
    private function buildPositionMap($contentData): array
    {
        $positionMap = [];

        if (!isset($contentData['containers']) || !is_array($contentData['containers'])) {
            return $positionMap;
        }

        foreach ($contentData['containers'] as $containerIndex => $container) {
            $containerId = $container['id'] ?? "container_{$containerIndex}";

            if (!isset($container['columns']) || !is_array($container['columns'])) {
                continue;
            }

            foreach ($container['columns'] as $columnIndex => $column) {
                $columnId = $column['id'] ?? "column_{$columnIndex}";

                if (!isset($column['widgets']) || !is_array($column['widgets'])) {
                    continue;
                }

                foreach ($column['widgets'] as $widgetIndex => $widget) {
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

        return $positionMap;
    }

    /**
     * Reverse the migrations.
     *
     * Note: This will remove all migrated widget data from the new table.
     * The original widgets_data in page_builder_content table will remain intact.
     */
    public function down(): void
    {
        // Get all page IDs that had widgets migrated
        $pageIds = DB::table('page_builder_content')
            ->whereNotNull('widgets_data')
            ->pluck('page_id');

        // Remove migrated widgets from the new table
        if (!$pageIds->isEmpty()) {
            $deletedCount = DB::table('page_builder_widgets')
                ->whereIn('page_id', $pageIds)
                ->delete();

            \Log::info("Widget data migration rollback completed. Deleted {$deletedCount} widget records.");
        }
    }
};