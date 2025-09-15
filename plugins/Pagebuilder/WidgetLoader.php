<?php

namespace Plugins\Pagebuilder;

use Plugins\Pagebuilder\Core\WidgetRegistry;
use Plugins\Pagebuilder\Widgets\Theme\HeaderWidget;

/**
 * WidgetLoader - Centralized widget registration for the page builder system
 *
 * This class handles the registration of all custom widgets to ensure they appear
 * in the page builder sidebar. The WidgetRegistry has auto-discovery, but this
 * loader ensures explicit registration for better control and debugging.
 */
class WidgetLoader
{
    /**
     * Register all available widgets
     */
    public static function registerAllWidgets(): void
    {
        // Basic Widgets (Category: BASIC)
        self::registerBasicWidgets();

        // Layout Widgets (Category: LAYOUT)
        self::registerLayoutWidgets();

        // Media Widgets (Category: MEDIA)
        self::registerMediaWidgets();

        // Interactive Widgets (Category: INTERACTIVE)
        self::registerInteractiveWidgets();

        // Content Widgets (Category: CONTENT)
        self::registerContentWidgets();

        // Advanced Widgets (Category: ADVANCED)
        self::registerAdvancedWidgets();

        // Form Widgets (Category: FORM)
        self::registerFormWidgets();

        // Cache the registry for better performance
        WidgetRegistry::cache();
    }

    /**
     * Register basic widgets
     */
    private static function registerBasicWidgets(): void
    {
        $basicWidgets = [
            \Plugins\Pagebuilder\Widgets\Basic\HeadingWidget::class,
            \Plugins\Pagebuilder\Widgets\Basic\ParagraphWidget::class,
            \Plugins\Pagebuilder\Widgets\Basic\ListWidget::class,
            \Plugins\Pagebuilder\Widgets\Basic\LinkWidget::class,
            \Plugins\Pagebuilder\Widgets\Basic\ButtonWidget::class,
            HeaderWidget::class,
        ];

        WidgetRegistry::registerMultiple($basicWidgets);
    }

    /**
     * Register layout widgets
     */
    private static function registerLayoutWidgets(): void
    {
        $layoutWidgets = [
            \Plugins\Pagebuilder\Widgets\Layout\SectionWidget::class,
            \Plugins\Pagebuilder\Widgets\Layout\DividerWidget::class,
            \Plugins\Pagebuilder\Widgets\Layout\SpacerWidget::class,
            \Plugins\Pagebuilder\Widgets\Layout\GridWidget::class,
        ];

        WidgetRegistry::registerMultiple($layoutWidgets);
    }

    /**
     * Register media widgets
     */
    private static function registerMediaWidgets(): void
    {
        $mediaWidgets = [
            \Plugins\Pagebuilder\Widgets\Media\ImageWidget::class,
            \Plugins\Pagebuilder\Widgets\Media\VideoWidget::class,
            \Plugins\Pagebuilder\Widgets\Media\IconWidget::class,
            \Plugins\Pagebuilder\Widgets\Media\ImageGalleryWidget::class,
        ];

        WidgetRegistry::registerMultiple($mediaWidgets);
    }

    /**
     * Register interactive widgets
     */
    private static function registerInteractiveWidgets(): void
    {
        $interactiveWidgets = [
            \Plugins\Pagebuilder\Widgets\Interactive\TabsWidget::class,
        ];

        WidgetRegistry::registerMultiple($interactiveWidgets);
    }

    /**
     * Register content widgets
     */
    private static function registerContentWidgets(): void
    {
        $contentWidgets = [
            \Plugins\Pagebuilder\Widgets\Content\TestimonialWidget::class,
        ];

        WidgetRegistry::registerMultiple($contentWidgets);
    }

    /**
     * Register advanced widgets
     */
    private static function registerAdvancedWidgets(): void
    {
        $advancedWidgets = [
            \Plugins\Pagebuilder\Widgets\Advanced\CodeWidget::class,
        ];

        WidgetRegistry::registerMultiple($advancedWidgets);
    }

    /**
     * Register form widgets
     */
    private static function registerFormWidgets(): void
    {
        $formWidgets = [
            \Plugins\Pagebuilder\Widgets\Form\ContactFormWidget::class,
        ];

        WidgetRegistry::registerMultiple($formWidgets);
    }

    /**
     * Get all registered widgets for API/sidebar display
     */
    public static function getWidgetsForSidebar(): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::getWidgetsForApi();
    }

    /**
     * Get widgets grouped by category for sidebar
     */
    public static function getWidgetsGroupedForSidebar(): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::getWidgetsGroupedByCategory();
    }

    /**
     * Get widget statistics
     */
    public static function getWidgetStats(): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::getStats();
    }

    /**
     * Get categories with widget counts
     */
    public static function getCategoriesWithCounts(): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::getCategoriesWithCounts();
    }

    /**
     * Initialize the widget system
     * Call this method in your application bootstrap or service provider
     */
    public static function init(): void
    {
        // Try to load from cache first
        if (!WidgetRegistry::loadFromCache()) {
            // If cache is empty, register all widgets
            self::registerAllWidgets();
        }
    }

    /**
     * Force refresh all widgets (clears cache and re-registers)
     */
    public static function refresh(): void
    {
        WidgetRegistry::clearCache();
        WidgetRegistry::clear();
        self::registerAllWidgets();
    }

    /**
     * Get widget by type for rendering
     */
    public static function getWidget(string $type): ?\Plugins\Pagebuilder\Core\BaseWidget
    {
        self::registerAllWidgets();
        return WidgetRegistry::getWidget($type);
    }

    /**
     * Check if a widget type exists
     */
    public static function widgetExists(string $type): bool
    {
        self::registerAllWidgets();
        return WidgetRegistry::widgetExists($type);
    }

    /**
     * Search widgets
     */
    public static function searchWidgets(string $query, array $filters = []): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::searchWidgets($query, $filters);
    }

    /**
     * Get widget fields for the editor
     */
    public static function getWidgetFields(string $type, string $tab = null): ?array
    {
        self::registerAllWidgets();
        return WidgetRegistry::getWidgetFields($type, $tab);
    }

    /**
     * Validate widget settings
     */
    public static function validateWidgetSettings(string $type, array $settings): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::validateWidgetSettings($type, $settings);
    }

    /**
     * Get popular widgets for quick access
     */
    public static function getPopularWidgets(int $limit = 6): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::getPopularWidgets($limit);
    }

    /**
     * Get recently added widgets
     */
    public static function getRecentWidgets(int $limit = 6): array
    {
        self::registerAllWidgets();
        return WidgetRegistry::getRecentWidgets($limit);
    }
}
