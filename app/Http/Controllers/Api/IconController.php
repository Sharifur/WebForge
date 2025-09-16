<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Plugins\Pagebuilder\Services\IconService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * IconController - API controller for icon management
 *
 * Handles API requests for Line Awesome icons including listing,
 * searching, categorizing, and validating icons.
 */
class IconController extends Controller
{
    private IconService $iconService;

    public function __construct(IconService $iconService)
    {
        $this->iconService = $iconService;
    }

    /**
     * Get all available icons
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $icons = $this->iconService->getAllIcons();

            // Optional pagination
            $perPage = $request->input('per_page', 100);
            $page = $request->input('page', 1);

            if ($perPage > 0) {
                $total = count($icons);
                $offset = ($page - 1) * $perPage;
                $icons = array_slice($icons, $offset, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $icons,
                    'pagination' => [
                        'total' => $total,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'last_page' => ceil($total / $perPage),
                        'from' => $offset + 1,
                        'to' => min($offset + $perPage, $total),
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $icons,
                'total' => count($icons)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve icons',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get popular/commonly used icons
     *
     * @return JsonResponse
     */
    public function popular(): JsonResponse
    {
        try {
            $icons = $this->iconService->getPopularIcons();

            return response()->json([
                'success' => true,
                'data' => $icons,
                'total' => count($icons)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve popular icons',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available icon categories
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = $this->iconService->getCategories();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve categories',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get icons by category
     *
     * @param Request $request
     * @param string $category
     * @return JsonResponse
     */
    public function byCategory(Request $request, string $category): JsonResponse
    {
        try {
            $icons = $this->iconService->getIconsByCategory($category);

            if (empty($icons)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Category not found',
                    'message' => "No icons found for category: {$category}"
                ], 404);
            }

            // Optional pagination
            $perPage = $request->input('per_page', 100);
            $page = $request->input('page', 1);

            if ($perPage > 0) {
                $total = count($icons);
                $offset = ($page - 1) * $perPage;
                $icons = array_slice($icons, $offset, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $icons,
                    'category' => $category,
                    'pagination' => [
                        'total' => $total,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'last_page' => ceil($total / $perPage),
                        'from' => $offset + 1,
                        'to' => min($offset + $perPage, $total),
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $icons,
                'category' => $category,
                'total' => count($icons)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve icons by category',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Search icons
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
            'category' => 'nullable|string|max:50',
            'per_page' => 'nullable|integer|min:1|max:500',
            'page' => 'nullable|integer|min:1'
        ]);

        try {
            $query = $request->input('q');
            $category = $request->input('category');

            // Search icons by query
            $icons = $this->iconService->searchIcons($query);

            // Filter by category if specified
            if ($category) {
                $icons = array_filter($icons, function ($icon) use ($category) {
                    return in_array($category, $icon['categories']);
                });
                $icons = array_values($icons); // Re-index array
            }

            // Optional pagination
            $perPage = $request->input('per_page', 50);
            $page = $request->input('page', 1);

            if ($perPage > 0) {
                $total = count($icons);
                $offset = ($page - 1) * $perPage;
                $icons = array_slice($icons, $offset, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $icons,
                    'query' => $query,
                    'category' => $category,
                    'pagination' => [
                        'total' => $total,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'last_page' => ceil($total / $perPage),
                        'from' => $offset + 1,
                        'to' => min($offset + $perPage, $total),
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $icons,
                'query' => $query,
                'category' => $category,
                'total' => count($icons)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to search icons',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Validate an icon class
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'icon_class' => 'required|string|max:100'
        ]);

        try {
            $iconClass = $request->input('icon_class');
            $isValid = $this->iconService->validateIcon($iconClass);

            return response()->json([
                'success' => true,
                'valid' => $isValid,
                'icon_class' => $iconClass
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to validate icon',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get icon details by class name
     *
     * @param Request $request
     * @param string $iconClass
     * @return JsonResponse
     */
    public function show(string $iconClass): JsonResponse
    {
        try {
            // Decode URL-encoded icon class
            $iconClass = urldecode($iconClass);

            $allIcons = $this->iconService->getAllIcons();

            $icon = collect($allIcons)->firstWhere('cssClass', $iconClass);

            if (!$icon) {
                return response()->json([
                    'success' => false,
                    'error' => 'Icon not found',
                    'message' => "Icon with class '{$iconClass}' not found"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $icon
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve icon details',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Clear icons cache
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->iconService->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Icons cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to clear cache',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}