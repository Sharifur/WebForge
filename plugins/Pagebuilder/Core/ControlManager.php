<?php

namespace Plugins\Pagebuilder\Core;

use Plugins\Pagebuilder\Core\Fields\BaseField;
use Plugins\Pagebuilder\Core\FieldTypes\FieldInterface;

/**
 * ControlManager - Fluent field registration and organization system
 * 
 * Provides a chainable API for registering fields, organizing them into groups and tabs,
 * and building the final field configuration for widgets. Supports conditional fields,
 * validation, and CSS selector management for style fields.
 * 
 * @package Plugins\Pagebuilder\Core
 */
class ControlManager
{
    /** @var array<string, mixed> */
    private array $fields = [];
    
    /** @var array<string, mixed> */
    private array $groups = [];
    
    /** @var array<string, mixed> */
    private array $tabs = [];
    
    /** @var string|null */
    private ?string $currentGroup = null;
    
    /** @var string|null */
    private ?string $currentTab = null;
    
    /** @var array<string, mixed> */
    private array $structure = [];

    /**
     * Register a field with the control manager
     *
     * @param string $id Unique field identifier
     * @param BaseField|FieldInterface $field Field instance
     * @return self
     */
    public function registerField(string $id, BaseField|FieldInterface $field): self
    {
        // Handle both BaseField and FieldInterface types
        if ($field instanceof BaseField) {
            $fieldConfig = $field->toArray();
        } elseif ($field instanceof FieldInterface) {
            $fieldConfig = method_exists($field, 'toArray') ? $field->toArray() : $field->render([], null);
        } else {
            throw new \InvalidArgumentException('Field must be instance of BaseField or FieldInterface');
        }
        
        if ($this->currentGroup) {
            if (!isset($this->structure['groups'])) {
                $this->structure['groups'] = [];
            }
            if (!isset($this->structure['groups'][$this->currentGroup])) {
                $this->structure['groups'][$this->currentGroup] = [
                    'type' => 'group',
                    'label' => $this->groups[$this->currentGroup]['label'] ?? '',
                    'fields' => []
                ];
            }
            $this->structure['groups'][$this->currentGroup]['fields'][$id] = $fieldConfig;
        } elseif ($this->currentTab) {
            if (!isset($this->structure['tabs'])) {
                $this->structure['tabs'] = [];
            }
            if (!isset($this->structure['tabs'][$this->currentTab])) {
                $this->structure['tabs'][$this->currentTab] = [
                    'label' => $this->tabs[$this->currentTab]['label'] ?? '',
                    'fields' => []
                ];
            }
            $this->structure['tabs'][$this->currentTab]['fields'][$id] = $fieldConfig;
        } else {
            $this->fields[$id] = $fieldConfig;
        }
        
        return $this;
    }

    /**
     * Start a new field group
     *
     * @param string $id Group identifier
     * @param string $label Group display label
     * @param array<string, mixed> $options Additional group options
     * @return self
     */
    public function addGroup(string $id, string $label, array $options = []): self
    {
        $this->currentGroup = $id;
        $this->groups[$id] = array_merge([
            'label' => $label,
            'collapsible' => false,
            'collapsed' => false,
            'border' => true,
            'description' => null
        ], $options);
        
        return $this;
    }

    /**
     * End the current field group
     *
     * @return self
     */
    public function endGroup(): self
    {
        $this->currentGroup = null;
        return $this;
    }

    /**
     * Add a new tab
     *
     * @param string $id Tab identifier
     * @param string $label Tab display label
     * @param array<string, mixed> $options Additional tab options
     * @return self
     */
    public function addTab(string $id, string $label, array $options = []): self
    {
        $this->currentTab = $id;
        $this->tabs[$id] = array_merge([
            'label' => $label,
            'icon' => null,
            'active' => false
        ], $options);
        
        return $this;
    }

    /**
     * End the current tab
     *
     * @return self
     */
    public function endTab(): self
    {
        $this->currentTab = null;
        return $this;
    }

    /**
     * Add a divider for visual separation
     *
     * @param string|null $label Optional divider label
     * @return self
     */
    public function addDivider(?string $label = null): self
    {
        $id = 'divider_' . uniqid();
        return $this->registerField($id, FieldManager::DIVIDER()->setLabel($label ?? ''));
    }

    /**
     * Add a heading for section organization
     *
     * @param string $title Heading text
     * @param string $size Heading size (h1-h6)
     * @return self
     */
    public function addHeading(string $title, string $size = 'h3'): self
    {
        $id = 'heading_' . uniqid();
        return $this->registerField($id, FieldManager::HEADING()
            ->setLabel($title)
            ->setSize($size)
        );
    }

    /**
     * Get the final field configuration
     *
     * @return array<string, mixed>
     */
    public function getFields(): array
    {
        $result = [];
        
        // Add standalone fields
        $result = array_merge($result, $this->fields);
        
        // Add grouped fields
        if (!empty($this->structure['groups'])) {
            foreach ($this->structure['groups'] as $groupId => $group) {
                $result[$groupId] = $group;
            }
        }
        
        // Add tabbed fields
        if (!empty($this->structure['tabs'])) {
            $result['_tabs'] = $this->structure['tabs'];
        }
        
        return $result;
    }

    /**
     * Get CSS from all fields with selectors
     *
     * @param string $widgetId Widget identifier for wrapper replacement
     * @param array<string, mixed> $fieldValues Current field values
     * @param string $breakpoint Current responsive breakpoint
     * @return string Generated CSS
     */
    public function generateCSS(string $widgetId, array $fieldValues, string $breakpoint = 'desktop'): string
    {
        $css = '';
        $fields = $this->getAllFieldsFlat();
        
        foreach ($fields as $fieldId => $fieldConfig) {
            if (isset($fieldConfig['selectors']) && !empty($fieldConfig['selectors'])) {
                $fieldValue = $fieldValues[$fieldId] ?? $fieldConfig['default'] ?? null;
                
                if ($fieldValue !== null) {
                    $css .= $this->generateFieldCSS(
                        $fieldConfig['selectors'],
                        $fieldValue,
                        $widgetId,
                        $fieldConfig,
                        $breakpoint
                    );
                }
            }
        }
        
        return $css;
    }

    /**
     * Generate CSS for a specific field
     *
     * @param array<string, string> $selectors Field selectors
     * @param mixed $value Field value
     * @param string $widgetId Widget ID
     * @param array<string, mixed> $fieldConfig Field configuration
     * @param string $breakpoint Responsive breakpoint
     * @return string Generated CSS
     */
    private function generateFieldCSS(
        array $selectors, 
        mixed $value, 
        string $widgetId, 
        array $fieldConfig, 
        string $breakpoint
    ): string {
        $css = '';
        $unit = $fieldConfig['unit'] ?? '';
        
        foreach ($selectors as $selector => $properties) {
            // Replace wrapper placeholder
            $processedSelector = str_replace('{{WRAPPER}}', "#{$widgetId}", $selector);
            
            // Process properties based on field type
            $processedProperties = $this->processProperties($properties, $value, $unit, $fieldConfig);
            
            if (!empty($processedProperties)) {
                $css .= "{$processedSelector} { {$processedProperties} }\n";
            }
        }
        
        return $css;
    }

    /**
     * Process CSS properties with placeholder replacement
     *
     * @param string $properties CSS properties template
     * @param mixed $value Field value
     * @param string $unit Field unit
     * @param array<string, mixed> $fieldConfig Field configuration
     * @return string Processed CSS properties
     */
    private function processProperties(string $properties, mixed $value, string $unit, array $fieldConfig): string
    {
        // Handle dimension fields (top, right, bottom, left)
        if ($fieldConfig['type'] === 'dimension' && is_array($value)) {
            $properties = str_replace('{{VALUE.TOP}}', (string)($value['top'] ?? 0), $properties);
            $properties = str_replace('{{VALUE.RIGHT}}', (string)($value['right'] ?? 0), $properties);
            $properties = str_replace('{{VALUE.BOTTOM}}', (string)($value['bottom'] ?? 0), $properties);
            $properties = str_replace('{{VALUE.LEFT}}', (string)($value['left'] ?? 0), $properties);
            $properties = str_replace('{{UNIT}}', $unit, $properties);
        } else {
            // Handle single value fields
            $properties = str_replace('{{VALUE}}', (string)$value, $properties);
            $properties = str_replace('{{UNIT}}', $unit, $properties);
        }
        
        return $properties;
    }

    /**
     * Get all fields flattened from groups and tabs
     *
     * @return array<string, mixed>
     */
    private function getAllFieldsFlat(): array
    {
        $allFields = $this->fields;
        
        // Add fields from groups
        foreach ($this->structure['groups'] ?? [] as $group) {
            if (isset($group['fields'])) {
                $allFields = array_merge($allFields, $group['fields']);
            }
        }
        
        // Add fields from tabs
        foreach ($this->structure['tabs'] ?? [] as $tab) {
            if (isset($tab['fields'])) {
                $allFields = array_merge($allFields, $tab['fields']);
            }
        }
        
        return $allFields;
    }

    /**
     * Reset the control manager state
     *
     * @return self
     */
    public function reset(): self
    {
        $this->fields = [];
        $this->groups = [];
        $this->tabs = [];
        $this->currentGroup = null;
        $this->currentTab = null;
        $this->structure = [];
        
        return $this;
    }

    /**
     * Export configuration as JSON
     *
     * @return string JSON representation
     */
    public function toJson(): string
    {
        return json_encode($this->getFields(), JSON_PRETTY_PRINT);
    }

    /**
     * Import configuration from array
     *
     * @param array<string, mixed> $config Configuration array
     * @return self
     */
    public function fromArray(array $config): self
    {
        $this->reset();
        $this->fields = $config;
        return $this;
    }
}