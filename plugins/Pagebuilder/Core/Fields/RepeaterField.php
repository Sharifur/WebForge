<?php

namespace Plugins\Pagebuilder\Core\Fields;

class RepeaterField extends BaseField
{
    protected string $type = 'repeater';
    protected array $fields = [];
    protected int $min = 0;
    protected int $max = 100;
    
    public function setFields(array $fields): static
    {
        $this->fields = $fields;
        return $this;
    }
    
    public function setMin(int $min): static
    {
        $this->min = $min;
        return $this;
    }
    
    public function setMax(int $max): static
    {
        $this->max = $max;
        return $this;
    }
    
    protected function getTypeSpecificConfig(): array
    {
        // Convert field objects to arrays
        $processedFields = [];
        foreach ($this->fields as $key => $field) {
            if (is_object($field) && method_exists($field, 'toArray')) {
                $processedFields[$key] = $field->toArray();
            } else {
                $processedFields[$key] = $field;
            }
        }
        
        return [
            'fields' => $processedFields,
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}