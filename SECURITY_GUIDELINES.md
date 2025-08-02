# 🔒 Security Guidelines - XSS Prevention

## Overview

This document provides comprehensive security guidelines for the PageBuilder widget system, with a focus on Cross-Site Scripting (XSS) prevention. **All widget developers MUST follow these guidelines** to ensure user safety and system security.

## 🚨 Critical Security Requirements

### ✅ REQUIRED: Use XSSProtection Utility

**All user input MUST be sanitized using the XSSProtection utility class.**

```php
use App\Utils\XSSProtection;

// ✅ CORRECT: Sanitize all user input
$safeText = XSSProtection::sanitizeText($userInput);
$safeHTML = XSSProtection::sanitizeHTML($userInput, 'widget');
$safeURL = XSSProtection::sanitizeURL($userInput);

// ❌ WRONG: Never trust user input directly
return "<div>{$userInput}</div>"; // DANGEROUS!
```

### ✅ REQUIRED: Use BaseWidget Security Methods

**All widgets inherit security methods from BaseWidget. Use them consistently.**

```php
class MyWidget extends BaseWidget
{
    public function render(array $settings = []): string
    {
        // ✅ CORRECT: Use built-in sanitization
        $safeText = $this->sanitizeInput($settings['text'] ?? '', 'text');
        $safeURL = $this->sanitizeInput($settings['url'] ?? '', 'url');
        $safeAttributes = $this->buildSecureAttributes($attributes);
        
        return "<a {$safeAttributes}>{$safeText}</a>";
    }
}
```

## 🛡️ XSSProtection Utility Class

### Available Methods

#### 1. Text Content Sanitization

```php
// Basic text (escapes HTML entities)
$safe = XSSProtection::sanitizeText($input);

// Text with line breaks preserved as <br> tags
$safe = XSSProtection::sanitizeText($input, true);
```

#### 2. HTML Content Sanitization

```php
// Minimal HTML (only basic formatting)
$safe = XSSProtection::sanitizeHTML($input, 'minimal');

// Basic HTML (headings, lists, links)
$safe = XSSProtection::sanitizeHTML($input, 'basic');

// Rich HTML (images, divs, more elements)
$safe = XSSProtection::sanitizeHTML($input, 'rich');

// Widget HTML (optimized for widget content)
$safe = XSSProtection::sanitizeHTML($input, 'widget');
```

#### 3. URL Validation

```php
// Standard URL validation
$safeURL = XSSProtection::sanitizeURL($input);

// Custom allowed schemes
$safeURL = XSSProtection::sanitizeURL($input, ['http', 'https', 'mailto']);
```

#### 4. CSS Sanitization

```php
// Clean CSS properties and values
$safeCSS = XSSProtection::sanitizeCSS($input);
```

#### 5. Widget Content Sanitization

```php
// Automatically sanitize entire widget content array
$safeSettings = XSSProtection::sanitizeWidgetContent($settings);
```

## 🔧 Widget Development Security Checklist

### ✅ Input Handling

```php
public function render(array $settings = []): string
{
    // ✅ 1. Sanitize all user inputs
    $general = $settings['general'] ?? [];
    $content = $general['content'] ?? [];
    
    // ✅ 2. Use appropriate sanitization for each field type
    $text = $this->sanitizeInput($content['text'] ?? '', 'text');
    $url = $this->sanitizeInput($content['url'] ?? '', 'url');
    $customCSS = $this->sanitizeInput($content['custom_css'] ?? '', 'css');
    
    // ✅ 3. Validate and whitelist enum values
    $allowedSizes = ['sm', 'md', 'lg', 'xl'];
    $size = in_array($content['size'] ?? 'md', $allowedSizes) 
        ? $content['size'] 
        : 'md';
    
    // ✅ 4. Build secure HTML
    return $this->buildSecureHTML($text, $url, $size);
}
```

### ✅ HTML Generation

```php
private function buildSecureHTML(string $text, string $url, string $size): string
{
    // ✅ 1. Use secure attribute building
    $attributes = [
        'href' => $url,
        'class' => "btn btn-{$size}",
        'target' => '_blank'
    ];
    
    $safeAttributes = $this->buildSecureAttributes($attributes);
    
    // ✅ 2. Ensure content is already sanitized
    return "<a {$safeAttributes}>{$text}</a>";
}
```

### ✅ CSS Handling

```php
// ✅ CORRECT: Sanitize CSS values
private function buildInlineStyles(array $style): string
{
    $styles = [];
    
    if (isset($style['background_color'])) {
        // Validate hex color format
        if (preg_match('/^#[a-fA-F0-9]{6}$/', $style['background_color'])) {
            $styles[] = 'background-color: ' . $style['background_color'];
        }
    }
    
    // Sanitize any custom CSS
    if (isset($style['custom_css'])) {
        $styles[] = XSSProtection::sanitizeCSS($style['custom_css']);
    }
    
    return implode('; ', $styles);
}
```

## 🚫 Common Security Mistakes

### ❌ NEVER Do These Things

```php
// ❌ 1. Never output user input directly
return "<div>{$userInput}</div>";

// ❌ 2. Never trust URLs without validation
$html = "<a href='{$userURL}'>Link</a>";

// ❌ 3. Never allow raw CSS without sanitization
$style = "style='{$userCSS}'";

// ❌ 4. Never use dangerous HTML functions
$html = html_entity_decode($userInput); // DANGEROUS!

// ❌ 5. Never trust file uploads without validation
move_uploaded_file($_FILES['upload']['tmp_name'], $destination);
```

### ✅ Secure Alternatives

```php
// ✅ 1. Always sanitize user input
return "<div>" . XSSProtection::sanitizeText($userInput) . "</div>";

// ✅ 2. Validate URLs before use
$safeURL = XSSProtection::sanitizeURL($userURL);
if ($safeURL) {
    $html = "<a href='{$safeURL}'>Link</a>";
}

// ✅ 3. Sanitize CSS properties
$safeCSS = XSSProtection::sanitizeCSS($userCSS);
$style = "style='{$safeCSS}'";

// ✅ 4. Use proper escaping functions
$safe = htmlspecialchars($userInput, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// ✅ 5. Validate file uploads properly
$safeFile = XSSProtection::sanitizeFileUpload($_FILES['upload']);
if ($safeFile) {
    // Process safe file
}
```

## 🛡️ Advanced Security Features

### Content Security Policy (CSP)

```php
// Generate CSP header for additional protection
$csp = XSSProtection::generateCSP([
    'script-src' => "'self' 'unsafe-inline'",
    'style-src' => "'self' 'unsafe-inline'",
    'img-src' => "'self' data: https:"
]);

// Add to response headers
header("Content-Security-Policy: {$csp}");
```

### Threat Detection

```php
// Detect potential security threats in content
$threats = XSSProtection::detectThreats($userContent);
if (!empty($threats)) {
    // Log security incident
    Log::warning('Security threats detected', [
        'threats' => $threats,
        'content' => $userContent,
        'user_id' => auth()->id()
    ]);
    
    // Take appropriate action (block, sanitize, alert)
}
```

### File Upload Security

```php
// Secure file upload handling
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$safeFile = XSSProtection::sanitizeFileUpload($_FILES['image'], $allowedTypes);

if ($safeFile) {
    // Process validated file
    $destination = storage_path('uploads/' . $safeFile['name']);
    move_uploaded_file($safeFile['tmp_name'], $destination);
} else {
    throw new InvalidArgumentException('Invalid file upload');
}
```

## 📋 Security Testing Checklist

### Before Deploying Any Widget

- [ ] **Input Sanitization**: All user inputs are sanitized using XSSProtection
- [ ] **URL Validation**: All URLs are validated and dangerous protocols blocked
- [ ] **CSS Sanitization**: All CSS content is cleaned of dangerous expressions
- [ ] **HTML Escaping**: All dynamic content is properly escaped
- [ ] **Attribute Security**: All HTML attributes use secure building methods
- [ ] **Threat Detection**: Implement logging for detected security threats
- [ ] **File Uploads**: If applicable, file uploads are properly validated
- [ ] **Testing**: Widget tested with malicious inputs (see test cases below)

### Security Test Cases

Test your widgets with these potentially dangerous inputs:

```php
$maliciousInputs = [
    // Script injection
    '<script>alert("XSS")</script>',
    'javascript:alert("XSS")',
    
    // Event handlers
    '<img src="x" onerror="alert(\'XSS\')">',
    '<div onclick="alert(\'XSS\')">Click me</div>',
    
    // CSS injection
    'background: url(javascript:alert("XSS"))',
    'expression(alert("XSS"))',
    
    // Data URIs
    'data:text/html,<script>alert("XSS")</script>',
    
    // Protocol injection
    'ftp://malicious.com/file.exe',
    'file:///etc/passwd'
];

foreach ($maliciousInputs as $input) {
    $result = $widget->render(['content' => ['text' => $input]]);
    // Verify $result does not contain executable code
    assert(strpos($result, '<script>') === false);
    assert(strpos($result, 'javascript:') === false);
}
```

## 🔄 Migration Guide

### Updating Existing Widgets

1. **Add XSSProtection import:**
```php
use App\Utils\XSSProtection;
```

2. **Replace manual escaping with XSSProtection methods:**
```php
// Before
$safe = htmlspecialchars($input, ENT_QUOTES);

// After
$safe = XSSProtection::sanitizeText($input);
```

3. **Use BaseWidget security methods:**
```php
// Before
$attrs = 'href="' . $url . '" class="' . $class . '"';

// After
$attrs = $this->buildSecureAttributes(['href' => $url, 'class' => $class]);
```

4. **Update render methods:**
```php
// Before
public function render(array $settings = []): string
{
    $text = $settings['text'] ?? '';
    return "<div>{$text}</div>";
}

// After
public function render(array $settings = []): string
{
    $text = $this->sanitizeInput($settings['text'] ?? '', 'text');
    return "<div>{$text}</div>";
}
```

## 📚 Additional Resources

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [Content Security Policy Guide](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)

## ⚠️ Security Incident Response

If you discover a security vulnerability:

1. **DO NOT** commit the fix to public repositories immediately
2. **Report** the issue to the security team
3. **Document** the vulnerability and potential impact
4. **Test** the fix thoroughly before deployment
5. **Update** this documentation if new patterns are discovered

---

**Remember: Security is everyone's responsibility. When in doubt, sanitize!** 🔒