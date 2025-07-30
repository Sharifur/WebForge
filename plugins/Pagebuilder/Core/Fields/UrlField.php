<?php

namespace Plugins\Pagebuilder\Core\Fields;

class UrlField extends TextField
{
    protected string $type = 'url';
    
    protected bool $validateUrl = true;
    
    public function setValidateUrl(bool $validate = true): static
    {
        $this->validateUrl = $validate;
        return $this;
    }
    
    protected function getTypeSpecificConfig(): array
    {
        return array_merge(parent::getTypeSpecificConfig(), [
            'validate_url' => $this->validateUrl,
        ]);
    }
}