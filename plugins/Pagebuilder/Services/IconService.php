<?php

namespace Plugins\Pagebuilder\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class IconService
{
    private const JSON_FILE_PATH = 'assets/line-awesome-icons-complete.json';
    private const CACHE_KEY = 'line_awesome_icons';
    private const CACHE_DURATION = 3600; // 1 hour

    /**
     * Get all available Line Awesome icons
     */
    public function getAllIcons(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return $this->loadIconsFromJSON();
        });
    }

    /**
     * Get icons by category
     */
    public function getIconsByCategory(string $category): array
    {
        $allIcons = $this->getAllIcons();

        return array_filter($allIcons, function ($icon) use ($category) {
            return isset($icon['category']) && $icon['category'] === $category;
        });
    }

    /**
     * Search icons by name or keywords
     */
    public function searchIcons(string $query): array
    {
        $allIcons = $this->getAllIcons();
        $query = strtolower($query);

        return array_filter($allIcons, function ($icon) use ($query) {
            return str_contains(strtolower($icon['name']), $query) ||
                   str_contains(strtolower($icon['cssClass']), $query) ||
                   !empty(array_filter($icon['keywords'], function ($keyword) use ($query) {
                       return str_contains(strtolower($keyword), $query);
                   }));
        });
    }

    /**
     * Validate if an icon class is valid
     */
    public function validateIcon(string $iconClass): bool
    {
        $allIcons = $this->getAllIcons();

        foreach ($allIcons as $icon) {
            if ($icon['cssClass'] === $iconClass) { // Changed from 'iconClass' to 'cssClass' to match JSON structure
                return true;
            }
        }

        return false;
    }

    /**
     * Load icons from JSON file
     */
    private function loadIconsFromJSON(): array
    {
        try {
            $jsonPath = public_path(self::JSON_FILE_PATH);

            if (!File::exists($jsonPath)) {
                return [];
            }

            $jsonContent = File::get($jsonPath);
            $data = json_decode($jsonContent, true);

            if (!$data || !isset($data['icons']) || !is_array($data['icons'])) {
                return [];
            }

            return $data['icons'];

        } catch (\Exception $e) {
            \Log::error('Failed to load icons from JSON: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Determine the icon type based on the icon name and context
     */
    private function determineIconType(string $iconName): string
    {
        // Brand icons (social media, companies, etc.)
        $brandPatterns = [
            'facebook', 'twitter', 'instagram', 'linkedin', 'github', 'google', 'apple', 'microsoft',
            'youtube', 'whatsapp', 'telegram', 'discord', 'slack', 'dropbox', 'amazon', 'paypal',
            'stripe', 'visa', 'mastercard', 'bitcoin', 'android', 'ios', 'windows', 'linux',
            'chrome', 'firefox', 'safari', 'opera', 'edge', 'wordpress', 'drupal', 'joomla',
            'shopify', 'woocommerce', 'magento', 'prestashop', 'opencart', 'bootstrap', 'jquery',
            'react', 'vue', 'angular', 'node', 'npm', 'yarn', 'webpack', 'docker', 'git',
            'cc-', 'fab', 'brand'
        ];

        foreach ($brandPatterns as $pattern) {
            if (str_contains($iconName, $pattern)) {
                return 'brand';
            }
        }

        // Regular (outline) icons
        $regularPatterns = [
            'calendar', 'clock', 'heart', 'star', 'bookmark', 'user', 'envelope', 'file',
            'folder', 'image', 'bell', 'comment', 'thumbs', 'hand-', 'circle', 'square',
            'flag', 'eye', 'lightbulb', 'smile', 'frown', 'meh'
        ];

        foreach ($regularPatterns as $pattern) {
            if (str_contains($iconName, $pattern) && (str_contains($iconName, '-o') || str_contains($iconName, 'outline'))) {
                return 'regular';
            }
        }

        // Default to solid
        return 'solid';
    }

    /**
     * Build the appropriate icon class based on type
     */
    private function buildIconClass(string $iconType, string $iconName): string
    {
        switch ($iconType) {
            case 'brand':
                return "lab la-{$iconName}";
            case 'regular':
                return "lar la-{$iconName}";
            case 'solid':
            default:
                return "las la-{$iconName}";
        }
    }

    /**
     * Format icon name for display
     */
    private function formatDisplayName(string $iconName): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $iconName));
    }

    /**
     * Categorize icon based on its name (optional categorization)
     */
    private function categorizeIcon(string $iconName): array
    {
        $categories = [];

        // Social Media & Brands (highest priority)
        if (preg_match('/(facebook|twitter|instagram|linkedin|youtube|github|google|apple|microsoft|amazon|paypal|stripe|visa|mastercard|bitcoin|whatsapp|telegram|discord|slack|dropbox|wordpress|drupal|joomla|bootstrap|react|vue|angular|node|npm)/', $iconName)) {
            $categories[] = 'social';
        }

        // Interface & Navigation
        if (preg_match('/(home|menu|bars|cog|settings|gear|search|filter|sort|list|grid|table|dashboard|window|desktop|mobile|tablet|laptop|navigation|nav)/', $iconName)) {
            $categories[] = 'interface';
        }

        // Communication
        if (preg_match('/(mail|envelope|phone|message|comment|chat|bell|notification|signal|wifi|bluetooth|call|contact)/', $iconName)) {
            $categories[] = 'communication';
        }

        // Media & Entertainment
        if (preg_match('/(play|pause|stop|volume|music|video|camera|image|photo|film|microphone|headphones|speaker|media|movie)/', $iconName)) {
            $categories[] = 'media';
        }

        // Arrows & Direction
        if (preg_match('/(arrow|chevron|angle|caret|direction|up|down|left|right|forward|backward|next|prev)/', $iconName)) {
            $categories[] = 'arrows';
        }

        // Business & Finance
        if (preg_match('/(dollar|money|credit|card|bank|briefcase|chart|graph|analytics|report|invoice|business|finance|shopping|cart)/', $iconName)) {
            $categories[] = 'business';
        }

        // Files & Documents
        if (preg_match('/(file|document|folder|archive|download|upload|save|copy|paste|cut|pdf|word|excel|zip)/', $iconName)) {
            $categories[] = 'files';
        }

        // Users & People
        if (preg_match('/(user|users|person|people|profile|account|avatar|team|group|member|contact)/', $iconName)) {
            $categories[] = 'users';
        }

        // Tools & Utilities
        if (preg_match('/(tool|wrench|hammer|screwdriver|key|lock|unlock|shield|security|bug|code|config|admin|utility)/', $iconName)) {
            $categories[] = 'tools';
        }

        // Transportation
        if (preg_match('/(car|truck|plane|ship|train|bus|bicycle|motorcycle|taxi|transport)/', $iconName)) {
            $categories[] = 'transport';
        }

        // Food & Drink
        if (preg_match('/(coffee|beer|wine|food|restaurant|pizza|burger|drink|kitchen|utensils)/', $iconName)) {
            $categories[] = 'food';
        }

        // Health & Medical
        if (preg_match('/(hospital|medical|doctor|health|heart|medicine|pill|stethoscope|ambulance|first-aid)/', $iconName)) {
            $categories[] = 'medical';
        }

        // Sports & Recreation
        if (preg_match('/(sport|football|basketball|tennis|golf|swimming|running|fitness|game|trophy)/', $iconName)) {
            $categories[] = 'sports';
        }

        // Weather & Nature
        if (preg_match('/(sun|moon|cloud|rain|snow|wind|weather|tree|leaf|flower|nature)/', $iconName)) {
            $categories[] = 'weather';
        }

        // Education & Learning
        if (preg_match('/(book|education|school|university|graduation|student|teacher|learn|library)/', $iconName)) {
            $categories[] = 'education';
        }

        // If no specific category matches, don't assign any (categories are optional)
        return array_unique($categories);
    }

    /**
     * Generate searchable keywords for an icon
     */
    private function generateKeywords(string $iconName): array
    {
        $keywords = [];

        // Add the icon name parts as keywords
        $nameParts = explode('-', $iconName);
        $keywords = array_merge($keywords, $nameParts);

        // Add synonyms based on common icon meanings
        $synonymMap = [
            'home' => ['house', 'main', 'start'],
            'user' => ['person', 'profile', 'account'],
            'mail' => ['email', 'message', 'letter'],
            'phone' => ['call', 'contact', 'telephone'],
            'search' => ['find', 'look', 'magnify'],
            'heart' => ['love', 'like', 'favorite'],
            'star' => ['favorite', 'rating', 'bookmark'],
            'trash' => ['delete', 'remove', 'bin'],
            'edit' => ['modify', 'change', 'update'],
            'plus' => ['add', 'create', 'new'],
            'minus' => ['remove', 'subtract', 'delete'],
            'check' => ['confirm', 'approve', 'tick'],
            'times' => ['close', 'cancel', 'x'],
        ];

        foreach ($nameParts as $part) {
            if (isset($synonymMap[$part])) {
                $keywords = array_merge($keywords, $synonymMap[$part]);
            }
        }

        return array_unique($keywords);
    }

    /**
     * Get popular/commonly used icons
     */
    public function getPopularIcons(): array
    {
        $popularIconNames = [
            'home', 'user', 'search', 'heart', 'star', 'envelope', 'phone', 'cog',
            'edit', 'trash-alt', 'plus', 'minus', 'check', 'times', 'arrow-right',
            'arrow-left', 'arrow-up', 'arrow-down', 'calendar', 'clock', 'map-marker-alt',
            'camera', 'image', 'file', 'folder', 'download', 'upload', 'share',
            'facebook', 'twitter', 'instagram', 'linkedin', 'github', 'google',
            'shopping-cart', 'credit-card', 'lock', 'unlock', 'eye', 'eye-slash',
            'thumbs-up', 'thumbs-down', 'bookmark', 'bell', 'comment', 'lightbulb'
        ];

        $allIcons = $this->getAllIcons();
        $popularIcons = [];

        foreach ($popularIconNames as $iconName) {
            foreach ($allIcons as $icon) {
                if ($icon['name'] === $iconName) { // Changed from 'iconName' to 'name' to match JSON structure
                    $popularIcons[] = $icon;
                    break;
                }
            }
        }

        return $popularIcons;
    }

    /**
     * Get available categories from JSON file
     */
    public function getCategories(): array
    {
        try {
            $jsonPath = public_path(self::JSON_FILE_PATH);

            if (!File::exists($jsonPath)) {
                return $this->getDefaultCategories();
            }

            $jsonContent = File::get($jsonPath);
            $data = json_decode($jsonContent, true);

            if (!$data || !isset($data['categories'])) {
                return $this->getDefaultCategories();
            }

            // Return categories directly from JSON (key = category value, value = display name)
            return $data['categories'];

        } catch (\Exception $e) {
            \Log::error('Failed to load categories from JSON: ' . $e->getMessage());
            return $this->getDefaultCategories();
        }
    }

    /**
     * Get default categories as fallback
     */
    private function getDefaultCategories(): array
    {
        return [
            'accessibility' => 'Accessibility',
            'arrows' => 'Arrows & Directions',
            'brand' => 'Brand & Social Media',
            'communication' => 'Communication',
            'emotions' => 'Emotions',
            'files' => 'Files & Documents',
            'food' => 'Food & Drink',
            'interface' => 'Interface & Navigation',
            'medical' => 'Health & Medical',
            'misc' => 'Miscellaneous',
            'science' => 'Science & Technology',
            'text' => 'Text & Typography',
            'transportation' => 'Transportation',
        ];
    }

    /**
     * Format category name for display
     */
    private function formatCategoryDisplayName(string $category): string
    {
        $categoryNames = [
            'accessibility' => 'Accessibility',
            'arrows' => 'Arrows & Directions',
            'brand' => 'Brand & Social Media',
            'communication' => 'Communication',
            'emotions' => 'Emotions',
            'files' => 'Files & Documents',
            'food' => 'Food & Drink',
            'interface' => 'Interface & Navigation',
            'medical' => 'Health & Medical',
            'misc' => 'Miscellaneous',
            'science' => 'Science & Technology',
            'text' => 'Text & Typography',
            'transportation' => 'Transportation',
        ];

        return $categoryNames[$category] ?? ucwords(str_replace(['-', '_'], ' ', $category));
    }

    /**
     * Clear the icons cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}