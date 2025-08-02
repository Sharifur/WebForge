<?php

namespace App\Utils;

/**
 * URLHandler - Comprehensive URL processing and validation utility
 * 
 * Built on top of XSSProtection for security, this class provides advanced
 * URL handling capabilities specifically for the page builder system.
 * 
 * Features:
 * - URL validation and normalization
 * - Protocol detection and filtering
 * - Social media URL extraction
 * - Relative/absolute URL conversion
 * - URL parameter manipulation
 * - Click tracking and analytics support
 * - SEO-friendly URL generation
 * - Accessibility enhancements
 * 
 * @package App\Utils
 * @author PageBuilder Team
 * @version 1.0.0
 */
class URLHandler
{
    /**
     * Supported URL schemes for different contexts
     */
    const SCHEMES = [
        'web' => ['http', 'https'],
        'email' => ['mailto'],
        'phone' => ['tel', 'sms'],
        'file' => ['file'],
        'social' => ['http', 'https'],
        'all' => ['http', 'https', 'mailto', 'tel', 'sms', 'ftp']
    ];
    
    /**
     * Social media domain patterns
     */
    const SOCIAL_DOMAINS = [
        'facebook.com' => 'facebook',
        'instagram.com' => 'instagram',
        'twitter.com' => 'twitter',
        'x.com' => 'twitter',
        'linkedin.com' => 'linkedin',
        'youtube.com' => 'youtube',
        'tiktok.com' => 'tiktok',
        'pinterest.com' => 'pinterest',
        'github.com' => 'github',
        'telegram.org' => 'telegram',
        'whatsapp.com' => 'whatsapp'
    ];
    
    /**
     * URL type detection patterns
     */
    const URL_PATTERNS = [
        'email' => '/^mailto:[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,}$/i',
        'phone' => '/^(tel:|sms:)[\+]?[\d\s\-\(\)]+$/i',
        'anchor' => '/^#[\w\-]+$/',
        'relative' => '/^[^\/]*\//',
        'absolute' => '/^https?:\/\//',
        'protocol_relative' => '/^\/\//'
    ];
    
    /**
     * Validate and sanitize a URL for widget use
     * 
     * @param string $url Raw URL input
     * @param array $options Validation options
     * @return array URL validation result
     */
    public static function validateURL(string $url, array $options = []): array
    {
        $defaults = [
            'allowed_schemes' => self::SCHEMES['all'],
            'allow_relative' => true,
            'allow_anchors' => true,
            'require_scheme' => false,
            'context' => 'general'
        ];
        
        $options = array_merge($defaults, $options);
        $originalUrl = $url;
        $url = trim($url);
        
        $result = [
            'valid' => false,
            'sanitized_url' => '',
            'original_url' => $originalUrl,
            'type' => 'unknown',
            'scheme' => '',
            'domain' => '',
            'path' => '',
            'errors' => [],
            'warnings' => [],
            'metadata' => []
        ];
        
        // Empty URL handling
        if (empty($url)) {
            $result['errors'][] = 'URL cannot be empty';
            return $result;
        }
        
        // Detect URL type
        $result['type'] = self::detectURLType($url);
        
        // Handle different URL types
        switch ($result['type']) {
            case 'anchor':
                if (!$options['allow_anchors']) {
                    $result['errors'][] = 'Anchor links are not allowed in this context';
                    return $result;
                }
                $result['valid'] = true;
                $result['sanitized_url'] = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
                break;
                
            case 'email':
                $result = self::validateEmailURL($url, $result);
                break;
                
            case 'phone':
                $result = self::validatePhoneURL($url, $result);
                break;
                
            case 'relative':
                if (!$options['allow_relative']) {
                    $result['errors'][] = 'Relative URLs are not allowed in this context';
                    return $result;
                }
                $result['valid'] = true;
                $result['sanitized_url'] = self::sanitizeRelativeURL($url);
                break;
                
            case 'absolute':
            case 'protocol_relative':
                $result = self::validateAbsoluteURL($url, $options, $result);
                break;
                
            default:
                // Try to fix common URL issues
                $fixedUrl = self::attemptURLFix($url);
                if ($fixedUrl !== $url) {
                    $result['warnings'][] = 'URL was automatically corrected';
                    return self::validateURL($fixedUrl, $options);
                }
                $result['errors'][] = 'Invalid URL format';
        }
        
        // Additional security check using XSSProtection
        if ($result['valid']) {
            $secureUrl = XSSProtection::sanitizeURL($result['sanitized_url'], $options['allowed_schemes']);
            if ($secureUrl === null) {
                $result['valid'] = false;
                $result['errors'][] = 'URL failed security validation';
            } else {
                $result['sanitized_url'] = $secureUrl;
            }
        }
        
        return $result;
    }
    
    /**
     * Detect the type of URL
     */
    private static function detectURLType(string $url): string
    {
        foreach (self::URL_PATTERNS as $type => $pattern) {
            if (preg_match($pattern, $url)) {
                return $type;
            }
        }
        return 'unknown';
    }
    
    /**
     * Validate email URLs
     */
    private static function validateEmailURL(string $url, array $result): array
    {
        if (preg_match('/^mailto:([\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,})$/i', $url, $matches)) {
            $email = $matches[1];
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['valid'] = true;
                $result['sanitized_url'] = 'mailto:' . $email;
                $result['scheme'] = 'mailto';
                $result['metadata']['email'] = $email;
            } else {
                $result['errors'][] = 'Invalid email address in mailto URL';
            }
        } else {
            $result['errors'][] = 'Invalid mailto URL format';
        }
        return $result;
    }
    
    /**
     * Validate phone URLs
     */
    private static function validatePhoneURL(string $url, array $result): array
    {
        if (preg_match('/^(tel:|sms:)([\+]?[\d\s\-\(\)]+)$/i', $url, $matches)) {
            $scheme = strtolower($matches[1]);
            $number = preg_replace('/[^\d\+]/', '', $matches[2]);
            
            if (!empty($number)) {
                $result['valid'] = true;
                $result['sanitized_url'] = $scheme . $number;
                $result['scheme'] = rtrim($scheme, ':');
                $result['metadata']['phone'] = $number;
            } else {
                $result['errors'][] = 'Invalid phone number format';
            }
        } else {
            $result['errors'][] = 'Invalid phone URL format';
        }
        return $result;
    }
    
    /**
     * Sanitize relative URLs
     */
    private static function sanitizeRelativeURL(string $url): string
    {
        // Remove dangerous characters and normalize
        $url = preg_replace('/[^\w\-._~:\/\?#\[\]@!$&\'()*+,;=%]/', '', $url);
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate absolute URLs
     */
    private static function validateAbsoluteURL(string $url, array $options, array $result): array
    {
        // Handle protocol-relative URLs
        if (strpos($url, '//') === 0) {
            $url = 'https:' . $url;
        }
        
        $parsed = parse_url($url);
        if ($parsed === false) {
            $result['errors'][] = 'Malformed URL';
            return $result;
        }
        
        // Validate scheme
        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, $options['allowed_schemes'])) {
            $result['errors'][] = "Scheme '{$scheme}' is not allowed";
            return $result;
        }
        
        // Validate domain
        $domain = strtolower($parsed['host'] ?? '');
        if (empty($domain)) {
            $result['errors'][] = 'Missing domain';
            return $result;
        }
        
        $result['valid'] = true;
        $result['scheme'] = $scheme;
        $result['domain'] = $domain;
        $result['path'] = $parsed['path'] ?? '/';
        $result['sanitized_url'] = self::reconstructURL($parsed);
        
        // Add metadata
        $result['metadata']['social_platform'] = self::detectSocialPlatform($domain);
        $result['metadata']['is_external'] = !self::isInternalDomain($domain);
        
        return $result;
    }
    
    /**
     * Attempt to fix common URL issues
     */
    private static function attemptURLFix(string $url): string
    {
        // Add scheme if missing for common domains
        if (!preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
            if (preg_match('/^(www\.)?[\w\-]+\.[\w]{2,}/i', $url)) {
                return 'https://' . $url;
            }
        }
        
        // Fix common typos
        $fixes = [
            'http://' => 'https://',
            'wwww.' => 'www.',
            '.com/' => '.com',
            '.org/' => '.org'
        ];
        
        foreach ($fixes as $wrong => $correct) {
            if (stripos($url, $wrong) !== false) {
                return str_ireplace($wrong, $correct, $url);
            }
        }
        
        return $url;
    }
    
    /**
     * Reconstruct URL from parsed components
     */
    private static function reconstructURL(array $parsed): string
    {
        $url = '';
        
        if (isset($parsed['scheme'])) {
            $url .= $parsed['scheme'] . '://';
        }
        
        if (isset($parsed['user'])) {
            $url .= $parsed['user'];
            if (isset($parsed['pass'])) {
                $url .= ':' . $parsed['pass'];
            }
            $url .= '@';
        }
        
        if (isset($parsed['host'])) {
            $url .= $parsed['host'];
        }
        
        if (isset($parsed['port'])) {
            $url .= ':' . $parsed['port'];
        }
        
        if (isset($parsed['path'])) {
            $url .= $parsed['path'];
        }
        
        if (isset($parsed['query'])) {
            $url .= '?' . $parsed['query'];
        }
        
        if (isset($parsed['fragment'])) {
            $url .= '#' . $parsed['fragment'];
        }
        
        return $url;
    }
    
    /**
     * Detect social media platform from domain
     */
    private static function detectSocialPlatform(string $domain): ?string
    {
        $domain = strtolower($domain);
        
        foreach (self::SOCIAL_DOMAINS as $socialDomain => $platform) {
            if (strpos($domain, $socialDomain) !== false) {
                return $platform;
            }
        }
        
        return null;
    }
    
    /**
     * Check if domain is internal (same site)
     */
    private static function isInternalDomain(string $domain): bool
    {
        $currentDomain = $_SERVER['HTTP_HOST'] ?? '';
        return strtolower($domain) === strtolower($currentDomain);
    }
    
    /**
     * Generate SEO-friendly attributes for a link
     * 
     * @param array $urlData Validated URL data
     * @param array $options Link options
     * @return array HTML attributes
     */
    public static function generateLinkAttributes(array $urlData, array $options = []): array
    {
        $defaults = [
            'target' => '_self',
            'rel' => [],
            'track_clicks' => false,
            'add_nofollow' => false,
            'add_noopener' => true,
            'custom_attributes' => []
        ];
        
        $options = array_merge($defaults, $options);
        $attributes = ['href' => $urlData['sanitized_url']];
        
        // Target handling
        if ($options['target'] !== '_self') {
            $attributes['target'] = $options['target'];
        }
        
        // Auto-detect external links and add appropriate rel attributes
        if ($urlData['metadata']['is_external'] ?? false) {
            if ($options['add_noopener'] && $options['target'] === '_blank') {
                $options['rel'][] = 'noopener';
            }
            
            if ($options['add_nofollow']) {
                $options['rel'][] = 'nofollow';
            }
        }
        
        // Add rel attributes
        if (!empty($options['rel'])) {
            $attributes['rel'] = implode(' ', array_unique($options['rel']));
        }
        
        // Add tracking attributes
        if ($options['track_clicks']) {
            $attributes['data-track'] = 'click';
            $attributes['data-url-type'] = $urlData['type'];
            
            if (isset($urlData['metadata']['social_platform'])) {
                $attributes['data-social-platform'] = $urlData['metadata']['social_platform'];
            }
        }
        
        // Add accessibility attributes for external links
        if ($urlData['metadata']['is_external'] ?? false) {
            $attributes['aria-label'] = ($attributes['aria-label'] ?? '') . ' (opens in new window)';
        }
        
        // Merge custom attributes
        $attributes = array_merge($attributes, $options['custom_attributes']);
        
        return $attributes;
    }
    
    /**
     * Convert URL to different formats for various use cases
     * 
     * @param string $url Source URL
     * @param string $format Target format
     * @return string Converted URL
     */
    public static function convertURL(string $url, string $format): string
    {
        $validated = self::validateURL($url);
        
        if (!$validated['valid']) {
            return $url;
        }
        
        switch ($format) {
            case 'absolute':
                return self::makeAbsolute($validated['sanitized_url']);
                
            case 'relative':
                return self::makeRelative($validated['sanitized_url']);
                
            case 'canonical':
                return self::makeCanonical($validated['sanitized_url']);
                
            case 'tracking':
                return self::addTracking($validated['sanitized_url']);
                
            default:
                return $validated['sanitized_url'];
        }
    }
    
    /**
     * Make URL absolute
     */
    private static function makeAbsolute(string $url): string
    {
        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        }
        
        if (strpos($url, '/') === 0) {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            return $scheme . '://' . $host . $url;
        }
        
        return $url;
    }
    
    /**
     * Make URL relative
     */
    private static function makeRelative(string $url): string
    {
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';
        $parsed = parse_url($url);
        
        if (isset($parsed['host']) && $parsed['host'] === $currentHost) {
            $relative = $parsed['path'] ?? '/';
            
            if (isset($parsed['query'])) {
                $relative .= '?' . $parsed['query'];
            }
            
            if (isset($parsed['fragment'])) {
                $relative .= '#' . $parsed['fragment'];
            }
            
            return $relative;
        }
        
        return $url;
    }
    
    /**
     * Make URL canonical (remove tracking parameters, etc.)
     */
    private static function makeCanonical(string $url): string
    {
        $parsed = parse_url($url);
        
        if (isset($parsed['query'])) {
            // Remove common tracking parameters
            $trackingParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'fbclid', 'gclid'];
            parse_str($parsed['query'], $params);
            
            foreach ($trackingParams as $param) {
                unset($params[$param]);
            }
            
            $parsed['query'] = !empty($params) ? http_build_query($params) : null;
        }
        
        // Remove fragment for canonical URLs
        unset($parsed['fragment']);
        
        return self::reconstructURL($parsed);
    }
    
    /**
     * Add tracking parameters to URL
     */
    private static function addTracking(string $url): string
    {
        $parsed = parse_url($url);
        
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $params);
        } else {
            $params = [];
        }
        
        // Add basic tracking
        $params['pb_source'] = 'widget';
        $params['pb_time'] = time();
        
        $parsed['query'] = http_build_query($params);
        
        return self::reconstructURL($parsed);
    }
    
    /**
     * Extract URLs from text content
     * 
     * @param string $text Text to scan for URLs
     * @return array Found URLs with metadata
     */
    public static function extractURLs(string $text): array
    {
        $urls = [];
        
        // Pattern for various URL types
        $patterns = [
            'web' => '/https?:\/\/[^\s<>"\']+/i',
            'email' => '/mailto:[^\s<>"\']+/i',
            'phone' => '/(tel|sms):[^\s<>"\']+/i'
        ];
        
        foreach ($patterns as $type => $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[0] as $match) {
                    $validated = self::validateURL($match);
                    if ($validated['valid']) {
                        $urls[] = [
                            'url' => $validated['sanitized_url'],
                            'type' => $type,
                            'position' => strpos($text, $match),
                            'length' => strlen($match),
                            'metadata' => $validated['metadata']
                        ];
                    }
                }
            }
        }
        
        return $urls;
    }
    
    /**
     * Generate accessibility-friendly link text
     * 
     * @param string $url Target URL
     * @param string $text Original link text
     * @return string Enhanced link text
     */
    public static function generateAccessibleLinkText(string $url, string $text): string
    {
        $validated = self::validateURL($url);
        
        if (!$validated['valid']) {
            return $text;
        }
        
        $enhancements = [];
        
        // Add context for different URL types
        switch ($validated['type']) {
            case 'email':
                $enhancements[] = '(email)';
                break;
                
            case 'phone':
                $enhancements[] = '(phone)';
                break;
                
            case 'absolute':
                if ($validated['metadata']['is_external']) {
                    $enhancements[] = '(external link)';
                }
                
                if ($platform = $validated['metadata']['social_platform'] ?? null) {
                    $enhancements[] = "({$platform})";
                }
                break;
        }
        
        if (!empty($enhancements)) {
            $text .= ' ' . implode(' ', $enhancements);
        }
        
        return $text;
    }
    
    /**
     * Render a complete HTML link from URL field settings
     * 
     * This method takes the enhanced URL field settings and renders
     * a complete, secure, and accessible HTML link element.
     * 
     * @param array $urlSettings URL field settings from widget
     * @param string $linkText The link text content
     * @param array $options Additional rendering options
     * @return string Complete HTML link element
     */
    public static function renderLink(array $urlSettings, string $linkText = '', array $options = []): string
    {
        $defaults = [
            'escape_text' => true,
            'enable_xss_protection' => true,
            'fallback_href' => '#',
            'add_wrapper' => false,
            'wrapper_class' => 'url-link-wrapper',
            'link_class' => '',
            'auto_accessibility' => true
        ];
        
        $options = array_merge($defaults, $options);
        
        // Extract URL and settings
        $url = $urlSettings['url'] ?? '';
        $target = $urlSettings['target'] ?? '_self';
        $rel = $urlSettings['rel'] ?? [];
        $download = $urlSettings['download'] ?? false;
        $downloadFilename = $urlSettings['download_filename'] ?? '';
        $ariaLabel = $urlSettings['aria_label'] ?? '';
        $title = $urlSettings['title'] ?? '';
        $trackClicks = $urlSettings['track_clicks'] ?? false;
        $trackingCategory = $urlSettings['tracking_category'] ?? '';
        $customAttributes = $urlSettings['custom_attributes'] ?? [];
        
        // Validate URL
        if (empty($url)) {
            if ($options['fallback_href']) {
                $url = $options['fallback_href'];
            } else {
                return $options['escape_text'] ? htmlspecialchars($linkText, ENT_QUOTES, 'UTF-8') : $linkText;
            }
        }
        
        // Use XSS protection if enabled
        if ($options['enable_xss_protection']) {
            $sanitizedUrl = XSSProtection::sanitizeURL($url);
            if ($sanitizedUrl === null) {
                $url = $options['fallback_href'];
            } else {
                $url = $sanitizedUrl;
            }
        }
        
        // Validate URL with our handler
        $validated = self::validateURL($url);
        if (!$validated['valid']) {
            $url = $options['fallback_href'];
        }
        
        // Generate link attributes
        $linkOptions = [
            'target' => $target,
            'rel' => is_array($rel) ? $rel : [],
            'track_clicks' => $trackClicks,
            'add_nofollow' => in_array('nofollow', is_array($rel) ? $rel : []),
            'add_noopener' => $target === '_blank' || in_array('noopener', is_array($rel) ? $rel : []),
            'custom_attributes' => []
        ];
        
        $attributes = self::generateLinkAttributes($validated, $linkOptions);
        
        // Add additional attributes
        if (!empty($title)) {
            $attributes['title'] = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        }
        
        if (!empty($ariaLabel)) {
            $attributes['aria-label'] = htmlspecialchars($ariaLabel, ENT_QUOTES, 'UTF-8');
        }
        
        // Handle download attribute
        if ($download) {
            if (!empty($downloadFilename)) {
                $attributes['download'] = htmlspecialchars($downloadFilename, ENT_QUOTES, 'UTF-8');
            } else {
                $attributes['download'] = '';
            }
        }
        
        // Add tracking attributes
        if ($trackClicks) {
            $attributes['data-track'] = 'click';
            if (!empty($trackingCategory)) {
                $attributes['data-track-category'] = htmlspecialchars($trackingCategory, ENT_QUOTES, 'UTF-8');
            }
            if (isset($validated['metadata']['social_platform'])) {
                $attributes['data-social-platform'] = $validated['metadata']['social_platform'];
            }
        }
        
        // Add custom CSS class
        if (!empty($options['link_class'])) {
            $currentClass = $attributes['class'] ?? '';
            $attributes['class'] = trim($currentClass . ' ' . $options['link_class']);
        }
        
        // Process custom attributes from repeater field
        if (is_array($customAttributes) && !empty($customAttributes)) {
            foreach ($customAttributes as $customAttr) {
                $attrName = $customAttr['attribute_name'] ?? '';
                $attrValue = $customAttr['attribute_value'] ?? '';
                
                // Validate attribute name (basic security check)
                if (!empty($attrName) && !empty($attrValue)) {
                    // Sanitize attribute name - only allow alphanumeric, hyphens, underscores
                    $attrName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $attrName);
                    
                    // Skip if attribute name is empty after sanitization
                    if (empty($attrName)) {
                        continue;
                    }
                    
                    // Prevent overriding critical attributes
                    $protectedAttributes = ['href', 'target', 'rel', 'download', 'aria-label', 'title'];
                    if (in_array(strtolower($attrName), $protectedAttributes)) {
                        continue;
                    }
                    
                    // Sanitize attribute value
                    $attrValue = htmlspecialchars($attrValue, ENT_QUOTES, 'UTF-8');
                    
                    // Add to attributes array
                    $attributes[$attrName] = $attrValue;
                }
            }
        }
        
        // Auto-enhance accessibility
        if ($options['auto_accessibility']) {
            $linkText = self::generateAccessibleLinkText($url, $linkText);
            
            // Add screen reader context for external links
            if ($validated['metadata']['is_external'] ?? false) {
                if (empty($attributes['aria-label'])) {
                    $attributes['aria-label'] = trim($linkText . ' (opens in new window)');
                }
            }
        }
        
        // Escape link text
        if ($options['escape_text']) {
            $linkText = htmlspecialchars($linkText, ENT_QUOTES, 'UTF-8');
        }
        
        // Build attributes string
        $attributesString = self::buildAttributesString($attributes);
        
        // Create the link element
        $linkElement = "<a{$attributesString}>{$linkText}</a>";
        
        // Add wrapper if requested
        if ($options['add_wrapper']) {
            $wrapperClass = htmlspecialchars($options['wrapper_class'], ENT_QUOTES, 'UTF-8');
            $linkElement = "<div class=\"{$wrapperClass}\">{$linkElement}</div>";
        }
        
        return $linkElement;
    }
    
    /**
     * Render multiple links from an array of URL settings
     * 
     * @param array $urlSettingsArray Array of URL field settings
     * @param array $linkTexts Array of link texts (or single text for all)
     * @param array $options Rendering options
     * @return string HTML with multiple links
     */
    public static function renderLinks(array $urlSettingsArray, $linkTexts = [], array $options = []): string
    {
        $defaults = [
            'separator' => ' ',
            'wrapper_tag' => '',
            'wrapper_class' => 'url-links-container',
            'item_wrapper' => '',
            'item_class' => 'url-link-item'
        ];
        
        $options = array_merge($defaults, $options);
        $links = [];
        
        foreach ($urlSettingsArray as $index => $urlSettings) {
            $linkText = '';
            
            // Determine link text
            if (is_array($linkTexts)) {
                $linkText = $linkTexts[$index] ?? "Link " . ($index + 1);
            } elseif (is_string($linkTexts)) {
                $linkText = $linkTexts;
            }
            
            $link = self::renderLink($urlSettings, $linkText, $options);
            
            // Add item wrapper if specified
            if (!empty($options['item_wrapper'])) {
                $itemClass = htmlspecialchars($options['item_class'], ENT_QUOTES, 'UTF-8');
                $tag = $options['item_wrapper'];
                $link = "<{$tag} class=\"{$itemClass}\">{$link}</{$tag}>";
            }
            
            $links[] = $link;
        }
        
        $result = implode($options['separator'], $links);
        
        // Add container wrapper if specified
        if (!empty($options['wrapper_tag'])) {
            $wrapperClass = htmlspecialchars($options['wrapper_class'], ENT_QUOTES, 'UTF-8');
            $tag = $options['wrapper_tag'];
            $result = "<{$tag} class=\"{$wrapperClass}\">{$result}</{$tag}>";
        }
        
        return $result;
    }
    
    /**
     * Build HTML attributes string from array
     * 
     * @param array $attributes Attributes array
     * @return string HTML attributes string
     */
    private static function buildAttributesString(array $attributes): string
    {
        $attributesString = '';
        
        foreach ($attributes as $attr => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attributesString .= ' ' . $attr;
                }
            } elseif (is_array($value)) {
                // Handle array values (like rel attribute)
                $stringValue = implode(' ', $value);
                if (!empty($stringValue)) {
                    $attributesString .= ' ' . $attr . '="' . htmlspecialchars($stringValue, ENT_QUOTES, 'UTF-8') . '"';
                }
            } elseif ($value !== null && $value !== '') {
                $attributesString .= ' ' . $attr . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        
        return $attributesString;
    }
    
    /**
     * Quick render method for simple links
     * 
     * @param string $url The URL
     * @param string $text Link text
     * @param array $options Simple options
     * @return string HTML link
     */
    public static function quickLink(string $url, string $text, array $options = []): string
    {
        $urlSettings = [
            'url' => $url,
            'target' => $options['target'] ?? '_self',
            'rel' => $options['rel'] ?? [],
            'title' => $options['title'] ?? '',
            'aria_label' => $options['aria_label'] ?? ''
        ];
        
        return self::renderLink($urlSettings, $text, $options);
    }
    
    /**
     * Render a button-style link
     * 
     * @param array $urlSettings URL field settings
     * @param string $buttonText Button text
     * @param array $options Button options
     * @return string HTML button-link
     */
    public static function renderButton(array $urlSettings, string $buttonText, array $options = []): string
    {
        $defaults = [
            'button_class' => 'btn btn-primary',
            'button_style' => '',
            'add_wrapper' => false,
            'wrapper_class' => 'button-wrapper'
        ];
        
        $options = array_merge($defaults, $options);
        
        // Set button-specific link class
        $options['link_class'] = $options['button_class'];
        
        // Add inline styles if provided
        if (!empty($options['button_style'])) {
            $urlSettings['style'] = $options['button_style'];
        }
        
        return self::renderLink($urlSettings, $buttonText, $options);
    }
}