# Enhanced URL Field System - Developer Documentation

## Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Field Types & Usage](#field-types--usage)
4. [URL Rendering Methods](#url-rendering-methods)
5. [Custom Attributes](#custom-attributes)
6. [Security Features](#security-features)
7. [Accessibility Features](#accessibility-features)
8. [Migration Guide](#migration-guide)
9. [API Reference](#api-reference)
10. [Real-World Examples](#real-world-examples)

---

## Overview

The Enhanced URL Field System provides a comprehensive solution for handling URLs in pagebuilder widgets. It replaces the need for manual field groups and complex URL handling code with a single, feature-rich field that includes validation, security, accessibility, and custom attributes.

### Key Components

- **URLHandler** (`App\Utils\URLHandler`) - Core utility class for URL processing and rendering
- **Enhanced UrlField** (`Plugins\Pagebuilder\Core\Fields\UrlField`) - Advanced URL field with sub-fields
- **FieldManager Integration** - Factory methods for different URL types
- **XSS Protection Integration** - Built-in security validation using existing XSSProtection class

### Benefits Over Manual Implementation

| Manual Approach | Enhanced URL Field |
|------------------|-------------------|
| 5-10 separate fields | 1 comprehensive field |
| 50+ lines of rendering code | 1 line rendering call |
| Manual security validation | Built-in XSS protection |
| No accessibility features | Automatic ARIA enhancements |
| Inconsistent implementations | Standardized across widgets |

---

## Architecture

### System Design

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Widget        │───▶│  Enhanced        │───▶│  URLHandler     │
│   Definition    │    │  UrlField        │    │  Rendering      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │  XSSProtection   │
                       │  Security Layer  │
                       └──────────────────┘
```

### Class Hierarchy

```php
BaseField
└── UrlField (enhanced_url)
    ├── Sub-fields: url, target, rel, download, etc.
    ├── Preset configurations: asWebLink(), asEmailLink(), etc.
    └── Integration with URLHandler for rendering
```

---

## Field Types & Usage

### 1. Basic URL Field

For simple URL inputs without additional options:

```php
->registerField('website_url', FieldManager::URL()
    ->setLabel('Website URL')
    ->setPlaceholder('https://example.com')
    ->setValidateUrl(true)
)
```

**Generates:**
- Single URL input field
- Basic validation
- XSS protection

### 2. Enhanced URL Field

For comprehensive URL configuration with all options:

```php
->registerField('link_config', FieldManager::ENHANCED_URL()
    ->setLabel('Link Configuration')
    ->setShowTargetOptions(true)
    ->setShowRelOptions(true)
    ->setEnableAccessibility(true)
    ->setDescription('Complete URL setup with target, rel, and accessibility options')
)
```

**Generates:**
- URL input field
- Target dropdown (_self, _blank, _parent, _top)
- Rel attributes multiselect (nofollow, sponsored, ugc, etc.)
- ARIA label and title fields
- Custom attributes repeater

### 3. Preset Field Types

#### Web Link (External URLs)
```php
->registerField('external_link', FieldManager::WEB_LINK()
    ->setLabel('External Website')
    ->setDescription('Link to external website')
)
```

**Configuration:**
- Allowed schemes: http, https
- Shows target options
- Shows rel options
- Context: 'web'

#### Email Link
```php
->registerField('contact_email', FieldManager::EMAIL_LINK()
    ->setLabel('Contact Email')
    ->setPlaceholder('mailto:contact@example.com')
)
```

**Configuration:**
- Allowed schemes: mailto
- No target options
- No rel options
- Context: 'email'

#### Phone Link
```php
->registerField('phone_number', FieldManager::PHONE_LINK()
    ->setLabel('Phone Number')
    ->setPlaceholder('tel:+1234567890')
)
```

**Configuration:**
- Allowed schemes: tel, sms
- No target options
- No rel options
- Context: 'phone'

#### Download Link
```php
->registerField('file_download', FieldManager::DOWNLOAD_LINK()
    ->setLabel('File Download')
    ->setDescription('Link to downloadable file')
)
```

**Configuration:**
- Allowed schemes: http, https
- Shows download options
- Default target: _blank
- Context: 'download'

#### Internal Link
```php
->registerField('internal_page', FieldManager::INTERNAL_LINK()
    ->setLabel('Internal Page')
    ->setDescription('Link to internal page or section')
)
```

**Configuration:**
- Allows relative URLs
- Allows anchor links
- No target options (always _self)
- Context: 'internal'

### 4. Custom Configuration

Create custom URL field configurations:

```php
->registerField('social_link', FieldManager::URL()
    ->setLabel('Social Media Link')
    ->setAllowedSchemes(['http', 'https'])
    ->setShowTargetOptions(true)
    ->setDefaultTarget('_blank')
    ->setDefaultRel(['nofollow', 'noopener'])
    ->setEnableTracking(true)
    ->setContext('social')
)
```

---

## URL Rendering Methods

### 1. Automatic Link Rendering

**Primary Method - Handles All Features:**

```php
public function render(array $settings = []): string
{
    $urlSettings = $settings['general']['link_config'] ?? [];
    $linkText = $settings['general']['link_text'] ?? 'Click here';
    
    // One line renders complete, secure, accessible link
    return URLHandler::renderLink($urlSettings, $linkText);
}
```

**Generated Output Example:**
```html
<a href="https://example.com" 
   target="_blank" 
   rel="nofollow noopener"
   aria-label="Visit Example Site (opens in new window)"
   data-custom="value">
   Click here (external link)
</a>
```

### 2. Button-Style Links

For rendering links as buttons:

```php
public function render(array $settings = []): string
{
    $urlSettings = $settings['general']['button_url'] ?? [];
    $buttonText = $settings['general']['button_text'] ?? 'Button';
    
    $options = [
        'button_class' => 'btn btn-primary',
        'add_wrapper' => true,
        'wrapper_class' => 'button-container'
    ];
    
    return URLHandler::renderButton($urlSettings, $buttonText, $options);
}
```

**Generated Output:**
```html
<div class="button-container">
    <a href="https://example.com" 
       class="btn btn-primary" 
       target="_blank">
       Click Me
    </a>
</div>
```

### 3. Multiple Links

For rendering arrays of links:

```php
public function render(array $settings = []): string
{
    $socialLinks = $settings['general']['social_links'] ?? [];
    $platforms = ['Facebook', 'Twitter', 'Instagram'];
    
    $options = [
        'wrapper_tag' => 'div',
        'wrapper_class' => 'social-links',
        'item_wrapper' => 'span',
        'item_class' => 'social-link',
        'separator' => ' | '
    ];
    
    return URLHandler::renderLinks($socialLinks, $platforms, $options);
}
```

**Generated Output:**
```html
<div class="social-links">
    <span class="social-link">
        <a href="https://facebook.com/..." target="_blank">Facebook (facebook)</a>
    </span> | 
    <span class="social-link">
        <a href="https://twitter.com/..." target="_blank">Twitter (twitter)</a>
    </span>
</div>
```

### 4. Quick Simple Links

For simple, one-off links:

```php
// Quick link without field configuration
$quickLink = URLHandler::quickLink(
    'https://example.com', 
    'Visit Example', 
    ['target' => '_blank', 'rel' => ['nofollow']]
);
```

### 5. Rendering Options

All rendering methods support these options:

```php
$options = [
    'escape_text' => true,                    // HTML escape link text
    'enable_xss_protection' => true,          // Enable XSS filtering
    'fallback_href' => '#',                   // Fallback for invalid URLs
    'add_wrapper' => false,                   // Add container div
    'wrapper_class' => 'url-link-wrapper',    // Wrapper CSS class
    'link_class' => 'custom-link',            // Additional link CSS class
    'auto_accessibility' => true             // Auto-enhance accessibility
];
```

---

## Custom Attributes

### Repeater Field Interface

The enhanced URL field includes a repeater for custom attributes:

**User Interface:**
```
┌─────────────────────────────────────────────┐
│ Custom Attributes                           │
├─────────────────────────────────────────────┤
│ [+] Add Custom Attribute                    │
│                                             │
│ ┌─────────────────┐ ┌─────────────────────┐ │
│ │ Attribute Name  │ │ Attribute Value     │ │
│ │ data-product-id │ │ 123                 │ │
│ └─────────────────┘ └─────────────────────┘ │
│                                             │
│ ┌─────────────────┐ ┌─────────────────────┐ │
│ │ Attribute Name  │ │ Attribute Value     │ │
│ │ data-category   │ │ electronics         │ │
│ └─────────────────┘ └─────────────────────┘ │
└─────────────────────────────────────────────┘
```

### Field Configuration

```php
// Custom attributes are automatically included in enhanced URL fields
'custom_attributes' => [
    'type' => 'repeater',
    'label' => 'Custom Attributes',
    'description' => 'Add custom HTML attributes to the link',
    'fields' => [
        'attribute_name' => [
            'type' => 'text',
            'label' => 'Attribute Name',
            'placeholder' => 'data-custom',
            'required' => true
        ],
        'attribute_value' => [
            'type' => 'text', 
            'label' => 'Attribute Value',
            'placeholder' => 'custom-value',
            'required' => true
        ]
    ]
]
```

### Security Features

**Attribute Name Sanitization:**
- Only alphanumeric, hyphens, and underscores allowed
- Invalid characters are stripped
- Empty names after sanitization are ignored

**Protected Attributes:**
These critical attributes cannot be overridden by custom attributes:
- `href`
- `target` 
- `rel`
- `download`
- `aria-label`
- `title`

**Value Sanitization:**
- All attribute values are HTML escaped
- XSS protection applied

### Usage Examples

**E-commerce Product Link:**
```php
// Custom attributes in URL settings
$urlSettings = [
    'url' => 'https://shop.example.com/product/123',
    'target' => '_blank',
    'custom_attributes' => [
        ['attribute_name' => 'data-product-id', 'attribute_value' => '123'],
        ['attribute_name' => 'data-category', 'attribute_value' => 'electronics'],
        ['attribute_name' => 'data-price', 'attribute_value' => '99.99']
    ]
];
```

**Generated Output:**
```html
<a href="https://shop.example.com/product/123"
   target="_blank"
   data-product-id="123"
   data-category="electronics" 
   data-price="99.99">
   View Product
</a>
```

---

## Security Features

### 1. XSS Protection Integration

The system integrates with the existing `XSSProtection` utility:

```php
// Automatic XSS protection in rendering
if ($options['enable_xss_protection']) {
    $sanitizedUrl = XSSProtection::sanitizeURL($url);
    if ($sanitizedUrl === null) {
        $url = $options['fallback_href'];
    } else {
        $url = $sanitizedUrl;
    }
}
```

### 2. URL Validation

**Scheme Filtering:**
```php
const SCHEMES = [
    'web' => ['http', 'https'],
    'email' => ['mailto'],
    'phone' => ['tel', 'sms'],
    'all' => ['http', 'https', 'mailto', 'tel', 'sms', 'ftp']
];
```

**Dangerous Protocol Blocking:**
```php
const DANGEROUS_PROTOCOLS = [
    'javascript:', 'vbscript:', 'data:', 'blob:', 'file:',
    'ftp:', 'jar:', 'view-source:', 'chrome:', 'chrome-extension:'
];
```

### 3. Validation Process

```php
public static function validateURL(string $url, array $options = []): array
{
    // 1. Empty URL check
    // 2. URL type detection (anchor, email, phone, relative, absolute)
    // 3. Scheme validation
    // 4. Domain validation  
    // 5. XSS protection check
    // 6. Return validation result with metadata
}
```

### 4. Secure Attribute Handling

```php
// Attribute name sanitization
$attrName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $attrName);

// Protected attributes check
$protectedAttributes = ['href', 'target', 'rel', 'download', 'aria-label', 'title'];
if (in_array(strtolower($attrName), $protectedAttributes)) {
    continue; // Skip protected attributes
}

// Value sanitization
$attrValue = htmlspecialchars($attrValue, ENT_QUOTES, 'UTF-8');
```

---

## Accessibility Features

### 1. Automatic Link Context

The system automatically enhances link text with context:

```php
// Input
URLHandler::renderLink(['url' => 'mailto:contact@example.com'], 'Contact Us');

// Output
<a href="mailto:contact@example.com">Contact Us (email)</a>
```

### 2. External Link Indicators

```php
// External link detection
if ($validated['metadata']['is_external'] ?? false) {
    $enhancements[] = '(external link)';
    
    // Auto-add screen reader context
    if (empty($attributes['aria-label'])) {
        $attributes['aria-label'] = trim($linkText . ' (opens in new window)');
    }
}
```

### 3. Social Platform Detection

```php
const SOCIAL_DOMAINS = [
    'facebook.com' => 'facebook',
    'instagram.com' => 'instagram', 
    'twitter.com' => 'twitter',
    'x.com' => 'twitter',
    'linkedin.com' => 'linkedin',
    'youtube.com' => 'youtube'
];

// Automatic social platform context
// Input: https://facebook.com/page
// Output: <a href="...">Visit Page (facebook)</a>
```

### 4. ARIA Enhancements

**Built-in ARIA Support:**
- `aria-label` field for descriptive labels
- `title` attribute for tooltips
- Automatic external link context
- Screen reader friendly text enhancements

**Example Enhancement:**
```php
// Original text: "Click here"
// Enhanced text: "Click here (external link)"
// ARIA label: "Click here (external link) (opens in new window)"
```

---

## Migration Guide

### From Manual Field Groups

**Before (Manual Implementation):**
```php
// Old way - multiple fields
$control->addGroup('link', 'Link Settings')
    ->registerField('url', FieldManager::URL()
        ->setLabel('URL')
    )
    ->registerField('target', FieldManager::SELECT()
        ->setLabel('Target')
        ->setOptions([...])
    )
    ->registerField('rel', FieldManager::MULTISELECT()
        ->setLabel('Rel Attributes')
        ->setOptions([...])
    )
    ->registerField('aria_label', FieldManager::TEXT()
        ->setLabel('ARIA Label')
    )
    ->endGroup();

// Manual rendering with 20+ lines of code...
```

**After (Enhanced URL Field):**
```php
// New way - single field
$control->addGroup('link', 'Link Settings')
    ->registerField('link_config', FieldManager::ENHANCED_URL()
        ->setLabel('Link Configuration')
        ->setShowTargetOptions(true)
        ->setShowRelOptions(true)
        ->setEnableAccessibility(true)
    )
    ->endGroup();

// One-line rendering
return URLHandler::renderLink($settings['general']['link_config'], $linkText);
```

### Migration Steps

1. **Replace Field Groups:**
   ```php
   // Replace multiple URL-related fields with single enhanced field
   ->registerField('url_settings', FieldManager::ENHANCED_URL()
       ->setLabel('Link Configuration')
       ->setShowTargetOptions(true)
       ->setShowRelOptions(true)
   )
   ```

2. **Update Render Methods:**
   ```php
   // Replace manual HTML construction with URLHandler
   public function render(array $settings = []): string
   {
       $urlSettings = $settings['general']['url_settings'] ?? [];
       $text = $settings['general']['text'] ?? 'Click here';
       
       return URLHandler::renderLink($urlSettings, $text);
   }
   ```

3. **Update Settings Access:**
   ```php
   // Old way
   $url = $settings['general']['url'] ?? '#';
   $target = $settings['general']['target'] ?? '_self';
   
   // New way
   $urlSettings = $settings['general']['url_settings'] ?? [];
   // All URL configuration is in the single urlSettings array
   ```

---

## API Reference

### UrlField Methods

#### Configuration Methods
```php
->setValidateUrl(bool $validate = true): static
->setAllowedSchemes(array $schemes): static
->setAllowRelative(bool $allow = true): static
->setAllowAnchors(bool $allow = true): static
->setShowTargetOptions(bool $show = true): static
->setShowRelOptions(bool $show = true): static
->setShowDownloadOptions(bool $show = true): static
->setEnablePreview(bool $enable = true): static
->setEnableAccessibility(bool $enable = true): static
->setEnableTracking(bool $enable = true): static
->setDefaultTarget(string $target): static
->setDefaultRel(array $rel): static
->setContext(string $context): static
```

#### Preset Methods
```php
->asWebLink(): static          // External web links
->asEmailLink(): static        // Email links
->asPhoneLink(): static        // Phone/SMS links  
->asDownloadLink(): static     // Download links
->asInternalLink(): static     // Internal navigation
```

#### Validation & Utility Methods
```php
->validateValue($value): array
->generateLinkAttributes(string $url, array $settings = []): array
->getSubFields(): array
```

### URLHandler Methods

#### Primary Rendering Methods
```php
URLHandler::renderLink(array $urlSettings, string $linkText = '', array $options = []): string
URLHandler::renderButton(array $urlSettings, string $buttonText, array $options = []): string
URLHandler::renderLinks(array $urlSettingsArray, $linkTexts = [], array $options = []): string
URLHandler::quickLink(string $url, string $text, array $options = []): string
```

#### Utility Methods
```php
URLHandler::validateURL(string $url, array $options = []): array
URLHandler::generateLinkAttributes(array $urlData, array $options = []): array
URLHandler::convertURL(string $url, string $format): string
URLHandler::extractURLs(string $text): array
URLHandler::generateAccessibleLinkText(string $url, string $text): string
```

### FieldManager Factory Methods
```php
FieldManager::URL(): UrlField
FieldManager::ENHANCED_URL(): UrlField
FieldManager::WEB_LINK(): UrlField
FieldManager::EMAIL_LINK(): UrlField
FieldManager::PHONE_LINK(): UrlField
FieldManager::DOWNLOAD_LINK(): UrlField
FieldManager::INTERNAL_LINK(): UrlField
```

---

## Real-World Examples

### 1. Social Media Widget

```php
// Widget field definition
->registerField('social_links', FieldManager::REPEATER()
    ->setLabel('Social Media Links')
    ->setFields([
        'platform' => FieldManager::SELECT()
            ->setLabel('Platform')
            ->setOptions([
                'facebook' => 'Facebook',
                'twitter' => 'Twitter', 
                'instagram' => 'Instagram'
            ]),
        'url_config' => FieldManager::WEB_LINK()
            ->setLabel('Profile URL')
            ->setDefaultTarget('_blank')
            ->setDefaultRel(['nofollow'])
    ])
)

// Rendering
public function render(array $settings = []): string
{
    $socialLinks = $settings['general']['social_links'] ?? [];
    $html = '<div class="social-links">';
    
    foreach ($socialLinks as $link) {
        $platform = $link['platform'] ?? '';
        $urlConfig = $link['url_config'] ?? [];
        
        // Add platform-specific custom attributes
        $urlConfig['custom_attributes'] = [
            ['attribute_name' => 'data-platform', 'attribute_value' => $platform],
            ['attribute_name' => 'data-social', 'attribute_value' => 'true']
        ];
        
        $html .= URLHandler::renderLink($urlConfig, ucfirst($platform), [
            'link_class' => "social-link social-{$platform}"
        ]);
    }
    
    return $html . '</div>';
}
```

### 2. Call-to-Action Button Widget

```php
// Widget field definition
->registerField('cta_button', FieldManager::ENHANCED_URL()
    ->setLabel('Call-to-Action Button')
    ->setShowTargetOptions(true)
    ->setShowRelOptions(true)
    ->setEnableTracking(true)
    ->setDescription('Complete CTA button with tracking and analytics')
)
->registerField('button_text', FieldManager::TEXT()
    ->setLabel('Button Text')
    ->setDefault('Get Started')
)

// Rendering
public function render(array $settings = []): string
{
    $buttonSettings = $settings['general']['cta_button'] ?? [];
    $buttonText = $settings['general']['button_text'] ?? 'Get Started';
    
    // Add analytics attributes
    $buttonSettings['custom_attributes'] = array_merge(
        $buttonSettings['custom_attributes'] ?? [],
        [
            ['attribute_name' => 'data-analytics', 'attribute_value' => 'cta-click'],
            ['attribute_name' => 'data-widget-type', 'attribute_value' => 'cta-button']
        ]
    );
    
    return URLHandler::renderButton($buttonSettings, $buttonText, [
        'button_class' => 'btn btn-primary btn-lg',
        'add_wrapper' => true,
        'wrapper_class' => 'cta-container text-center'
    ]);
}
```

### 3. Download Center Widget

```php
// Widget field definition
->registerField('downloads', FieldManager::REPEATER()
    ->setLabel('Download Files')
    ->setFields([
        'file_name' => FieldManager::TEXT()
            ->setLabel('File Name'),
        'file_size' => FieldManager::TEXT()
            ->setLabel('File Size'),
        'download_link' => FieldManager::DOWNLOAD_LINK()
            ->setLabel('Download URL')
            ->setShowDownloadOptions(true)
    ])
)

// Rendering
public function render(array $settings = []): string
{
    $downloads = $settings['general']['downloads'] ?? [];
    $html = '<div class="download-center">';
    
    foreach ($downloads as $download) {
        $fileName = $download['file_name'] ?? 'Download';
        $fileSize = $download['file_size'] ?? '';
        $downloadConfig = $download['download_link'] ?? [];
        
        // Add file metadata as custom attributes
        $downloadConfig['custom_attributes'] = [
            ['attribute_name' => 'data-file-name', 'attribute_value' => $fileName],
            ['attribute_name' => 'data-file-size', 'attribute_value' => $fileSize],
            ['attribute_name' => 'data-download-type', 'attribute_value' => 'file']
        ];
        
        $downloadText = $fileName . ($fileSize ? " ({$fileSize})" : '');
        
        $html .= '<div class="download-item">';
        $html .= URLHandler::renderLink($downloadConfig, $downloadText, [
            'link_class' => 'download-link',
            'add_wrapper' => false
        ]);
        $html .= '</div>';
    }
    
    return $html . '</div>';
}
```

### 4. Contact Information Widget

```php
// Widget field definition  
->registerField('contact_email', FieldManager::EMAIL_LINK()
    ->setLabel('Email Address')
    ->setPlaceholder('mailto:contact@example.com')
)
->registerField('contact_phone', FieldManager::PHONE_LINK()
    ->setLabel('Phone Number')
    ->setPlaceholder('tel:+1234567890')
)
->registerField('website', FieldManager::WEB_LINK()
    ->setLabel('Website')
    ->setDefaultTarget('_blank')
)

// Rendering
public function render(array $settings = []): string
{
    $emailConfig = $settings['general']['contact_email'] ?? [];
    $phoneConfig = $settings['general']['contact_phone'] ?? [];
    $websiteConfig = $settings['general']['website'] ?? [];
    
    $contacts = [
        'email' => $emailConfig,
        'phone' => $phoneConfig, 
        'website' => $websiteConfig
    ];
    
    $contactTexts = ['Email Us', 'Call Us', 'Visit Website'];
    
    return URLHandler::renderLinks($contacts, $contactTexts, [
        'wrapper_tag' => 'div',
        'wrapper_class' => 'contact-links',
        'item_wrapper' => 'p',
        'item_class' => 'contact-item',
        'separator' => ''
    ]);
}
```

---

## Best Practices

### 1. Field Configuration

- **Use preset types** when possible (WEB_LINK, EMAIL_LINK, etc.)
- **Enable appropriate options** based on use case
- **Set descriptive labels** and placeholders
- **Provide context** in field descriptions

### 2. Security

- **Always enable XSS protection** in production
- **Validate custom attributes** before processing
- **Use fallback URLs** for invalid inputs
- **Test with malicious inputs** during development

### 3. Accessibility

- **Enable accessibility features** by default
- **Provide descriptive link text** context
- **Use ARIA labels** for complex interactions
- **Test with screen readers**

### 4. Performance

- **Cache validation results** for repeated URLs
- **Use appropriate rendering methods** (renderLink vs quickLink)
- **Minimize custom attribute processing** in loops
- **Consider URL conversion** for canonical forms

---

This documentation provides comprehensive guidance for implementing and using the Enhanced URL Field System in your pagebuilder widgets. The system significantly reduces development time while improving security, accessibility, and user experience.