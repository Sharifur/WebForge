<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageBuilderContent;
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
    public function saveContent(Request $request, Page $page): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|array',
            'content.containers' => 'sometimes|array',
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

            $content = $request->input('content', ['containers' => []]);
            $isPublished = $request->input('is_published', false);
            $version = $request->input('version', '1.0');

            // Create or update page builder content
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

            // Update individual widgets data
            $pageBuilderContent->updateWidgetsData();

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
                    'widgets_count' => count($pageBuilderContent->widgets_data ?? [])
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
    public function getContent(Page $page): JsonResponse
    {
        try {
            $pageBuilderContent = $page->pageBuilderContent;

            if (!$pageBuilderContent) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'content' => ['containers' => []],
                        'widgets_data' => [],
                        'version' => '1.0',
                        'is_published' => false,
                        'published_at' => null
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pageBuilderContent->id,
                    'content' => $pageBuilderContent->content,
                    'widgets_data' => $pageBuilderContent->widgets_data,
                    'version' => $pageBuilderContent->version,
                    'is_published' => $pageBuilderContent->is_published,
                    'published_at' => $pageBuilderContent->published_at,
                    'created_at' => $pageBuilderContent->created_at,
                    'updated_at' => $pageBuilderContent->updated_at
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
    public function publish(Page $page): JsonResponse
    {
        try {
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
    public function unpublish(Page $page): JsonResponse
    {
        try {
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
    public function getHistory(Page $page): JsonResponse
    {
        try {
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
    public function getWidgetData(Page $page, string $widgetId): JsonResponse
    {
        try {
            $pageBuilderContent = $page->pageBuilderContent;

            if (!$pageBuilderContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No page builder content found'
                ], 404);
            }

            $widgetsData = $pageBuilderContent->widgets_data ?? [];
            
            if (!isset($widgetsData[$widgetId])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $widgetsData[$widgetId]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get widget data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
