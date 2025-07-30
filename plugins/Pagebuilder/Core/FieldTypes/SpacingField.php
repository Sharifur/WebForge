<?php

namespace Plugins\Pagebuilder\Core\FieldTypes;

class SpacingField extends AbstractField
{
    protected string $type = 'spacing';
    protected mixed $defaultValue = '0px 0px 0px 0px';

    public function getType(): string
    {
        return $this->type;
    }

    public function validate($value, array $rules = []): array
    {
        $errors = $this->validateCommon($value, $rules);

        if ($value !== null && $value !== '') {
            // For responsive spacing, value might be an object
            if (is_array($value) || is_object($value)) {
                $value = (array) $value;
                
                // Validate each responsive breakpoint
                foreach (['desktop', 'tablet', 'mobile'] as $breakpoint) {
                    if (isset($value[$breakpoint])) {
                        $spacingErrors = $this->validateSpacingString($value[$breakpoint]);
                        if (!empty($spacingErrors)) {
                            $errors[] = "Invalid {$breakpoint} spacing: " . implode(', ', $spacingErrors);
                        }
                    }
                }
            } else {
                // Single spacing string
                $spacingErrors = $this->validateSpacingString($value);
                $errors = array_merge($errors, $spacingErrors);
            }
        }

        return $errors;
    }

    private function validateSpacingString(string $spacing): array
    {
        $errors = [];
        
        // Parse spacing string (e.g., "10px 15px 10px 15px")
        $parts = explode(' ', trim($spacing));
        
        if (count($parts) > 4) {
            $errors[] = 'Too many spacing values (maximum 4)';
            return $errors;
        }

        foreach ($parts as $part) {
            if (!preg_match('/^\d+(\.\d+)?(px|em|rem|%|vh|vw)$/', $part)) {
                $errors[] = "Invalid spacing value: {$part}";
            }
        }

        return $errors;
    }

    public function sanitize($value): mixed
    {
        if ($value === null || $value === '') {
            return $this->defaultValue;
        }

        // Handle responsive spacing
        if (is_array($value) || is_object($value)) {
            $value = (array) $value;
            $sanitized = [];
            
            foreach (['desktop', 'tablet', 'mobile'] as $breakpoint) {
                if (isset($value[$breakpoint])) {
                    $sanitized[$breakpoint] = $this->sanitizeSpacingString($value[$breakpoint]);
                } else {
                    $sanitized[$breakpoint] = $this->defaultValue;
                }
            }
            
            return $sanitized;
        }

        // Single spacing string
        return $this->sanitizeSpacingString((string) $value);
    }

    private function sanitizeSpacingString(string $spacing): string
    {
        $spacing = trim($spacing);
        
        // Parse and normalize spacing values
        $parts = explode(' ', $spacing);
        $normalizedParts = [];
        
        foreach ($parts as $part) {
            $part = trim($part);
            if (preg_match('/^(\d+(?:\.\d+)?)(px|em|rem|%|vh|vw)?$/', $part, $matches)) {
                $value = $matches[1];
                $unit = $matches[2] ?? 'px';
                $normalizedParts[] = $value . $unit;
            }
        }
        
        if (empty($normalizedParts)) {
            return $this->defaultValue;
        }
        
        // CSS shorthand normalization
        switch (count($normalizedParts)) {
            case 1:
                return implode(' ', array_fill(0, 4, $normalizedParts[0]));
            case 2:
                return $normalizedParts[0] . ' ' . $normalizedParts[1] . ' ' . 
                       $normalizedParts[0] . ' ' . $normalizedParts[1];
            case 3:
                return $normalizedParts[0] . ' ' . $normalizedParts[1] . ' ' . 
                       $normalizedParts[2] . ' ' . $normalizedParts[1];
            case 4:
                return implode(' ', $normalizedParts);
            default:
                return implode(' ', array_slice($normalizedParts, 0, 4));
        }
    }

    public function render(array $config, $value = null): array
    {
        return [
            'type' => 'spacing',
            'value' => $value ?? $this->defaultValue,
            'responsive' => $config['responsive'] ?? false,
            'units' => $config['units'] ?? ['px', 'em', 'rem', '%'],
            'linked' => $config['linked'] ?? false,
            'sides' => $config['sides'] ?? ['top', 'right', 'bottom', 'left'],
            'min' => $config['min'] ?? 0,
            'max' => $config['max'] ?? 1000,
            'step' => $config['step'] ?? 1,
            'required' => $config['required'] ?? false,
            'disabled' => $config['disabled'] ?? false,
            'className' => $config['class_name'] ?? 'form-spacing',
            'showLabels' => $config['show_labels'] ?? true,
            'attributes' => $config['attributes'] ?? []
        ];
    }

    public function getSchema(): array
    {
        return array_merge($this->getCommonSchema(), [
            'properties' => [
                'responsive' => [
                    'type' => 'boolean',
                    'description' => 'Enable responsive spacing controls',
                    'default' => false
                ],
                'units' => [
                    'type' => 'array',
                    'description' => 'Available units',
                    'items' => ['type' => 'string'],
                    'default' => ['px', 'em', 'rem', '%']
                ],
                'linked' => [
                    'type' => 'boolean',
                    'description' => 'Link all sides together',
                    'default' => false
                ],
                'sides' => [
                    'type' => 'array',
                    'description' => 'Which sides to show',
                    'items' => ['type' => 'string'],
                    'default' => ['top', 'right', 'bottom', 'left']
                ],
                'min' => [
                    'type' => 'number',
                    'description' => 'Minimum value',
                    'default' => 0
                ],
                'max' => [
                    'type' => 'number',
                    'description' => 'Maximum value',
                    'default' => 1000
                ]
            ]
        ]);
    }
}