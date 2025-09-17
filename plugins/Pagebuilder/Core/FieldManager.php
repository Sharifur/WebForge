<?php

namespace Plugins\Pagebuilder\Core;

use Plugins\Pagebuilder\Core\Fields\TextField;
use Plugins\Pagebuilder\Core\Fields\TextareaField;
use Plugins\Pagebuilder\Core\Fields\NumberField;
use Plugins\Pagebuilder\Core\Fields\SelectField;
use Plugins\Pagebuilder\Core\Fields\MultiSelectField;
use Plugins\Pagebuilder\Core\Fields\ToggleField;
use Plugins\Pagebuilder\Core\Fields\ColorField;
use Plugins\Pagebuilder\Core\Fields\IconField;
use Plugins\Pagebuilder\Core\Fields\IconInputField;
use Plugins\Pagebuilder\Core\Fields\ImageField;
use Plugins\Pagebuilder\Core\Fields\UrlField;
use Plugins\Pagebuilder\Core\Fields\EmailField;
use Plugins\Pagebuilder\Core\Fields\PasswordField;
use Plugins\Pagebuilder\Core\Fields\RangeField;
use Plugins\Pagebuilder\Core\Fields\RadioField;
use Plugins\Pagebuilder\Core\Fields\CheckboxField;
use Plugins\Pagebuilder\Core\Fields\DateField;
use Plugins\Pagebuilder\Core\Fields\TimeField;
use Plugins\Pagebuilder\Core\Fields\DateTimeField;
use Plugins\Pagebuilder\Core\Fields\RepeaterField;
use Plugins\Pagebuilder\Core\Fields\GroupField;
use Plugins\Pagebuilder\Core\Fields\HeadingField;
use Plugins\Pagebuilder\Core\Fields\CodeField;
use Plugins\Pagebuilder\Core\Fields\WysiwygField;
use Plugins\Pagebuilder\Core\Fields\DimensionField;
use Plugins\Pagebuilder\Core\Fields\GradientField;
use Plugins\Pagebuilder\Core\Fields\AlignmentField;
use Plugins\Pagebuilder\Core\Fields\BackgroundField;
use Plugins\Pagebuilder\Core\Fields\TypographyField;
use Plugins\Pagebuilder\Core\Fields\BorderShadowField;
use Plugins\Pagebuilder\Core\FieldTypes\TabGroupField;
use Plugins\Pagebuilder\Core\FieldTypes\LinkField;
use Plugins\Pagebuilder\Core\FieldTypes\DividerField as VisualDividerField;

/**
 * FieldManager - Static factory class for creating field instances
 * 
 * Provides a fluent API for creating all supported field types with IDE autocompletion
 * and type safety. Each method returns a configured field instance that can be further
 * customized using chainable methods.
 * 
 * @package Plugins\Pagebuilder\Core
 */
class FieldManager
{
    /**
     * Create a text input field
     *
     * @return TextField
     */
    public static function TEXT(): TextField
    {
        return new TextField();
    }

    /**
     * Create a textarea field
     *
     * @return TextareaField
     */
    public static function TEXTAREA(): TextareaField
    {
        return new TextareaField();
    }

    /**
     * Create a number input field
     *
     * @return NumberField
     */
    public static function NUMBER(): NumberField
    {
        return new NumberField();
    }

    /**
     * Create a select dropdown field
     *
     * @return SelectField
     */
    public static function SELECT(): SelectField
    {
        return new SelectField();
    }

    /**
     * Create a multi-select field
     *
     * @return MultiSelectField
     */
    public static function MULTISELECT(): MultiSelectField
    {
        return new MultiSelectField();
    }

    /**
     * Create a toggle/switch field
     *
     * @return ToggleField
     */
    public static function TOGGLE(): ToggleField
    {
        return new ToggleField();
    }

    /**
     * Create a color picker field
     *
     * @return ColorField
     */
    public static function COLOR(): ColorField
    {
        return new ColorField();
    }

    /**
     * Create an icon picker field
     *
     * @return IconField
     */
    public static function ICON(): IconField
    {
        return new IconField();
    }

    /**
     * Create a visual icon input field with modal selector
     *
     * @return IconInputField
     */
    public static function ICON_INPUT(): IconInputField
    {
        return IconInputField::create();
    }

    /**
     * Create an image upload/picker field
     *
     * @return ImageField
     */
    public static function IMAGE(): ImageField
    {
        return new ImageField();
    }

    /**
     * Create a URL input field
     *
     * @return UrlField
     */
    public static function URL(): UrlField
    {
        return new UrlField();
    }

    /**
     * Create an enhanced URL field with all link options
     * This combines URL input, target, rel, accessibility, and tracking options
     *
     * @return UrlField
     */
    public static function ENHANCED_URL(): UrlField
    {
        return (new UrlField())
            ->setShowTargetOptions(true)
            ->setShowRelOptions(true)
            ->setEnableAccessibility(true);
    }

    /**
     * Create a web link field (external links)
     *
     * @return UrlField
     */
    public static function WEB_LINK(): UrlField
    {
        return (new UrlField())->asWebLink();
    }

    /**
     * Create an email link field
     *
     * @return UrlField
     */
    public static function EMAIL_LINK(): UrlField
    {
        return (new UrlField())->asEmailLink();
    }

    /**
     * Create a phone link field
     *
     * @return UrlField
     */
    public static function PHONE_LINK(): UrlField
    {
        return (new UrlField())->asPhoneLink();
    }

    /**
     * Create a download link field
     *
     * @return UrlField
     */
    public static function DOWNLOAD_LINK(): UrlField
    {
        return (new UrlField())->asDownloadLink();
    }

    /**
     * Create an internal navigation link field
     *
     * @return UrlField
     */
    public static function INTERNAL_LINK(): UrlField
    {
        return (new UrlField())->asInternalLink();
    }

    /**
     * Create an email input field
     *
     * @return EmailField
     */
    public static function EMAIL(): EmailField
    {
        return new EmailField();
    }

    /**
     * Create a password input field
     *
     * @return PasswordField
     */
    public static function PASSWORD(): PasswordField
    {
        return new PasswordField();
    }

    /**
     * Create a range slider field
     *
     * @return RangeField
     */
    public static function RANGE(): RangeField
    {
        return new RangeField();
    }

    /**
     * Create a radio buttons field
     *
     * @return RadioField
     */
    public static function RADIO(): RadioField
    {
        return new RadioField();
    }

    /**
     * Create a checkbox field
     *
     * @return CheckboxField
     */
    public static function CHECKBOX(): CheckboxField
    {
        return new CheckboxField();
    }

    /**
     * Create a date picker field
     *
     * @return DateField
     */
    public static function DATE(): DateField
    {
        return new DateField();
    }

    /**
     * Create a time picker field
     *
     * @return TimeField
     */
    public static function TIME(): TimeField
    {
        return new TimeField();
    }

    /**
     * Create a datetime picker field
     *
     * @return DateTimeField
     */
    public static function DATETIME(): DateTimeField
    {
        return new DateTimeField();
    }

    /**
     * Create a repeater field for dynamic content
     *
     * @return RepeaterField
     */
    public static function REPEATER(): RepeaterField
    {
        return new RepeaterField();
    }

    /**
     * Create a group container for organizing fields
     *
     * @return GroupField
     */
    public static function GROUP(): GroupField
    {
        return new GroupField();
    }


    /**
     * Create a heading for field sections
     *
     * @return HeadingField
     */
    public static function HEADING(): HeadingField
    {
        return new HeadingField();
    }

    /**
     * Create a code editor field
     *
     * @return CodeField
     */
    public static function CODE(): CodeField
    {
        return new CodeField();
    }

    /**
     * Create a WYSIWYG editor field
     *
     * @return WysiwygField
     */
    public static function WYSIWYG(): WysiwygField
    {
        return new WysiwygField();
    }

    /**
     * Create a dimension field for spacing, padding, margin etc.
     *
     * @return DimensionField
     */
    public static function DIMENSION(): DimensionField
    {
        return new DimensionField();
    }

    /**
     * Create a gradient picker field with visual preview
     *
     * @return GradientField
     */
    public static function GRADIENT(): GradientField
    {
        return new GradientField();
    }

    /**
     * Create an alignment field with icon-based controls
     *
     * @return AlignmentField
     */
    public static function ALIGNMENT(): AlignmentField
    {
        return new AlignmentField();
    }

    /**
     * Create a unified background field for colors, gradients, and images
     *
     * @return BackgroundField
     */
    public static function BACKGROUND_GROUP(): BackgroundField
    {
        return new BackgroundField();
    }

    /**
     * Create a unified typography field for font settings
     *
     * @return TypographyField
     */
    public static function TYPOGRAPHY_GROUP(): TypographyField
    {
        return new TypographyField();
    }

    /**
     * Create a unified border and shadow field with visual controls
     *
     * @return BorderShadowField
     */
    public static function BORDER_SHADOW_GROUP(): BorderShadowField
    {
        return new BorderShadowField();
    }

    /**
     * Create a tab group for organizing fields into tabs
     *
     * @param array $tabs Array of tab configurations
     * @return TabGroupField
     */
    public static function TAB_GROUP(array $tabs = []): TabGroupField
    {
        return new TabGroupField($tabs);
    }

    /**
     * Create a style states tab group (normal, hover, active, focus)
     *
     * @param array $states States to include (default: ['normal', 'hover'])
     * @param array $fields Fields to include in each state
     * @return TabGroupField
     */
    public static function STYLE_STATES(array $states = ['normal', 'hover'], array $fields = []): TabGroupField
    {
        return TabGroupField::styleStates($states, $fields);
    }

    /**
     * Create a responsive tab group (desktop, tablet, mobile)
     *
     * @param array $fields Fields to include in each breakpoint
     * @return TabGroupField
     */
    public static function RESPONSIVE_GROUP(array $fields = []): TabGroupField
    {
        return TabGroupField::responsive($fields);
    }

    /**
     * Create a custom tab group with user-defined tab names and labels
     *
     * Usage examples:
     * 
     * // Simple format - auto-generates labels from keys
     * CUSTOM_TABS(['design' => [...], 'content' => [...]])
     * 
     * // Detailed format - specify labels and icons
     * CUSTOM_TABS([
     *     'primary' => ['label' => 'Primary Design', 'icon' => 'Palette', 'fields' => [...]],
     *     'secondary' => ['label' => 'Secondary Design', 'icon' => 'Brush', 'fields' => [...]]
     * ])
     *
     * @param array $tabs Custom tab configuration
     * @return TabGroupField
     */
    public static function CUSTOM_TABS(array $tabs = []): TabGroupField
    {
        return new TabGroupField($tabs);
    }

    /**
     * Create a comprehensive link field with advanced options
     *
     * Features:
     * - Smart link type detection (internal, external, email, phone, file)
     * - Advanced target and behavior options
     * - SEO controls (rel attributes, title, etc.)
     * - Custom HTML attributes manager
     * - UTM parameter builder
     * - Link validation and testing
     * - Responsive behavior settings
     *
     * Usage examples:
     * 
     * // Basic link field
     * LINK_GROUP()
     * 
     * // Advanced link field with all features
     * LINK_GROUP()
     *     ->enableAdvancedOptions(true)
     *     ->enableSEOControls(true)
     *     ->enableUTMTracking(true)
     *     ->setLinkTypes(['internal', 'external', 'email'])
     *     ->setDefaultTarget('_blank')
     *
     * @return LinkField
     */
    public static function LINK_GROUP(): LinkField
    {
        return new LinkField();
    }

    /**
     * Create a visual divider/separator field
     *
     * Features:
     * - Customizable colors and styles (solid, dashed, dotted, double)
     * - Optional text labels with positioning
     * - Adjustable thickness and spacing
     * - Multiple pre-configured variants
     *
     * Usage examples:
     * 
     * // Basic divider
     * DIVIDER()
     * 
     * // Divider with custom styling
     * DIVIDER()
     *     ->setColor('#e2e8f0')
     *     ->setStyle('dashed')
     *     ->setThickness(2)
     *     ->setMargin(['top' => 20, 'bottom' => 20])
     * 
     * // Section divider with text
     * DIVIDER()
     *     ->setText('Advanced Options')
     *     ->setTextPosition('center')
     *     ->setTextSize('base')
     * 
     * // Pre-configured variants
     * DIVIDER()->thick()     // Thick divider (3px)
     * DIVIDER()->dashed()    // Dashed style
     * DIVIDER()->dotted()    // Dotted style
     * DIVIDER()->section('Section Name') // With text label
     * DIVIDER()->spacer(30)  // Invisible spacer (30px height)
     *
     * @return VisualDividerField
     */
    public static function DIVIDER(): VisualDividerField
    {
        return new VisualDividerField();
    }
}