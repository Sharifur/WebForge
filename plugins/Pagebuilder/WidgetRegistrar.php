<?php

namespace Plugins\Pagebuilder;

use Plugins\Pagebuilder\Core\WidgetLoader;

/**
 * WidgetRegistrar - Ultra-Simple API for widget registration
 *
 * This provides the simplest possible interface for registering widgets
 * and widget categories. Just pass arrays and everything is handled automatically.
 *
 * ## Usage Examples:
 *
 * ```php
 * // Register widgets
 * WidgetRegistrar::register([
 *     Widget1::class,
 *     Widget2::class,
 *     Widget3::class
 * ]);
 *
 * // Register categories
 * WidgetRegistrar::registerCategory([
 *     ['slug' => 'ecommerce', 'name' => 'E-commerce', 'icon' => 'las la-shopping-cart'],
 *     ['slug' => 'marketing', 'name' => 'Marketing', 'icon' => 'las la-bullhorn']
 * ]);
 * ```
 *
 * @author Page Builder Team
 * @since 1.0.0
 */
class WidgetRegistrar
{
    /**
     * Register widgets
     *
     * Takes an array of widget classes and registers them all automatically.
     * All complex logic is handled by the core system.
     *
     * @param array $widgets Array of fully qualified widget class names
     *
     * @example
     * ```php
     * WidgetRegistrar::register([
     *     ProductWidget::class,
     *     CartWidget::class,
     *     CheckoutWidget::class
     * ]);
     * ```
     */
    public static function register(): void
    {
        WidgetLoader::registerWidgets([

        ]);
    }

    /**
     * Register categories
     *
     * Takes an array of category definitions and registers them all automatically.
     * All complex logic is handled by the core system.
     *
     * @param array $categories Array of category definitions
     *
     * @example
     * ```php
     * WidgetRegistrar::registerCategory([
     *     ['slug' => 'ecommerce', 'name' => 'E-commerce', 'icon' => 'las la-shopping-cart'],
     *     ['slug' => 'marketing', 'name' => 'Marketing', 'icon' => 'las la-bullhorn', 'sortOrder' => 50]
     * ]);
     * ```
     */
    public static function registerCategory(): void
    {
        WidgetLoader::registerCategories([

        ]);
    }
}
