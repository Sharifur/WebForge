<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageBuilderContent;
use App\Models\PageBuilderWidget;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PageBuilderController extends Controller
{
    /**
     * Save page builder content for a page
     */
    public function saveContent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page_id' => 'required|integer|exists:pages,id',
            'content' => 'required|array',
            'content.containers' => 'sometimes|array',
            'widgets' => 'sometimes|array',
            'is_published' => 'sometimes|boolean',
            'version' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Find the page by ID from request
            $pageId = $request->input('page_id');
            $page = Page::findOrFail($pageId);

            $content = $request->input('content', ['containers' => []]);
            $widgets = $request->input('widgets', []);
            $isPublished = $request->input('is_published', false);
            $version = $request->input('version', '1.0');

            // Create or update page builder content (layout only)
            $pageBuilderContent = PageBuilderContent::updateOrCreate(
                ['page_id' => $page->id],
                [
                    'content' => $content,
                    'version' => $version,
                    'is_published' => $isPublished,
                    'published_at' => $isPublished ? now() : null,
                    'created_by' => Auth::guard('admin')->id(),
                    'updated_by' => Auth::guard('admin')->id()
                ]
            );

            // Handle widgets separately
            $widgetStats = $this->syncPageWidgets($page->id, $widgets);

            // Sync widget positions with content structure
            $pageBuilderContent->syncWidgetPositions();

            // If page uses page builder, ensure it's marked as such
            if (!$page->use_page_builder) {
                $page->update(['use_page_builder' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Page builder content saved successfully',
                'data' => [
                    'id' => $pageBuilderContent->id,
                    'page_id' => $page->id,
                    'version' => $pageBuilderContent->version,
                    'is_published' => $pageBuilderContent->is_published,
                    'published_at' => $pageBuilderContent->published_at,
                    'updated_at' => $pageBuilderContent->updated_at,
                    'widgets_count' => $widgetStats['total'],
                    'widgets_stats' => $widgetStats
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save page builder content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get page builder content for a page
     */
    public function getContent(int $pageId): JsonResponse
    {
        try {
            $page = Page::findOrFail($pageId);
            $pageBuilderContent = $page->pageBuilderContent;

            if (!$pageBuilderContent) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'content' => ['containers' => []],
                        'widgets' => [],
                        'version' => '1.0',
                        'is_published' => false,
                        'published_at' => null
                    ]
                ]);
            }

            // Get widgets separately
            $widgets = $page->widgets()->with(['creator', 'updater'])->get();
            $widgetsArray = [];

            foreach ($widgets as $widget) {
                $widgetsArray[$widget->widget_id] = [
                    'id' => $widget->widget_id,
                    'type' => $widget->widget_type,
                    'container_id' => $widget->container_id,
                    'column_id' => $widget->column_id,
                    'sort_order' => $widget->sort_order,
                    'settings' => $widget->all_settings,
                    'is_visible' => $widget->is_visible,
                    'is_enabled' => $widget->is_enabled,
                    'version' => $widget->version,
                    'analytics' => $widget->getAnalytics(),
                    'created_at' => $widget->created_at,
                    'updated_at' => $widget->updated_at
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pageBuilderContent->id,
                    'content' => $pageBuilderContent->content,
                    'widgets' => $widgetsArray,
                    'version' => $pageBuilderContent->version,
                    'is_published' => $pageBuilderContent->is_published,
                    'published_at' => $pageBuilderContent->published_at,
                    'created_at' => $pageBuilderContent->created_at,
                    'updated_at' => $pageBuilderContent->updated_at,
                    'widget_analytics' => $pageBuilderContent->getWidgetAnalytics()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get page builder content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish page builder content
     */
    public function publish(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page_id' => 'required|integer|exists:pages,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pageId = $request->input('page_id');
            $page = Page::findOrFail($pageId);
            $pageBuilderContent = $page->pageBuilderContent;

            if (!$pageBuilderContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No page builder content found'
                ], 404);
            }

            $pageBuilderContent->publish();

            return response()->json([
                'success' => true,
                'message' => 'Page builder content published successfully',
                'data' => [
                    'is_published' => true,
                    'published_at' => $pageBuilderContent->published_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish page builder content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unpublish page builder content
     */
    public function unpublish(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page_id' => 'required|integer|exists:pages,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pageId = $request->input('page_id');
            $page = Page::findOrFail($pageId);
            $pageBuilderContent = $page->pageBuilderContent;

            if (!$pageBuilderContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No page builder content found'
                ], 404);
            }

            $pageBuilderContent->unpublish();

            return response()->json([
                'success' => true,
                'message' => 'Page builder content unpublished successfully',
                'data' => [
                    'is_published' => false,
                    'published_at' => null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unpublish page builder content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get page builder content history/versions
     */
    public function getHistory(int $pageId): JsonResponse
    {
        try {
            $page = Page::findOrFail($pageId);
            $history = PageBuilderContent::where('page_id', $page->id)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'version', 'is_published', 'published_at', 'created_at', 'updated_at']);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get page builder content history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get individual widget data
     */
    public function getWidgetData(int $pageId, string $widgetId): JsonResponse
    {
        try {
            $page = Page::findOrFail($pageId);
            $pageBuilderContent = $page->pageBuilderContent;

            if (!$pageBuilderContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No page builder content found'
                ], 404);
            }

            $widget = PageBuilderWidget::where('page_id', $page->id)
                                         ->where('widget_id', $widgetId)
                                         ->first();

            if (!$widget) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $widget->widget_id,
                    'type' => $widget->widget_type,
                    'settings' => $widget->all_settings,
                    'is_visible' => $widget->is_visible,
                    'is_enabled' => $widget->is_enabled,
                    'analytics' => $widget->getAnalytics(),
                    'cache_status' => $widget->isCacheValid() ? 'valid' : 'expired',
                    'created_at' => $widget->created_at,
                    'updated_at' => $widget->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get widget data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync page widgets with provided widget data
     * Creates, updates, or removes widgets as needed
     */
    private function syncPageWidgets(int $pageId, array $widgets): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
            'total' => 0
        ];

        $adminId = Auth::guard('admin')->id();
        $providedWidgetIds = array_keys($widgets);

        // Get existing widgets for this page
        $existingWidgets = PageBuilderWidget::where('page_id', $pageId)
                                            ->get()
                                            ->keyBy('widget_id');

        // Process provided widgets
        foreach ($widgets as $widgetId => $widgetData) {
            if (!isset($widgetData['type'])) {
                continue; // Skip invalid widget data
            }

            $widgetAttributes = [
                'page_id' => $pageId,
                'widget_id' => $widgetId,
                'widget_type' => $widgetData['type'],
                'container_id' => $widgetData['container_id'] ?? null,
                'column_id' => $widgetData['column_id'] ?? null,
                'sort_order' => $widgetData['sort_order'] ?? 0,
                'general_settings' => $widgetData['settings']['general'] ?? [],
                'style_settings' => $widgetData['settings']['style'] ?? [],
                'advanced_settings' => $widgetData['settings']['advanced'] ?? [],
                'is_visible' => $widgetData['is_visible'] ?? true,
                'is_enabled' => $widgetData['is_enabled'] ?? true,
                'version' => $widgetData['version'] ?? '1.0.0',
                'updated_by' => $adminId
            ];

            if ($existingWidgets->has($widgetId)) {
                // Update existing widget
                $existingWidgets[$widgetId]->update($widgetAttributes);
                $stats['updated']++;
            } else {
                // Create new widget
                $widgetAttributes['created_by'] = $adminId;
                PageBuilderWidget::create($widgetAttributes);
                $stats['created']++;
            }
        }

        // Remove widgets that are no longer present
        $widgetsToDelete = $existingWidgets->whereNotIn('widget_id', $providedWidgetIds);
        $stats['deleted'] = $widgetsToDelete->count();

        foreach ($widgetsToDelete as $widget) {
            $widget->delete();
        }

        $stats['total'] = count($widgets);

        return $stats;
    }

    /**
     * Create a new widget
     */
    public function createWidget(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page_id' => 'required|integer|exists:pages,id',
            'widget_id' => 'required|string',
            'widget_type' => 'required|string',
            'container_id' => 'sometimes|string',
            'column_id' => 'sometimes|string',
            'sort_order' => 'sometimes|integer',
            'settings' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $widget = PageBuilderWidget::create([
                'page_id' => $request->input('page_id'),
                'widget_id' => $request->input('widget_id'),
                'widget_type' => $request->input('widget_type'),
                'container_id' => $request->input('container_id'),
                'column_id' => $request->input('column_id'),
                'sort_order' => $request->input('sort_order', 0),
                'general_settings' => $request->input('settings.general', []),
                'style_settings' => $request->input('settings.style', []),
                'advanced_settings' => $request->input('settings.advanced', []),
                'created_by' => Auth::guard('admin')->id(),
                'updated_by' => Auth::guard('admin')->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Widget created successfully',
                'data' => [
                    'id' => $widget->widget_id,
                    'type' => $widget->widget_type,
                    'settings' => $widget->all_settings
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create widget',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a widget
     */
    public function updateWidget(Request $request, int $pageId, string $widgetId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'sometimes|array',
            'is_visible' => 'sometimes|boolean',
            'is_enabled' => 'sometimes|boolean',
            'container_id' => 'sometimes|string',
            'column_id' => 'sometimes|string',
            'sort_order' => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $widget = PageBuilderWidget::where('page_id', $pageId)
                                       ->where('widget_id', $widgetId)
                                       ->firstOrFail();

            $updateData = ['updated_by' => Auth::guard('admin')->id()];

            if ($request->has('settings')) {
                $settings = $request->input('settings');
                if (isset($settings['general'])) {
                    $updateData['general_settings'] = $settings['general'];
                }
                if (isset($settings['style'])) {
                    $updateData['style_settings'] = $settings['style'];
                }
                if (isset($settings['advanced'])) {
                    $updateData['advanced_settings'] = $settings['advanced'];
                }
            }

            if ($request->has('is_visible')) {
                $updateData['is_visible'] = $request->boolean('is_visible');
            }

            if ($request->has('is_enabled')) {
                $updateData['is_enabled'] = $request->boolean('is_enabled');
            }

            if ($request->has('container_id')) {
                $updateData['container_id'] = $request->input('container_id');
            }

            if ($request->has('column_id')) {
                $updateData['column_id'] = $request->input('column_id');
            }

            if ($request->has('sort_order')) {
                $updateData['sort_order'] = $request->input('sort_order');
            }

            $widget->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Widget updated successfully',
                'data' => [
                    'id' => $widget->widget_id,
                    'type' => $widget->widget_type,
                    'settings' => $widget->all_settings,
                    'is_visible' => $widget->is_visible,
                    'is_enabled' => $widget->is_enabled
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update widget',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a widget
     */
    public function deleteWidget(int $pageId, string $widgetId): JsonResponse
    {
        try {
            $widget = PageBuilderWidget::where('page_id', $pageId)
                                       ->where('widget_id', $widgetId)
                                       ->firstOrFail();

            $widget->delete();

            return response()->json([
                'success' => true,
                'message' => 'Widget deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete widget',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate CSS for page components
     */
    public function generateCSS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:section,column,widget',
            'id' => 'required|string',
            'settings' => 'required|array',
            'responsiveSettings' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $type = $request->input('type');
            $id = $request->input('id');
            $settings = $request->input('settings', []);
            $responsiveSettings = $request->input('responsiveSettings', []);

            // Generate CSS using the service logic
            $css = $this->generateComponentCSS($type, $id, $settings, $responsiveSettings);

            return response()->json([
                'success' => true,
                'data' => [
                    'css' => $css,
                    'selector' => ".pb-{$type}-{$id}",
                    'type' => $type,
                    'id' => $id
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate CSS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate CSS for multiple components at once
     */
    public function generateBulkCSS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'components' => 'required|array',
            'components.*.type' => 'required|string|in:section,column,widget',
            'components.*.id' => 'required|string',
            'components.*.settings' => 'required|array',
            'components.*.responsiveSettings' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $components = $request->input('components', []);
            $results = [];
            $combinedCSS = [];

            foreach ($components as $component) {
                $type = $component['type'];
                $id = $component['id'];
                $settings = $component['settings'];
                $responsiveSettings = $component['responsiveSettings'] ?? [];

                $css = $this->generateComponentCSS($type, $id, $settings, $responsiveSettings);

                $results[] = [
                    'type' => $type,
                    'id' => $id,
                    'selector' => ".pb-{$type}-{$id}",
                    'css' => $css
                ];

                if ($css) {
                    $combinedCSS[] = $css;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'components' => $results,
                    'combinedCSS' => implode("\n\n", $combinedCSS)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate bulk CSS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get default settings for a component type
     */
    public function getDefaultSettings(string $type): JsonResponse
    {
        try {
            $defaults = $this->getComponentDefaults($type);

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'defaults' => $defaults
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get default settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate CSS for component (server-side implementation)
     */
    private function generateComponentCSS(string $type, string $id, array $settings, array $responsiveSettings = []): string
    {
        $baseSelector = ".pb-{$type}-{$id}";
        $css = [];

        // Merge with defaults
        $defaults = $this->getComponentDefaults($type);
        $mergedSettings = array_merge($defaults, $settings);

        // Generate base styles
        $baseStyles = $this->generateBaseStyles($baseSelector, $mergedSettings);
        if ($baseStyles) $css[] = $baseStyles;

        // Generate layout-specific styles for sections
        if ($type === 'section' && isset($mergedSettings['contentWidth'])) {
            $layoutStyles = $this->generateSectionLayoutCSS($mergedSettings['contentWidth'], $mergedSettings['maxWidth'] ?? 1200);
            if ($layoutStyles) $css[] = $layoutStyles;
        }

        // Generate responsive styles
        if (!empty($responsiveSettings)) {
            $responsiveStyles = $this->generateResponsiveStyles($baseSelector, $responsiveSettings);
            if ($responsiveStyles) $css[] = $responsiveStyles;
        }

        return implode("\n", $css);
    }

    /**
     * Get component default settings
     */
    private function getComponentDefaults(string $type): array
    {
        $baseDefaults = [
            'background' => ['type' => 'none', 'color' => '#ffffff'],
            'padding' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px'],
            'margin' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px'],
            'border' => [
                'width' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],
                'style' => 'solid',
                'color' => '#e2e8f0',
                'radius' => ['topLeft' => 0, 'topRight' => 0, 'bottomLeft' => 0, 'bottomRight' => 0, 'unit' => 'px']
            ],
            'visibility' => ['hideOnDesktop' => false, 'hideOnTablet' => false, 'hideOnMobile' => false]
        ];

        switch ($type) {
            case 'section':
                return array_merge($baseDefaults, [
                    'contentWidth' => 'boxed',
                    'maxWidth' => 1200,
                    'padding' => ['top' => 40, 'right' => 20, 'bottom' => 40, 'left' => 20, 'unit' => 'px']
                ]);

            case 'column':
                return array_merge($baseDefaults, [
                    'display' => 'flex',
                    'flexDirection' => 'column',
                    'padding' => ['top' => 15, 'right' => 15, 'bottom' => 15, 'left' => 15, 'unit' => 'px']
                ]);

            case 'widget':
                return array_merge($baseDefaults, [
                    'typography' => ['fontSize' => '16px', 'fontWeight' => '400', 'lineHeight' => '1.6'],
                    'padding' => ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px']
                ]);

            default:
                return $baseDefaults;
        }
    }

    /**
     * Generate base component styles (server-side implementation)
     */
    private function generateBaseStyles(string $selector, array $settings): string
    {
        $styles = [];

        // Background styles
        if (isset($settings['background']) && $settings['background']['type'] !== 'none') {
            $bgCSS = $this->generateBackgroundCSS($settings['background']);
            if ($bgCSS) $styles[] = $bgCSS;
        }

        // Spacing styles
        if (isset($settings['padding'])) {
            $paddingCSS = $this->normalizeSpacing($settings['padding']);
            if ($paddingCSS !== '0') $styles[] = "padding: {$paddingCSS};";
        }

        if (isset($settings['margin'])) {
            $marginCSS = $this->normalizeSpacing($settings['margin']);
            if ($marginCSS !== '0') $styles[] = "margin: {$marginCSS};";
        }

        // Border styles
        if (isset($settings['border'])) {
            $borderCSS = $this->generateBorderCSS($settings['border']);
            if ($borderCSS) $styles[] = $borderCSS;
        }

        if (empty($styles)) return '';

        return "{$selector} {\n  " . implode("\n  ", $styles) . "\n}";
    }

    /**
     * Generate section layout CSS (server-side implementation)
     */
    private function generateSectionLayoutCSS(string $layoutMode, int $maxWidth = 1200): string
    {
        switch ($layoutMode) {
            case 'boxed':
                return "
.section-layout-boxed {
  max-width: {$maxWidth}px;
  margin: 0 auto;
  padding-left: 15px;
  padding-right: 15px;
}";

            case 'full_width_contained':
                return "
.section-layout-full_width_contained {
  width: 100vw;
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
}

.section-layout-full_width_contained .section-inner {
  max-width: {$maxWidth}px;
  margin: 0 auto;
  padding-left: 15px;
  padding-right: 15px;
}";

            case 'full_width':
                return "
.section-layout-full_width {
  width: 100vw;
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
}

.section-layout-full_width .section-inner {
  width: 100%;
  max-width: none;
  padding-left: 15px;
  padding-right: 15px;
}";

            default:
                return '';
        }
    }

    /**
     * Generate responsive styles (server-side implementation)
     */
    private function generateResponsiveStyles(string $selector, array $responsiveSettings): string
    {
        $css = [];

        // Tablet styles
        if (isset($responsiveSettings['tablet']) && !empty($responsiveSettings['tablet'])) {
            $tabletStyles = $this->generateBaseStyles($selector, $responsiveSettings['tablet']);
            if ($tabletStyles) {
                $css[] = "@media (max-width: 1024px) {\n{$tabletStyles}\n}";
            }
        }

        // Mobile styles
        if (isset($responsiveSettings['mobile']) && !empty($responsiveSettings['mobile'])) {
            $mobileStyles = $this->generateBaseStyles($selector, $responsiveSettings['mobile']);
            if ($mobileStyles) {
                $css[] = "@media (max-width: 768px) {\n{$mobileStyles}\n}";
            }
        }

        return implode("\n", $css);
    }

    /**
     * Helper methods for CSS generation
     */
    private function generateBackgroundCSS(array $background): string
    {
        switch ($background['type']) {
            case 'color':
                return isset($background['color']) ? "background-color: {$background['color']};" : '';

            case 'gradient':
                if (isset($background['gradient'])) {
                    $gradient = $background['gradient'];
                    $stops = collect($gradient['colorStops'] ?? [])->map(function ($stop) {
                        return "{$stop['color']} {$stop['position']}%";
                    })->implode(', ');

                    if ($gradient['type'] === 'linear') {
                        return "background: linear-gradient({$gradient['angle']}deg, {$stops});";
                    } elseif ($gradient['type'] === 'radial') {
                        return "background: radial-gradient(circle, {$stops});";
                    }
                }
                return '';

            default:
                return '';
        }
    }

    private function generateBorderCSS(array $border): string
    {
        $styles = [];

        if (isset($border['width'])) {
            $width = $border['width'];
            if ($width['top'] || $width['right'] || $width['bottom'] || $width['left']) {
                $styles[] = "border-width: {$width['top']}px {$width['right']}px {$width['bottom']}px {$width['left']}px;";
                $styles[] = "border-style: " . ($border['style'] ?? 'solid') . ";";
                $styles[] = "border-color: " . ($border['color'] ?? '#e2e8f0') . ";";
            }
        }

        if (isset($border['radius'])) {
            $radius = $border['radius'];
            $unit = $radius['unit'] ?? 'px';
            if ($radius['topLeft'] || $radius['topRight'] || $radius['bottomLeft'] || $radius['bottomRight']) {
                $styles[] = "border-radius: {$radius['topLeft']}{$unit} {$radius['topRight']}{$unit} {$radius['bottomRight']}{$unit} {$radius['bottomLeft']}{$unit};";
            }
        }

        return implode(' ', $styles);
    }

    private function normalizeSpacing(array $spacing): string
    {
        if (isset($spacing['top'])) {
            $unit = $spacing['unit'] ?? 'px';
            return "{$spacing['top']}{$unit} {$spacing['right']}{$unit} {$spacing['bottom']}{$unit} {$spacing['left']}{$unit}";
        }

        return '0';
    }
}
