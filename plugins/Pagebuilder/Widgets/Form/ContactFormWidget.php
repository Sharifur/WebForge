<?php

namespace Plugins\Pagebuilder\Widgets\Form;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;
use Plugins\Pagebuilder\Core\ControlManager;
use Plugins\Pagebuilder\Core\FieldManager;

class ContactFormWidget extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'contact_form';
    }

    protected function getWidgetName(): string
    {
        return 'Contact Form';
    }

    protected function getWidgetIcon(): string
    {
        return 'las la-envelope';
    }

    protected function getWidgetDescription(): string
    {
        return 'A customizable contact form with various field types and validation';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::FORM;
    }

    protected function getWidgetTags(): array
    {
        return ['form', 'contact', 'email', 'fields', 'validation', 'submission'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        
        // Form Settings Group
        $control->addGroup('form_settings', 'Form Settings')
            ->registerField('form_title', FieldManager::TEXT()
                ->setLabel('Form Title')
                ->setDefault('Contact Us')
                ->setPlaceholder('Enter form title')
            )
            ->registerField('form_description', FieldManager::TEXTAREA()
                ->setLabel('Form Description')
                ->setPlaceholder('Enter form description')
                ->setRows(3)
            )
            ->registerField('submit_button_text', FieldManager::TEXT()
                ->setLabel('Submit Button Text')
                ->setDefault('Send Message')
                ->setRequired(true)
            )
            ->endGroup();
            
        // Form Fields Group  
        $control->addGroup('form_fields', 'Form Fields')
            ->registerField('fields', FieldManager::REPEATER()
                ->setLabel('Form Fields')
                ->setMin(1)
                ->setMax(20)
                ->setDefault([
                    ['field_type' => 'text', 'field_label' => 'Name', 'field_name' => 'name', 'required' => true],
                    ['field_type' => 'email', 'field_label' => 'Email', 'field_name' => 'email', 'required' => true],
                    ['field_type' => 'textarea', 'field_label' => 'Message', 'field_name' => 'message', 'required' => true]
                ])
                ->setFields([
                    'field_type' => FieldManager::SELECT()
                        ->setLabel('Field Type')
                        ->setOptions([
                            'text' => 'Text Input',
                            'email' => 'Email Input',
                            'tel' => 'Phone Input',
                            'textarea' => 'Textarea',
                            'select' => 'Select Dropdown',
                            'checkbox' => 'Checkbox',
                            'radio' => 'Radio Buttons',
                            'date' => 'Date Picker',
                            'number' => 'Number Input'
                        ])
                        ->setDefault('text')
                        ->setRequired(true),
                    'field_label' => FieldManager::TEXT()
                        ->setLabel('Field Label')
                        ->setRequired(true)
                        ->setPlaceholder('Enter field label'),
                    'field_name' => FieldManager::TEXT()
                        ->setLabel('Field Name')
                        ->setRequired(true)
                        ->setPlaceholder('field_name'),
                    'placeholder' => FieldManager::TEXT()
                        ->setLabel('Placeholder Text')
                        ->setCondition(['field_type' => ['text', 'email', 'tel', 'textarea', 'number']]),
                    'required' => FieldManager::TOGGLE()
                        ->setLabel('Required Field')
                        ->setDefault(false),
                    'field_options' => FieldManager::TEXTAREA()
                        ->setLabel('Field Options (one per line)')
                        ->setPlaceholder("Option 1\nOption 2\nOption 3")
                        ->setCondition(['field_type' => ['select', 'radio', 'checkbox']])
                        ->setRows(4),
                    'field_width' => FieldManager::SELECT()
                        ->setLabel('Field Width')
                        ->setOptions([
                            'full' => 'Full Width',
                            'half' => 'Half Width',
                            'third' => 'One Third',
                            'two-thirds' => 'Two Thirds'
                        ])
                        ->setDefault('full')
                ])
            )
            ->endGroup();
            
        // Form Submission Group
        $control->addGroup('submission', 'Form Submission')
            ->registerField('action_type', FieldManager::SELECT()
                ->setLabel('Submission Action')
                ->setOptions([
                    'email' => 'Send Email',
                    'database' => 'Save to Database',
                    'both' => 'Email & Database',
                    'webhook' => 'Send to Webhook'
                ])
                ->setDefault('email')
            )
            ->registerField('recipient_email', FieldManager::EMAIL()
                ->setLabel('Recipient Email')
                ->setPlaceholder('admin@example.com')
                ->setCondition(['action_type' => ['email', 'both']])
            )
            ->registerField('webhook_url', FieldManager::URL()
                ->setLabel('Webhook URL')
                ->setPlaceholder('https://example.com/webhook')
                ->setCondition(['action_type' => 'webhook'])
            )
            ->registerField('success_message', FieldManager::TEXTAREA()
                ->setLabel('Success Message')
                ->setDefault('Thank you for your message! We will get back to you soon.')
                ->setRows(3)
            )
            ->registerField('redirect_url', FieldManager::URL()
                ->setLabel('Redirect URL (optional)')
                ->setPlaceholder('https://example.com/thank-you')
            )
            ->endGroup();
            
        // Security Group
        $control->addGroup('security', 'Security')
            ->registerField('enable_captcha', FieldManager::TOGGLE()
                ->setLabel('Enable CAPTCHA')
                ->setDefault(true)
            )
            ->registerField('captcha_type', FieldManager::SELECT()
                ->setLabel('CAPTCHA Type')
                ->setOptions([
                    'recaptcha' => 'Google reCAPTCHA',
                    'hcaptcha' => 'hCaptcha',
                    'simple' => 'Simple Math'
                ])
                ->setDefault('recaptcha')
                ->setCondition(['enable_captcha' => true])
            )
            ->registerField('honeypot', FieldManager::TOGGLE()
                ->setLabel('Enable Honeypot Protection')
                ->setDefault(true)
            )
            ->endGroup();
            
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        
        // Form Layout Group
        $control->addGroup('form_layout', 'Form Layout')
            ->registerField('form_max_width', FieldManager::NUMBER()
                ->setLabel('Max Width')
                ->setUnit('px')
                ->setMin(300)
                ->setMax(1200)
                ->setDefault(600)
            )
            ->registerField('field_spacing', FieldManager::NUMBER()
                ->setLabel('Field Spacing')
                ->setUnit('px')
                ->setMin(5)
                ->setMax(50)
                ->setDefault(20)
            )
            ->registerField('form_alignment', FieldManager::SELECT()
                ->setLabel('Form Alignment')
                ->setOptions([
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right'
                ])
                ->setDefault('left')
            )
            ->endGroup();
            
        // Title Styling Group
        $control->addGroup('title_styling', 'Title Styling')
            ->registerField('title_color', FieldManager::COLOR()
                ->setLabel('Title Color')
                ->setDefault('#1F2937')
            )
            ->registerField('title_font_size', FieldManager::NUMBER()
                ->setLabel('Title Font Size')
                ->setUnit('px')
                ->setMin(16)
                ->setMax(48)
                ->setDefault(24)
            )
            ->registerField('title_font_weight', FieldManager::SELECT()
                ->setLabel('Title Font Weight')
                ->setOptions([
                    '400' => 'Normal',
                    '500' => 'Medium',
                    '600' => 'Semi Bold',
                    '700' => 'Bold'
                ])
                ->setDefault('600')
            )
            ->endGroup();
            
        // Field Styling Group
        $control->addGroup('field_styling', 'Field Styling')
            ->registerField('label_color', FieldManager::COLOR()
                ->setLabel('Label Color')
                ->setDefault('#374151')
            )
            ->registerField('input_background', FieldManager::COLOR()
                ->setLabel('Input Background')
                ->setDefault('#FFFFFF')
            )
            ->registerField('input_border_color', FieldManager::COLOR()
                ->setLabel('Input Border Color')
                ->setDefault('#D1D5DB')
            )
            ->registerField('input_border_radius', FieldManager::NUMBER()
                ->setLabel('Input Border Radius')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(20)
                ->setDefault(6)
            )
            ->registerField('input_padding', FieldManager::DIMENSION()
                ->setLabel('Input Padding')
                ->setDefault(['top' => 12, 'right' => 16, 'bottom' => 12, 'left' => 16])
                ->setUnits(['px', 'em'])
            )
            ->endGroup();
            
        // Button Styling Group
        $control->addGroup('button_styling', 'Button Styling')
            ->registerField('button_background', FieldManager::COLOR()
                ->setLabel('Button Background')
                ->setDefault('#3B82F6')
            )
            ->registerField('button_text_color', FieldManager::COLOR()
                ->setLabel('Button Text Color')
                ->setDefault('#FFFFFF')
            )
            ->registerField('button_hover_background', FieldManager::COLOR()
                ->setLabel('Button Hover Background')
                ->setDefault('#2563EB')
            )
            ->registerField('button_border_radius', FieldManager::NUMBER()
                ->setLabel('Button Border Radius')
                ->setUnit('px')
                ->setMin(0)
                ->setMax(20)
                ->setDefault(6)
            )
            ->registerField('button_padding', FieldManager::DIMENSION()
                ->setLabel('Button Padding')
                ->setDefault(['top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24])
                ->setUnits(['px', 'em'])
            )
            ->endGroup();
            
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        $formSettings = $general['form_settings'] ?? [];
        $formTitle = $formSettings['form_title'] ?? 'Contact Us';
        $formDescription = $formSettings['form_description'] ?? '';
        $submitButtonText = $formSettings['submit_button_text'] ?? 'Send Message';
        
        $fields = $general['form_fields']['fields'] ?? [
            ['field_type' => 'text', 'field_label' => 'Name', 'field_name' => 'name', 'required' => true],
            ['field_type' => 'email', 'field_label' => 'Email', 'field_name' => 'email', 'required' => true],
            ['field_type' => 'textarea', 'field_label' => 'Message', 'field_name' => 'message', 'required' => true]
        ];
        
        $html = '<div class="widget-contact-form">';
        
        // Form title
        if (!empty($formTitle)) {
            $html .= '<h3 class="form-title">' . htmlspecialchars($formTitle) . '</h3>';
        }
        
        // Form description
        if (!empty($formDescription)) {
            $html .= '<p class="form-description">' . htmlspecialchars($formDescription) . '</p>';
        }
        
        $html .= '<form class="contact-form" method="POST" action="#">';
        
        // Render form fields
        foreach ($fields as $field) {
            $html .= $this->renderFormField($field);
        }
        
        // Submit button
        $html .= '<div class="form-field">';
        $html .= '<button type="submit" class="submit-button">' . htmlspecialchars($submitButtonText) . '</button>';
        $html .= '</div>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function renderFormField(array $field): string
    {
        $fieldType = $field['field_type'] ?? 'text';
        $fieldLabel = $field['field_label'] ?? '';
        $fieldName = $field['field_name'] ?? '';
        $placeholder = $field['placeholder'] ?? '';
        $required = $field['required'] ?? false;
        $fieldWidth = $field['field_width'] ?? 'full';
        
        $requiredAttr = $required ? 'required' : '';
        $requiredMark = $required ? '<span class="required">*</span>' : '';
        
        $html = '<div class="form-field field-width-' . $fieldWidth . '">';
        
        if (!empty($fieldLabel)) {
            $html .= '<label for="' . htmlspecialchars($fieldName) . '">' . htmlspecialchars($fieldLabel) . $requiredMark . '</label>';
        }
        
        switch ($fieldType) {
            case 'textarea':
                $html .= '<textarea name="' . htmlspecialchars($fieldName) . '" id="' . htmlspecialchars($fieldName) . '" placeholder="' . htmlspecialchars($placeholder) . '" ' . $requiredAttr . ' rows="5"></textarea>';
                break;
            case 'select':
                $options = explode("\n", $field['field_options'] ?? '');
                $html .= '<select name="' . htmlspecialchars($fieldName) . '" id="' . htmlspecialchars($fieldName) . '" ' . $requiredAttr . '>';
                $html .= '<option value="">Choose an option</option>';
                foreach ($options as $option) {
                    $option = trim($option);
                    if (!empty($option)) {
                        $html .= '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
                    }
                }
                $html .= '</select>';
                break;
            default:
                $html .= '<input type="' . htmlspecialchars($fieldType) . '" name="' . htmlspecialchars($fieldName) . '" id="' . htmlspecialchars($fieldName) . '" placeholder="' . htmlspecialchars($placeholder) . '" ' . $requiredAttr . '>';
                break;
        }
        
        $html .= '</div>';
        
        return $html;
    }
}