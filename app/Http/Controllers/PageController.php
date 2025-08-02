<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\PageContentRenderer;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected $pageContentRenderer;

    public function __construct(PageContentRenderer $pageContentRenderer)
    {
        $this->pageContentRenderer = $pageContentRenderer;
    }

    public function show(Page $page)
    {
        // Check if page is published
        if ($page->status !== 'published') {
            abort(404);
        }

        // Render page builder content if page builder is enabled
        $renderedContent = '';
        if ($page->use_page_builder && $page->content) {
            $renderedContent = $this->pageContentRenderer->renderPageContent($page->content);
        }

        return view('frontend.page', compact('page', 'renderedContent'));
    }
}