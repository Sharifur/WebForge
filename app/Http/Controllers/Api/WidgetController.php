<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Plugins\Pagebuilder\Core\WidgetRegistry;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class WidgetController extends Controller
{
    /**
     * Get all widgets with optional filters
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];
            
            // Apply filters from request
            if ($request->has('category')) {
                $filters['category'] = $request->get('category');
            }
            
            if ($request->has('is_pro')) {
                $filters['is_pro'] = $request->boolean('is_pro');
            }
            
            if ($request->has('tags')) {
                $filters['tags'] = explode(',', $request->get('tags'));
            }

            $widgets = WidgetRegistry::getWidgetsForApi($filters);

            return response()->json([
                'success' => true,
                'data' => $widgets,
                'meta' => [
                    'total' => count($widgets),
                    'filters_applied' => $filters
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch widgets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all widget categories with widget counts
     * 
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = WidgetRegistry::getCategoriesWithCounts();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'meta' => [
                    'total' => count($categories)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get widgets by specific category
     * 
     * @param string $category
     * @return JsonResponse
     */
    public function getByCategory(string $category): JsonResponse
    {
        try {
            if (!WidgetCategory::categoryExists($category)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $widgets = WidgetRegistry::getWidgetsByCategory($category);
            $categoryInfo = WidgetCategory::getCategory($category);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => [
                        'slug' => $category,
                        'name' => $categoryInfo['name'],
                        'icon' => $categoryInfo['icon'],
                        'description' => $categoryInfo['description'],
                        'color' => $categoryInfo['color']
                    ],
                    'widgets' => array_values($widgets)
                ],
                'meta' => [
                    'category' => $category,
                    'widget_count' => count($widgets)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch widgets by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search widgets by query
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'q' => 'required|string|min:1|max:100',
                'category' => 'sometimes|string|in:' . implode(',', WidgetCategory::getCategorySlugs()),
                'is_pro' => 'sometimes|boolean',
                'tags' => 'sometimes|string'
            ]);

            $query = $request->get('q');
            $filters = [];

            if ($request->has('category')) {
                $filters['category'] = $request->get('category');
            }

            if ($request->has('is_pro')) {
                $filters['is_pro'] = $request->boolean('is_pro');
            }

            if ($request->has('tags')) {
                $filters['tags'] = explode(',', $request->get('tags'));
            }

            $widgets = WidgetRegistry::searchWidgets($query, $filters);

            return response()->json([
                'success' => true,
                'data' => array_values($widgets),
                'meta' => [
                    'query' => $query,
                    'filters' => $filters,
                    'total_results' => count($widgets)
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get widget field configuration
     * 
     * @param string $type
     * @param Request $request
     * @return JsonResponse
     */
    public function getFields(string $type, Request $request): JsonResponse
    {
        try {
            if (!WidgetRegistry::widgetExists($type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget type not found'
                ], 404);
            }

            $tab = $request->get('tab');
            $fields = WidgetRegistry::getWidgetFields($type, $tab);
            $widgetConfig = WidgetRegistry::getWidgetConfig($type);

            $response = [
                'success' => true,
                'data' => [
                    'widget_type' => $type,
                    'widget_name' => $widgetConfig['name'],
                    'fields' => $fields
                ]
            ];

            if ($tab) {
                $response['data']['tab'] = $tab;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch widget fields',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get widget fields for specific tab
     * 
     * @param string $type
     * @param string $tab
     * @return JsonResponse
     */
    public function getFieldsByTab(string $type, string $tab): JsonResponse
    {
        try {
            if (!WidgetRegistry::widgetExists($type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget type not found'
                ], 404);
            }

            $fields = WidgetRegistry::getWidgetFields($type, $tab);
            $widgetConfig = WidgetRegistry::getWidgetConfig($type);

            return response()->json([
                'success' => true,
                'data' => [
                    'widget_type' => $type,
                    'widget_name' => $widgetConfig['name'],
                    'tab' => $tab,
                    'fields' => $fields
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch widget fields',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get widget configuration
     * 
     * @param string $type
     * @return JsonResponse
     */
    public function getConfig(string $type): JsonResponse
    {
        try {
            $config = WidgetRegistry::getWidgetConfig($type);

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget type not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch widget configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate widget settings
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function validateSettings(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'widget_type' => 'required|string',
                'settings' => 'required|array'
            ]);

            $widgetType = $request->get('widget_type');
            $settings = $request->get('settings');

            if (!WidgetRegistry::widgetExists($widgetType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget type not found'
                ], 404);
            }

            $errors = WidgetRegistry::validateWidgetSettings($widgetType, $settings);

            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => empty($errors),
                    'errors' => $errors
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save widget settings (placeholder - would save to database)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function saveSettings(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'widget_type' => 'required|string',
                'widget_id' => 'sometimes|string',
                'page_id' => 'required|integer',
                'position' => 'required|array',
                'settings' => 'required|array'
            ]);

            $widgetType = $request->get('widget_type');
            $settings = $request->get('settings');

            if (!WidgetRegistry::widgetExists($widgetType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget type not found'
                ], 404);
            }

            // Validate settings
            $errors = WidgetRegistry::validateWidgetSettings($widgetType, $settings);
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid widget settings',
                    'errors' => $errors
                ], 422);
            }

            // Here you would save to database
            // For now, just return success with the data that would be saved
            $widgetData = [
                'id' => $request->get('widget_id', 'widget_' . uniqid()),
                'type' => $widgetType,
                'page_id' => $request->get('page_id'),
                'position' => $request->get('position'),
                'settings' => $settings,
                'created_at' => now(),
                'updated_at' => now()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Widget settings saved successfully',
                'data' => $widgetData
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save widget settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular widgets
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $limit = min(max($limit, 1), 50); // Between 1 and 50

            $widgets = WidgetRegistry::getPopularWidgets($limit);

            return response()->json([
                'success' => true,
                'data' => array_values($widgets),
                'meta' => [
                    'limit' => $limit,
                    'total' => count($widgets)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch popular widgets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recently added widgets
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $limit = min(max($limit, 1), 50); // Between 1 and 50

            $widgets = WidgetRegistry::getRecentWidgets($limit);

            return response()->json([
                'success' => true,
                'data' => array_values($widgets),
                'meta' => [
                    'limit' => $limit,
                    'total' => count($widgets)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent widgets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get widget registry statistics
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = WidgetRegistry::getStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear widget registry cache
     * 
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        try {
            WidgetRegistry::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Widget registry cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh widget registry
     * 
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            // Clear existing registry and cache
            WidgetRegistry::clear();
            WidgetRegistry::clearCache();
            
            // Force auto-discovery
            WidgetRegistry::autoDiscover();
            
            // Cache the results
            WidgetRegistry::cache();

            $stats = WidgetRegistry::getStats();

            return response()->json([
                'success' => true,
                'message' => 'Widget registry refreshed successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh widget registry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Render widget preview with settings
     * 
     * @param string $type
     * @param Request $request
     * @return JsonResponse
     */
    public function preview(string $type, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'settings' => 'required|array'
            ]);

            if (!WidgetRegistry::widgetExists($type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget type not found'
                ], 404);
            }

            $settings = $request->get('settings', []);
            
            // Get default values for the widget and merge with provided settings
            $defaults = WidgetRegistry::getWidgetDefaults($type);
            
            // Merge defaults with provided settings to ensure all required fields are present
            // Use recursive merge to handle nested arrays properly
            $mergedSettings = [
                'general' => $this->arrayMergeRecursive($defaults['general'] ?? [], $settings['general'] ?? []),
                'style' => $this->arrayMergeRecursive($defaults['style'] ?? [], $settings['style'] ?? []),
                'advanced' => $this->arrayMergeRecursive($defaults['advanced'] ?? [], $settings['advanced'] ?? [])
            ];

            // Validate the merged settings
            $errors = WidgetRegistry::validateWidgetSettings($type, $mergedSettings);
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid settings',
                    'errors' => $errors
                ], 400);
            }

            // Render the widget with merged settings
            $renderResult = WidgetRegistry::renderWidget($type, $mergedSettings);
            
            if (!$renderResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to render widget'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $renderResult
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to render widget preview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recursively merge arrays, with second array overriding the first
     */
    private function arrayMergeRecursive(array $array1, array $array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursive($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}