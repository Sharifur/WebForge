<?php

namespace App\Utils;

/**
 * XSSProtection - Comprehensive XSS Prevention Utility
 * 
 * Provides robust protection against Cross-Site Scripting (XSS) attacks
 * with multiple layers of sanitization and validation. Essential for
 * any user-generated content in page builder widgets.
 * 
 * Security Features:
 * - HTML tag sanitization with whitelist approach
 * - Attribute filtering and validation
 * - URL validation and protocol checking
 * - CSS property sanitization
 * - JavaScript code detection and removal
 * - Content Security Policy helpers
 * 
 * @package App\Utils
 * @author PageBuilder Security Team
 * @version 1.0.0
 */
class XSSProtection
{
    /**
     * Allowed HTML tags for different content types
     */
    const ALLOWED_TAGS = [
        'minimal' => ['p', 'br', 'strong', 'em', 'span'],
        'basic' => ['p', 'br', 'strong', 'em', 'span', 'a', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        'rich' => ['p', 'br', 'strong', 'em', 'span', 'a', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'img', 'div', 'blockquote', 'code', 'pre'],
        'widget' => ['div', 'span', 'p', 'a', 'strong', 'em', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'img', 'i', 'svg', 'path']
    ];
    
    /**
     * Allowed HTML attributes
     */
    const ALLOWED_ATTRIBUTES = [
        'global' => ['class', 'id', 'data-*', 'aria-*', 'role', 'title'],
        'a' => ['href', 'target', 'rel', 'download'],
        'img' => ['src', 'alt', 'width', 'height', 'loading'],
        'svg' => ['viewBox', 'xmlns', 'width', 'height', 'fill', 'stroke'],
        'path' => ['d', 'fill', 'stroke', 'stroke-width'],
        'i' => ['class'], // For icon fonts
        'div' => ['class', 'id', 'style'], // Limited style attribute
        'span' => ['class', 'id', 'style']
    ];
    
    /**
     * Dangerous protocols to block
     */
    const DANGEROUS_PROTOCOLS = [
        'javascript:', 'vbscript:', 'data:', 'blob:', 'file:',
        'ftp:', 'jar:', 'view-source:', 'chrome:', 'chrome-extension:'
    ];
    
    /**
     * Dangerous CSS properties and values
     */
    const DANGEROUS_CSS = [
        'properties' => ['expression', 'behavior', '-moz-binding', 'javascript'],
        'values' => ['javascript:', 'expression(', 'url(javascript:', '@import', 'behavior:']
    ];
    
    /**
     * Sanitize HTML content with configurable security level
     * 
     * @param string $content Raw HTML content
     * @param string $level Security level: 'minimal', 'basic', 'rich', 'widget'
     * @param array $customOptions Custom sanitization options
     * @return string Sanitized HTML content
     */
    public static function sanitizeHTML(string $content, string $level = 'basic', array $customOptions = []): string
    {
        if (empty($content)) {
            return '';
        }
        
        // Get allowed tags and attributes based on level
        $allowedTags = self::ALLOWED_TAGS[$level] ?? self::ALLOWED_TAGS['basic'];
        $options = array_merge([
            'allowed_tags' => $allowedTags,
            'strip_dangerous_attributes' => true,
            'validate_urls' => true,
            'sanitize_css' => true,
            'remove_scripts' => true
        ], $customOptions);
        
        // Remove dangerous scripts and comments first
        $content = self::removeScripts($content);
        $content = self::removeComments($content);
        
        // Use DOMDocument for proper HTML parsing
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->encoding = 'UTF-8';
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML with UTF-8 support
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Clear libxml errors
        libxml_clear_errors();
        
        // Process all elements
        self::processElements($dom, $options);
        
        // Get cleaned HTML
        $sanitized = $dom->saveHTML();
        
        // Remove XML declaration if present
        $sanitized = preg_replace('/^<\?xml[^>]*>/', '', $sanitized);
        
        return trim($sanitized);
    }
    
    /**
     * Sanitize text content (escape HTML entities)
     * 
     * @param string $text Raw text content
     * @param bool $preserveLineBreaks Convert line breaks to <br> tags
     * @return string Sanitized text
     */
    public static function sanitizeText(string $text, bool $preserveLineBreaks = false): string
    {
        if (empty($text)) {
            return '';
        }
        
        // Escape HTML entities
        $sanitized = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Preserve line breaks if requested
        if ($preserveLineBreaks) {
            $sanitized = nl2br($sanitized);
        }
        
        return $sanitized;
    }
    
    /**
     * Validate and sanitize URLs
     * 
     * @param string $url Raw URL
     * @param array $allowedSchemes Allowed URL schemes
     * @return string|null Sanitized URL or null if invalid
     */
    public static function sanitizeURL(string $url, array $allowedSchemes = ['http', 'https', 'mailto', 'tel']): ?string
    {
        if (empty($url)) {
            return null;
        }
        
        // Trim and decode
        $url = trim(urldecode($url));
        
        // Check for dangerous protocols
        foreach (self::DANGEROUS_PROTOCOLS as $protocol) {
            if (stripos($url, $protocol) === 0) {
                return null;
            }
        }
        
        // Handle relative URLs
        if (strpos($url, '//') === 0) {
            $url = 'https:' . $url;
        } elseif (strpos($url, '/') === 0 || !preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
            // Relative URL or no scheme - assume it's safe
            return filter_var($url, FILTER_SANITIZE_URL);
        }
        
        // Validate URL format
        $parsed = parse_url($url);
        if ($parsed === false) {
            return null;
        }
        
        // Check scheme
        if (isset($parsed['scheme']) && !in_array(strtolower($parsed['scheme']), $allowedSchemes)) {
            return null;
        }
        
        // Reconstruct and validate
        $cleanUrl = filter_var($url, FILTER_VALIDATE_URL);
        return $cleanUrl !== false ? $cleanUrl : null;
    }
    
    /**
     * Sanitize CSS properties and values
     * 
     * @param string $css Raw CSS content
     * @return string Sanitized CSS
     */
    public static function sanitizeCSS(string $css): string
    {
        if (empty($css)) {
            return '';
        }
        
        // Remove dangerous CSS expressions
        foreach (self::DANGEROUS_CSS['properties'] as $dangerous) {
            $css = preg_replace('/\b' . preg_quote($dangerous, '/') . '\b/i', '', $css);
        }
        
        foreach (self::DANGEROUS_CSS['values'] as $dangerous) {
            $css = str_ireplace($dangerous, '', $css);
        }
        
        // Remove @import and other dangerous at-rules
        $css = preg_replace('/@(import|charset|namespace|media|supports|document)[^;{]*[;{]/i', '', $css);
        
        // Remove comments
        $css = preg_replace('/\/\*.*?\*\//s', '', $css);
        
        // Basic CSS validation - only allow safe characters
        $css = preg_replace('/[^\w\s\-:;,.()%#\/\'"]/u', '', $css);
        
        return trim($css);
    }
    
    /**
     * Remove dangerous script content
     */
    private static function removeScripts(string $content): string
    {
        // Remove script tags and their content
        $content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);
        
        // Remove event handlers
        $content = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']?/i', '', $content);
        
        // Remove javascript: links
        $content = preg_replace('/href\s*=\s*["\']?\s*javascript:[^"\']*["\']?/i', 'href="#"', $content);
        
        return $content;
    }
    
    /**
     * Remove HTML comments
     */
    private static function removeComments(string $content): string
    {
        return preg_replace('/<!--.*?-->/s', '', $content);
    }
    
    /**
     * Process DOM elements recursively
     */
    private static function processElements(\DOMDocument $dom, array $options): void
    {
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//*');
        
        $nodesToRemove = [];
        
        foreach ($nodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            
            $tagName = strtolower($node->tagName);
            
            // Check if tag is allowed
            if (!in_array($tagName, $options['allowed_tags'])) {
                $nodesToRemove[] = $node;
                continue;
            }
            
            // Process attributes
            self::processAttributes($node, $options);
        }
        
        // Remove disallowed elements
        foreach ($nodesToRemove as $node) {
            if ($node->parentNode) {
                $node->parentNode->removeChild($node);
            }
        }
    }
    
    /**
     * Process element attributes
     */
    private static function processAttributes(\DOMElement $element, array $options): void
    {
        $tagName = strtolower($element->tagName);
        $allowedAttrs = array_merge(
            self::ALLOWED_ATTRIBUTES['global'] ?? [],
            self::ALLOWED_ATTRIBUTES[$tagName] ?? []
        );
        
        $attributesToRemove = [];
        
        foreach ($element->attributes as $attr) {
            $attrName = strtolower($attr->name);
            $attrValue = $attr->value;
            
            // Check if attribute is allowed
            $isAllowed = false;
            foreach ($allowedAttrs as $allowed) {
                if ($allowed === $attrName || 
                    (str_ends_with($allowed, '*') && str_starts_with($attrName, rtrim($allowed, '*')))) {
                    $isAllowed = true;
                    break;
                }
            }
            
            if (!$isAllowed) {
                $attributesToRemove[] = $attrName;
                continue;
            }
            
            // Sanitize attribute values
            if ($attrName === 'href') {
                $cleanUrl = self::sanitizeURL($attrValue);
                if ($cleanUrl === null) {
                    $attributesToRemove[] = $attrName;
                } else {
                    $element->setAttribute($attrName, $cleanUrl);
                }
            } elseif ($attrName === 'style' && $options['sanitize_css']) {
                $cleanCSS = self::sanitizeCSS($attrValue);
                $element->setAttribute($attrName, $cleanCSS);
            } elseif (in_array($attrName, ['class', 'id'])) {
                // Sanitize class and id attributes
                $cleanValue = preg_replace('/[^\w\s\-_]/u', '', $attrValue);
                $element->setAttribute($attrName, $cleanValue);
            }
        }
        
        // Remove disallowed attributes
        foreach ($attributesToRemove as $attrName) {
            $element->removeAttribute($attrName);
        }
    }
    
    /**
     * Sanitize widget content specifically
     * 
     * @param array $content Widget content array
     * @return array Sanitized content
     */
    public static function sanitizeWidgetContent(array $content): array
    {
        $sanitized = [];
        
        foreach ($content as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeWidgetContent($value);
            } elseif (is_string($value)) {
                // Determine sanitization level based on field type
                if (in_array($key, ['text', 'heading_text', 'content', 'description'])) {
                    // Rich text fields
                    $sanitized[$key] = self::sanitizeHTML($value, 'widget');
                } elseif (in_array($key, ['url', 'link_url', 'href'])) {
                    // URL fields
                    $sanitized[$key] = self::sanitizeURL($value) ?? '';
                } elseif (in_array($key, ['class', 'css_classes', 'custom_css'])) {
                    // CSS fields
                    $sanitized[$key] = self::sanitizeCSS($value);
                } else {
                    // Plain text fields
                    $sanitized[$key] = self::sanitizeText($value);
                }
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Generate Content Security Policy header value
     * 
     * @param array $options CSP options
     * @return string CSP header value
     */
    public static function generateCSP(array $options = []): string
    {
        $defaults = [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline'",
            'style-src' => "'self' 'unsafe-inline'",
            'img-src' => "'self' data: https:",
            'font-src' => "'self' https:",
            'connect-src' => "'self'",
            'frame-ancestors' => "'none'",
            'base-uri' => "'self'",
            'form-action' => "'self'"
        ];
        
        $csp = array_merge($defaults, $options);
        
        $cspString = '';
        foreach ($csp as $directive => $value) {
            $cspString .= $directive . ' ' . $value . '; ';
        }
        
        return rtrim($cspString);
    }
    
    /**
     * Validate and sanitize file uploads
     * 
     * @param array $file Uploaded file info
     * @param array $allowedTypes Allowed MIME types
     * @return array|null Sanitized file info or null if invalid
     */
    public static function sanitizeFileUpload(array $file, array $allowedTypes = []): ?array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }
        
        // Default allowed types for page builder
        if (empty($allowedTypes)) {
            $allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'image/svg+xml', 'application/pdf'
            ];
        }
        
        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return null;
        }
        
        // Sanitize filename
        $filename = basename($file['name']);
        $filename = preg_replace('/[^\w\-_\.]/', '', $filename);
        
        return [
            'name' => $filename,
            'type' => $mimeType,
            'tmp_name' => $file['tmp_name'],
            'size' => $file['size'],
            'error' => $file['error']
        ];
    }
    
    /**
     * Check if content contains suspicious patterns
     * 
     * @param string $content Content to check
     * @return array Array of detected threats
     */
    public static function detectThreats(string $content): array
    {
        $threats = [];
        
        // Check for script injections
        if (preg_match('/<script/i', $content)) {
            $threats[] = 'script_injection';
        }
        
        // Check for javascript: protocols
        if (preg_match('/javascript:/i', $content)) {
            $threats[] = 'javascript_protocol';
        }
        
        // Check for event handlers
        if (preg_match('/\son\w+\s*=/i', $content)) {
            $threats[] = 'event_handler';
        }
        
        // Check for CSS expressions
        if (preg_match('/expression\s*\(/i', $content)) {
            $threats[] = 'css_expression';
        }
        
        // Check for data URIs with scripts
        if (preg_match('/data:[^;]*;.*javascript/i', $content)) {
            $threats[] = 'data_uri_script';
        }
        
        return $threats;
    }
}