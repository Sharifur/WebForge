# Tab Field Documentation: Button Hover & Normal States

## 📋 **Overview**

This documentation explains how to use `$control->addTab()` to create sophisticated button styling with **Normal** and **Hover** states in the Style tab. The tab field system enables dynamic styling states management for interactive elements.

## 🎯 **Key Concepts**

### **Tab Field Architecture**
- **ControlManager**: `$control->addTab()` creates tabbed field groups
- **TabGroupField**: Advanced tab field with state management
- **Style States**: Normal, Hover, Active, Focus, Disabled
- **CSS Selectors**: Automatic state-based CSS generation

## 🛠️ **Basic Tab Implementation**

### **Method 1: ControlManager addTab()**
```php
public function getStyleFields(): array
{
    $control = new ControlManager();

    // Normal State Tab
    $control->addTab('normal', 'Normal State')
        ->registerField('text_color', FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#FFFFFF')
            ->setSelectors([
                '{{WRAPPER}} .button-element' => 'color: {{VALUE}};'
            ])
        )
        ->registerField('background_color', FieldManager::COLOR()
            ->setLabel('Background Color')
            ->setDefault('#3B82F6')
            ->setSelectors([
                '{{WRAPPER}} .button-element' => 'background-color: {{VALUE}};'
            ])
        )
        ->registerField('border_width', FieldManager::NUMBER()
            ->setLabel('Border Width')
            ->setUnit('px')
            ->setDefault(0)
            ->setSelectors([
                '{{WRAPPER}} .button-element' => 'border-width: {{VALUE}}{{UNIT}};'
            ])
        )
        ->endTab();

    // Hover State Tab
    $control->addTab('hover', 'Hover State')
        ->registerField('text_color_hover', FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#FFFFFF')
            ->setSelectors([
                '{{WRAPPER}} .button-element:hover' => 'color: {{VALUE}};'
            ])
        )
        ->registerField('background_color_hover', FieldManager::COLOR()
            ->setLabel('Background Color')
            ->setDefault('#2563EB')
            ->setSelectors([
                '{{WRAPPER}} .button-element:hover' => 'background-color: {{VALUE}};'
            ])
        )
        ->registerField('border_width_hover', FieldManager::NUMBER()
            ->setLabel('Border Width')
            ->setUnit('px')
            ->setDefault(2)
            ->setSelectors([
                '{{WRAPPER}} .button-element:hover' => 'border-width: {{VALUE}}{{UNIT}};'
            ])
        )
        ->endTab();

    return $control->getFields();
}
```

### **Method 2: TabGroupField (Advanced)**
```php
public function getStyleFields(): array
{
    $control = new ControlManager();

    // Style States Tab Group
    $control->registerField('button_styles', FieldManager::STYLE_STATES(['normal', 'hover'], [
        'text_color' => FieldManager::COLOR()
            ->setLabel('Text Color')
            ->setDefault('#FFFFFF'),
        'background_color' => FieldManager::COLOR()
            ->setLabel('Background Color')
            ->setDefault('#3B82F6'),
        'border_width' => FieldManager::NUMBER()
            ->setLabel('Border Width')
            ->setUnit('px')
            ->setDefault(0),
        'border_color' => FieldManager::COLOR()
            ->setLabel('Border Color')
            ->setDefault('#3B82F6'),
        'border_radius' => FieldManager::DIMENSION()
            ->setLabel('Border Radius')
            ->setUnits(['px', 'em', '%'])
            ->setDefault(['top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6])
    ]));

    return $control->getFields();
}
```

## 🎨 **Complete Button Widget with Tab Styling**

### **Enhanced Button Widget Example**
```php
<?php

namespace Plugins\Pagebuilder\Core\Widgets;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

class EnhancedButtonWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'enhanced_button';
    }

    protected function getWidgetName(): string
    {
        return 'Enhanced Button';
    }

    protected function getWidgetIcon(): string
    {
        return 'las la-hand-point-up';
    }

    protected function getWidgetDescription(): string
    {
        return 'Advanced button with normal and hover state styling';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::CORE;
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Click Me')
                ->setRequired(true)
            )
            ->registerField('url', FieldManager::ENHANCED_URL()
                ->setLabel('Button Link')
                ->setDefault('#')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        // Button States Tabs
        $control->addTab('normal', 'Normal State', ['icon' => 'MousePointer'])
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('background', FieldManager::BACKGROUND_GROUP()
                ->setLabel('Background')
                ->setAllowedTypes(['color', 'gradient'])
                ->setDefaultType('color')
                ->setDefaultBackground(['color' => '#3B82F6'])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'background: {{VALUE}};'
                ])
            )
            ->registerField('border', FieldManager::BORDER_SHADOW_GROUP()
                ->setLabel('Border & Shadow')
                ->setShowShadow(true)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'border: {{BORDER}}; box-shadow: {{SHADOW}};'
                ])
            )
            ->registerField('typography', FieldManager::TYPOGRAPHY_GROUP()
                ->setLabel('Typography')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => '{{TYPOGRAPHY}}'
                ])
            )
            ->endTab();

        $control->addTab('hover', 'Hover State', ['icon' => 'MousePointer2'])
            ->registerField('text_color_hover', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#FFFFFF')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'color: {{VALUE}};'
                ])
            )
            ->registerField('background_hover', FieldManager::BACKGROUND_GROUP()
                ->setLabel('Background')
                ->setAllowedTypes(['color', 'gradient'])
                ->setDefaultType('color')
                ->setDefaultBackground(['color' => '#2563EB'])
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'background: {{VALUE}};'
                ])
            )
            ->registerField('border_hover', FieldManager::BORDER_SHADOW_GROUP()
                ->setLabel('Border & Shadow')
                ->setShowShadow(true)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'border: {{BORDER}}; box-shadow: {{SHADOW}};'
                ])
            )
            ->registerField('transform_hover', FieldManager::SELECT()
                ->setLabel('Hover Transform')
                ->setOptions([
                    'none' => 'None',
                    'scale-105' => 'Scale Up (5%)',
                    'scale-110' => 'Scale Up (10%)',
                    'translateY(-2px)' => 'Lift Up',
                    'rotate(2deg)' => 'Slight Rotate'
                ])
                ->setDefault('scale-105')
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button:hover' => 'transform: {{VALUE}};'
                ])
            )
            ->endTab();

        // Spacing Group (outside tabs)
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->setLabel('Padding')
                ->setDefault(['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24])
                ->setUnits(['px', 'em', '%'])
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'padding: {{VALUE}};'
                ])
            )
            ->registerField('margin', FieldManager::DIMENSION()
                ->setLabel('Margin')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setUnits(['px', 'em', '%'])
                ->setResponsive(true)
                ->setSelectors([
                    '{{WRAPPER}} .enhanced-button' => 'margin: {{VALUE}};'
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];

        // Content
        $content = $general['content'] ?? [];
        $text = $this->sanitizeText($content['text'] ?? 'Click Me');
        $url = $this->sanitizeURL($content['url'] ?? '#');

        // Generate dynamic CSS from settings
        $css = $this->generateInlineStyles(['style' => $style]);
        $classes = $this->buildCssClasses($settings);

        // Build button HTML
        return "
        <div class=\"{$classes}\" style=\"{$css}\">
            <a href=\"{$url}\" class=\"enhanced-button\">
                {$text}
            </a>
        </div>";
    }
}
```

## 🔧 **Advanced Tab Field Configurations**

### **1. Multiple State Tabs**
```php
// Complete button states
$control->addTab('normal', 'Normal', ['icon' => 'MousePointer'])
    // ... normal state fields
    ->endTab();

$control->addTab('hover', 'Hover', ['icon' => 'MousePointer2'])
    // ... hover state fields
    ->endTab();

$control->addTab('active', 'Active', ['icon' => 'Hand'])
    // ... active state fields
    ->endTab();

$control->addTab('focus', 'Focus', ['icon' => 'Target'])
    // ... focus state fields
    ->endTab();

$control->addTab('disabled', 'Disabled', ['icon' => 'Ban'])
    // ... disabled state fields
    ->endTab();
```

### **2. Responsive Tabs with States**
```php
// Responsive + State combination
$control->registerField('responsive_button_styles', FieldManager::RESPONSIVE_GROUP([
    'desktop' => FieldManager::STYLE_STATES(['normal', 'hover'], [
        'font_size' => FieldManager::NUMBER()->setLabel('Font Size')->setUnit('px')->setDefault(16),
        'padding' => FieldManager::DIMENSION()->setLabel('Padding')
    ]),
    'tablet' => FieldManager::STYLE_STATES(['normal', 'hover'], [
        'font_size' => FieldManager::NUMBER()->setLabel('Font Size')->setUnit('px')->setDefault(14),
        'padding' => FieldManager::DIMENSION()->setLabel('Padding')
    ]),
    'mobile' => FieldManager::STYLE_STATES(['normal', 'hover'], [
        'font_size' => FieldManager::NUMBER()->setLabel('Font Size')->setUnit('px')->setDefault(12),
        'padding' => FieldManager::DIMENSION()->setLabel('Padding')
    ])
]));
```

### **3. Custom Tab Groups**
```php
// Custom tab group with specific configuration
$control->registerField('button_appearance', FieldManager::CUSTOM_TABS([
    'design' => [
        'label' => 'Design',
        'icon' => 'Palette',
        'fields' => [
            'style' => FieldManager::SELECT()->setOptions(['solid', 'outline', 'ghost']),
            'shape' => FieldManager::SELECT()->setOptions(['square', 'rounded', 'pill'])
        ]
    ],
    'animation' => [
        'label' => 'Animation',
        'icon' => 'Zap',
        'fields' => [
            'transition' => FieldManager::SELECT()->setOptions(['none', 'fade', 'scale', 'slide']),
            'duration' => FieldManager::NUMBER()->setUnit('ms')->setDefault(200)
        ]
    ]
]));
```

## 🎯 **CSS Selector Best Practices**

### **State-Specific Selectors**
```php
// Normal state
'{{WRAPPER}} .button-element' => 'property: {{VALUE}};'

// Hover state
'{{WRAPPER}} .button-element:hover' => 'property: {{VALUE}};'

// Active state
'{{WRAPPER}} .button-element:active' => 'property: {{VALUE}};'

// Focus state
'{{WRAPPER}} .button-element:focus' => 'property: {{VALUE}};'

// Disabled state
'{{WRAPPER}} .button-element:disabled' => 'property: {{VALUE}};'
'{{WRAPPER}} .button-element.disabled' => 'property: {{VALUE}};'

// Complex selectors
'{{WRAPPER}} .button-element::before' => 'content: ""; property: {{VALUE}};'
'{{WRAPPER}} .button-element:hover::after' => 'property: {{VALUE}};'
```

### **Responsive + State Combinations**
```php
// Desktop hover
'{{WRAPPER}} .button-element:hover' => 'transform: {{VALUE}};'

// Tablet hover (less transform)
'@media (max-width: 1024px) { {{WRAPPER}} .button-element:hover }' => 'transform: scale(1.02);'

// Mobile (no hover effects)
'@media (max-width: 768px) { {{WRAPPER}} .button-element:hover }' => 'transform: none;'
```

## 📚 **Field Groups in Tabs**

### **Organizing Fields with Groups Inside Tabs**
```php
$control->addTab('normal', 'Normal State')
    // Text Group
    ->addGroup('text', 'Text Styling')
        ->registerField('text_color', FieldManager::COLOR()->setLabel('Text Color'))
        ->registerField('typography', FieldManager::TYPOGRAPHY_GROUP()->setLabel('Typography'))
        ->endGroup()

    // Background Group
    ->addGroup('background', 'Background')
        ->registerField('background', FieldManager::BACKGROUND_GROUP()->setLabel('Background'))
        ->endGroup()

    // Border Group
    ->addGroup('border', 'Border & Effects')
        ->registerField('border', FieldManager::BORDER_SHADOW_GROUP()->setLabel('Border & Shadow'))
        ->endGroup()
    ->endTab();
```

## 🔄 **Data Structure Examples**

### **Settings Structure for Tabs**
```json
{
  "style": {
    "normal": {
      "text_color": "#FFFFFF",
      "background_color": "#3B82F6",
      "border_width": 1,
      "border_color": "#2563EB"
    },
    "hover": {
      "text_color": "#FFFFFF",
      "background_color": "#2563EB",
      "border_width": 2,
      "border_color": "#1D4ED8"
    }
  }
}
```

### **Accessing Tab Data in Templates**
```php
// In widget render method
public function render(array $settings = []): string
{
    $style = $settings['style'] ?? [];

    // Normal state settings
    $normalState = $style['normal'] ?? [];
    $normalTextColor = $normalState['text_color'] ?? '#000000';
    $normalBgColor = $normalState['background_color'] ?? '#FFFFFF';

    // Hover state settings
    $hoverState = $style['hover'] ?? [];
    $hoverTextColor = $hoverState['text_color'] ?? $normalTextColor;
    $hoverBgColor = $hoverState['background_color'] ?? $normalBgColor;

    // Generate CSS
    $css = "
    .button-element {
        color: {$normalTextColor};
        background-color: {$normalBgColor};
        transition: all 0.2s ease;
    }
    .button-element:hover {
        color: {$hoverTextColor};
        background-color: {$hoverBgColor};
    }";

    return "<style>{$css}</style><button class=\"button-element\">Button</button>";
}
```

## 🚀 **Advanced Features**

### **1. State Copy Functionality**
```php
// Enable copying between states
FieldManager::STYLE_STATES(['normal', 'hover'])
    ->allowStateCopy(true) // Users can copy normal → hover
```

### **2. Conditional Fields in Tabs**
```php
$control->addTab('hover', 'Hover State')
    ->registerField('enable_hover', FieldManager::TOGGLE()
        ->setLabel('Enable Hover Effects')
        ->setDefault(true)
    )
    ->registerField('hover_animation', FieldManager::SELECT()
        ->setLabel('Hover Animation')
        ->setCondition(['enable_hover' => true]) // Only show if hover enabled
        ->setOptions(['scale', 'rotate', 'slide'])
    )
    ->endTab();
```

### **3. Dynamic CSS Generation**
```php
// Automatic CSS generation from tab fields
public function generateCSS(string $widgetId, array $settings): string
{
    $css = parent::generateCSS($widgetId, $settings);

    // Add transition for smooth state changes
    $css .= "
    #{$widgetId} .button-element {
        transition: all 0.3s ease-in-out;
    }";

    return $css;
}
```

## 📖 **Blade Template Integration**

### **Using Tab Data in Blade Templates**
```blade
{{-- resources/views/widgets/enhanced_button.blade.php --}}
<div class="{{ $css_classes }}" style="{{ $inline_styles }}">
    <button class="enhanced-button"
            style="
                color: {{ $helper->styleSettings('normal.text_color', '#000000') }};
                background: {{ $helper->styleSettings('normal.background_color', '#ffffff') }};
            "
            onmouseover="
                this.style.color = '{{ $helper->styleSettings('hover.text_color', $helper->styleSettings('normal.text_color')) }}';
                this.style.background = '{{ $helper->styleSettings('hover.background_color', $helper->styleSettings('normal.background_color')) }}';
            "
            onmouseout="
                this.style.color = '{{ $helper->styleSettings('normal.text_color') }}';
                this.style.background = '{{ $helper->styleSettings('normal.background_color') }}';
            ">
        {{ $helper->generalSettings('content.text', 'Click Me') }}
    </button>
</div>
```

## 🎯 **Summary**

The tab field system provides powerful state management for interactive widgets:

- **`$control->addTab()`**: Simple tab creation with ControlManager
- **`FieldManager::STYLE_STATES()`**: Pre-configured state tabs
- **CSS Selectors**: Automatic state-based CSS generation
- **Data Structure**: Organized settings by state
- **Template Integration**: Easy access to state-specific values

This enables creation of sophisticated button widgets with professional hover effects and state management.