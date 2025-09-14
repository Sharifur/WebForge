<?php

namespace App\Services;

use Plugins\Pagebuilder\Helpers\FrontendRenderer;
use Plugins\Pagebuilder\Helpers\EditorRenderer;

/**
 * PageContentRenderer - Service for rendering page builder content
 * 
 * This service acts as a facade for the new renderer system, providing
 * backward compatibility while delegating to context-specific renderers.
 * 
 * @package App\Services
 * @since 1.0.0
 */
class PageContentRenderer
{
    /**
     * Frontend renderer instance
     * @var FrontendRenderer
     */
    private $frontendRenderer;

    /**
     * Editor renderer instance
     * @var EditorRenderer
     */
    private $editorRenderer;

    /**
     * Constructor - Initialize renderer instances
     */
    public function __construct()
    {
        $this->frontendRenderer = new FrontendRenderer();
        $this->editorRenderer = new EditorRenderer();
    }

    /**
     * Render page builder content structure to HTML
     * 
     * This method provides backward compatibility by using the FrontendRenderer
     * by default. For editor context, use renderForEditor() method.
     * 
     * @param mixed $pageContent Page content data (array or JSON string)
     * @return string Rendered HTML content
     */
    public function renderPageContent($pageContent): string
    {
        return $this->frontendRenderer->renderPageBuilderContent($pageContent);
    }

    /**
     * Render page content specifically for frontend display
     * 
     * Uses the FrontendRenderer optimized for public website display
     * with SEO-friendly markup and performance optimizations.
     * 
     * @param mixed $pageContent Page content data (array or JSON string)
     * @param array $config Optional renderer configuration
     * @return string Rendered HTML content optimized for frontend
     */
    public function renderForFrontend($pageContent, array $config = []): string
    {
        if (!empty($config)) {
            $renderer = new FrontendRenderer($config);
            return $renderer->renderPageBuilderContent($pageContent);
        }
        
        return $this->frontendRenderer->renderPageBuilderContent($pageContent);
    }

    /**
     * Render page content specifically for editor/preview mode
     * 
     * Uses the EditorRenderer with interactive editing controls,
     * visual feedback, and development features.
     * 
     * @param mixed $pageContent Page content data (array or JSON string)
     * @param array $config Optional renderer configuration
     * @return string Rendered HTML content optimized for editing
     */
    public function renderForEditor($pageContent, array $config = []): string
    {
        if (!empty($config)) {
            $renderer = new EditorRenderer($config);
            return $renderer->renderPageBuilderContent($pageContent);
        }
        
        return $this->editorRenderer->renderPageBuilderContent($pageContent);
    }

}