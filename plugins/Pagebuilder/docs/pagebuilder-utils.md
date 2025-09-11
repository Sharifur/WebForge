# PageBuilderUtils Documentation

PageBuilderUtils provides a comprehensive set of utility methods for rendering various page builder elements with built-in XSS protection and security measures. It focuses on secure link rendering with support for HTML content and multiple link types.

## Overview

PageBuilderUtils offers:
- **Secure Link Rendering** - XSS-protected link generation with enhanced security
- **Content Support** - HTML markup and plain text content handling
- **Multiple Link Types** - Support for internal, external, email, phone, and file links
- **Advanced Attributes** - Custom HTML attributes, UTM parameters, and accessibility features
- **Helper Methods** - Pre-configured link types with security defaults

## Main Features

### 1. Enhanced Link Rendering
- Smart URL sanitization and validation
- XSS prevention for all inputs
- Content escaping options
- Custom attribute filtering
- UTM parameter injection

### 2. Multiple Link Types
- Internal pages (`/page-slug`, `#section`)
- External URLs (`https://example.com`)
- Email links (`mailto:user@example.com`)
- Phone links (`tel:+1-555-123-4567`)
- File downloads (`/path/to/file.pdf`)

### 3. Security Features
- Blocks dangerous protocols (`javascript:`, unsafe `data:`)
- Validates all URL formats
- Sanitizes custom attributes
- Auto-adds security rel attributes
- Comprehensive input filtering

## Basic Usage

### Simple Link Rendering

```php
use Plugins\Pagebuilder\Core\PageBuilderUtils;

// Basic link with enhanced link data
$linkData = [
    'url' => 'https://example.com',
    'text' => 'Visit Example',
    'target' => '_blank',
    'rel' => ['noopener', 'noreferrer']
];

$html = PageBuilderUtils::renderLink($linkData, '<strong>Click Here</strong>');
// Output: <a href="https://example.com" target="_blank" rel="noopener noreferrer"><strong>Click Here</strong></a>
```

### Helper Methods

```php
// External link with security defaults
$html = PageBuilderUtils::createExternalLink('https://example.com', 'Visit Example');
// Auto-adds target="_blank" and rel="noopener noreferrer"

// Email link
$html = PageBuilderUtils::createEmailLink('user@example.com', 'Contact Us');
// Output: <a href="mailto:user@example.com">Contact Us</a>

// Phone link
$html = PageBuilderUtils::createPhoneLink('+1-555-123-4567', 'Call Us');
// Output: <a href="tel:+1-555-123-4567">Call Us</a>
```

## Advanced Usage

### Comprehensive Link Configuration

```php
$linkData = [
    'url' => 'https://example.com/page',
    'text' => 'Example Link',
    'type' => 'external',
    'target' => '_blank',
    'rel' => ['nofollow', 'noopener'],
    'title' => 'Visit our example page',
    'id' => 'example-link',
    'class' => 'btn btn-primary',
    'custom_attributes' => [
        ['name' => 'data-track', 'value' => 'click'],
        ['name' => 'aria-label', 'value' => 'External example link']
    ],
    'utm_parameters' => [
        'utm_source' => 'website',
        'utm_medium' => 'button',
        'utm_campaign' => 'homepage'
    ]
];

$options = [
    'escape_content' => false,  // Allow HTML in content
    'css_classes' => ['btn', 'btn-secondary'],
    'allow_empty_url' => false
];

$html = PageBuilderUtils::renderLink($linkData, '<span class="icon">🔗</span> Custom Content', $options);
```

### Content Handling Options

```php
// HTML content (not escaped)
$html = PageBuilderUtils::renderLink($linkData, '<strong>Bold Text</strong>', [
    'escape_content' => false
]);

// Plain text content (escaped)
$html = PageBuilderUtils::renderLink($linkData, '<script>alert("xss")</script>', [
    'escape_content' => true
]);
// XSS content will be safely escaped

// Empty URL handling
$html = PageBuilderUtils::renderLink(['url' => ''], 'Content', [
    'allow_empty_url' => true  // Renders as <span>Content</span>
]);
```

## Security Features

### XSS Prevention

```php
// Dangerous URLs are blocked
$linkData = ['url' => 'javascript:alert("xss")'];
$html = PageBuilderUtils::renderLink($linkData, 'Click');
// Returns: Click (no link, URL blocked)

// Data URLs are filtered
$linkData = ['url' => 'data:text/html,<script>alert(1)</script>'];
$html = PageBuilderUtils::renderLink($linkData, 'Click');
// Returns: Click (no link, dangerous data URL blocked)

// Safe data URLs are allowed
$linkData = ['url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg=='];
$html = PageBuilderUtils::renderLink($linkData, 'Image');
// Valid image data URL is allowed
```

### Input Sanitization

```php
$linkData = [
    'url' => 'https://example.com',
    'text' => '<script>alert("xss")</script>',
    'title' => '<img src=x onerror=alert(1)>',
    'id' => 'test<script>',
    'class' => 'btn alert("xss")',
    'custom_attributes' => [
        ['name' => 'onclick', 'value' => 'alert("xss")'],  // Blocked - not in whitelist
        ['name' => 'data-safe', 'value' => 'safe value']   // Allowed - data- prefix
    ]
];

$html = PageBuilderUtils::renderLink($linkData, 'Safe Link');
// All dangerous content is sanitized or filtered
```

### Attribute Filtering

```php
// Only safe attributes are allowed
$allowedAttributes = [
    'data-*',           // All data attributes
    'aria-*',           // All ARIA attributes  
    'class', 'id', 'title', 'style', 'role', 'tabindex',
    'lang', 'dir', 'hidden', 'accesskey'
];

// Dangerous attributes are filtered out
$dangerous = ['onclick', 'onload', 'onerror', 'javascript:', 'vbscript:'];
// These will be ignored/filtered
```

## Link Type Handling

### Internal Links

```php
$linkData = [
    'url' => '/internal/page',
    'type' => 'internal'
];

$html = PageBuilderUtils::renderLink($linkData, 'Internal Page');
// Handles relative URLs safely
```

### Email Links

```php
$linkData = [
    'url' => 'user@example.com',  // Auto-detects email
    'type' => 'email'
];

$html = PageBuilderUtils::renderLink($linkData, 'Send Email');
// Output: <a href="mailto:user@example.com">Send Email</a>

// Or use helper
$html = PageBuilderUtils::createEmailLink('user@example.com');
```

### Phone Links

```php
$linkData = [
    'url' => '+1-555-123-4567',
    'type' => 'phone'
];

$html = PageBuilderUtils::renderLink($linkData, 'Call Now');
// Output: <a href="tel:+1-555-123-4567">Call Now</a>

// Or use helper
$html = PageBuilderUtils::createPhoneLink('+1-555-123-4567', 'Call Now');
```

### External Links with Security

```php
$linkData = [
    'url' => 'https://external-site.com',
    'target' => '_blank'
];

$html = PageBuilderUtils::renderLink($linkData, 'External Site');
// Auto-adds rel="noopener" for security

// Or use helper with defaults
$html = PageBuilderUtils::createExternalLink('https://external-site.com', 'External Site');
// Automatically sets target="_blank" and rel="noopener noreferrer"
```

## UTM Parameter Handling

### Automatic UTM Injection

```php
$linkData = [
    'url' => 'https://example.com',
    'utm_parameters' => [
        'utm_source' => 'website',
        'utm_medium' => 'banner',
        'utm_campaign' => 'spring_sale',
        'utm_term' => 'discount',
        'utm_content' => 'header_banner'
    ]
];

$html = PageBuilderUtils::renderLink($linkData, 'Shop Now');
// URL becomes: https://example.com?utm_source=website&utm_medium=banner&utm_campaign=spring_sale&utm_term=discount&utm_content=header_banner
```

### UTM Sanitization

```php
$linkData = [
    'utm_parameters' => [
        'utm_source' => '<script>alert("xss")</script>',  // Will be sanitized
        'utm_medium' => 'safe-value'                      // Will be preserved
    ]
];

// UTM values are HTML-escaped and URL-encoded for safety
```

## Widget Integration

### In Widget Render Methods

```php
class ExampleWidget extends BaseWidget
{
    public function render(array $settings = []): string
    {
        $linkData = $settings['general']['link']['enhanced_link'] ?? [];
        
        if (!empty($linkData['url'])) {
            $linkHtml = PageBuilderUtils::renderLink(
                $linkData, 
                '<span class="widget-link-text">' . $this->sanitizeText($settings['general']['content']['text'] ?? 'Click Here') . '</span>',
                ['escape_content' => false]
            );
            
            return "<div class=\"widget-wrapper\">{$linkHtml}</div>";
        }
        
        return "<div class=\"widget-wrapper\">" . $this->sanitizeText($settings['general']['content']['text'] ?? '') . "</div>";
    }
}
```

### With Enhanced Link Field Data

```php
// Data from EnhancedLinkPicker component
$enhancedLinkData = [
    'url' => 'https://example.com',
    'text' => 'Custom Link Text',
    'type' => 'external',
    'target' => '_blank',
    'rel' => ['nofollow', 'noopener'],
    'title' => 'Visit Example Website',
    'id' => 'example-link',
    'class' => 'btn btn-primary',
    'custom_attributes' => [
        ['name' => 'data-analytics', 'value' => 'track-click'],
        ['name' => 'aria-describedby', 'value' => 'link-description']
    ],
    'utm_parameters' => [
        'utm_source' => 'widget',
        'utm_medium' => 'cta_button'
    ]
];

// Render with PageBuilderUtils
$html = PageBuilderUtils::renderLink($enhancedLinkData, $enhancedLinkData['text']);
```

## API Reference

### Main Methods

#### renderLink(array $linkData, string $content = '', array $options = []): string
Main method for rendering secure links with full feature support.

**Parameters:**
- `$linkData`: Link configuration array
- `$content`: Content to wrap in link (HTML or text)
- `$options`: Rendering options

**Options:**
- `escape_content`: Whether to HTML-escape content (default: false)
- `allow_empty_url`: Render span if URL is empty (default: false)
- `default_target`: Fallback target (default: '_self')
- `css_classes`: Additional CSS classes (default: [])

#### createLink(string $url, string $content = '', array $options = []): string
Simple link creation with basic options.

#### createExternalLink(string $url, string $content = '', array $options = []): string
External link with security defaults (`target="_blank"`, `rel="noopener noreferrer"`).

#### createEmailLink(string $email, string $content = '', array $options = []): string
Email link with `mailto:` protocol.

#### createPhoneLink(string $phone, string $content = '', array $options = []): string
Phone link with `tel:` protocol.

### Utility Methods

#### sanitizeLinkData(array $linkData): array
Sanitize all link data for security.

#### sanitizeURL(string $url): string
Sanitize and validate URL format.

## Error Handling

### Invalid URLs

```php
$linkData = ['url' => 'not-a-valid-url'];
$html = PageBuilderUtils::renderLink($linkData, 'Invalid Link');
// Returns content without link wrapper: "Invalid Link"
```

### Empty Content

```php
$linkData = ['url' => 'https://example.com'];
$html = PageBuilderUtils::renderLink($linkData);  // No content provided
// Falls back to URL: <a href="https://example.com">https://example.com</a>
```

### Security Blocks

```php
$linkData = ['url' => 'javascript:alert(1)'];
$html = PageBuilderUtils::renderLink($linkData, 'Blocked');
// Returns content only: "Blocked" (dangerous URL blocked)
```

## Best Practices

### 1. Always Use PageBuilderUtils for Link Rendering
- Provides consistent XSS protection
- Handles edge cases automatically
- Maintains security standards

### 2. Content Escaping Strategy
- Use `escape_content: false` for trusted HTML content
- Use `escape_content: true` for user-generated content
- Validate and sanitize content before rendering

### 3. External Link Security
- Always use `createExternalLink()` for external URLs
- Verify that security rel attributes are present
- Consider link target policies for user experience

### 4. UTM Parameter Management
- Sanitize UTM values from user input
- Use consistent parameter naming
- Monitor URL length with many parameters

### 5. Custom Attribute Safety
- Only use whitelisted attribute names
- Validate attribute values
- Prefer data-* and aria-* attributes for custom functionality

The PageBuilderUtils system provides a robust, secure foundation for link rendering throughout the page builder while maintaining flexibility for various use cases and content types.