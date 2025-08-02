<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PageBuilderRenderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * PageRenderController
 * 
 * Handles page rendering with consolidated CSS management
 */
class PageRenderController extends Controller
{
    protected PageBuilderRenderService $renderService;
    
    public function __construct(PageBuilderRenderService $renderService)
    {
        $this->renderService = $renderService;
    }
    
    /**
     * Render page content with consolidated CSS
     */
    public function renderPage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'content' => 'required|array'
            ]);
            
            $pageContent = $request->get('content');
            $result = $this->renderService->renderPageContent($pageContent);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'html' => $result['html'],
                    'css' => $result['css'],
                    'stats' => $result['stats']
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to render page',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get consolidated CSS for all widgets on page
     */
    public function getPageCSS(): JsonResponse
    {
        try {
            $css = PageBuilderRenderService::getPageCSS(false);
            $stats = PageBuilderRenderService::getCSSStats();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'css' => $css,
                    'css_with_tags' => PageBuilderRenderService::getPageCSS(true),
                    'stats' => $stats
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get CSS',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Demo endpoint showing multiple widgets with consolidated CSS
     */
    public function demoMultipleWidgets(Request $request): JsonResponse
    {
        try {
            // Clear previous CSS
            PageBuilderRenderService::clearCSS();
            
            // Simulate rendering multiple widgets
            $demoContent = [
                'containers' => [
                    [
                        'columns' => [
                            [
                                'size' => 12,
                                'widgets' => [
                                    [
                                        'type' => 'heading',
                                        'settings' => [
                                            'general' => [
                                                'content' => [
                                                    'heading_text' => 'Welcome to Our Site',
                                                    'heading_level' => 'h1'
                                                ]
                                            ],
                                            'style' => [
                                                'typography' => [
                                                    'font_size' => 48,
                                                    'font_weight' => '700'
                                                ],
                                                'colors' => [
                                                    'text_color' => '#1f2937'
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'type' => 'button',
                                        'settings' => [
                                            'general' => [
                                                'content' => [
                                                    'text' => 'Get Started',
                                                    'url' => '/get-started'
                                                ]
                                            ],
                                            'style' => [
                                                'colors' => [
                                                    'background_color' => '#3b82f6',
                                                    'text_color' => '#ffffff'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            
            $result = $this->renderService->renderPageContent($demoContent);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'demo_content' => $demoContent,
                    'rendered_html' => $result['html'],
                    'consolidated_css' => $result['css'],
                    'stats' => $result['stats'],
                    'css_for_header' => PageBuilderRenderService::getPageCSS(true)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demo failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}