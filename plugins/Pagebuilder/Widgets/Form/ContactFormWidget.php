<?php

namespace Plugins\Pagebuilder\Widgets\Form;

use Plugins\Pagebuilder\Core\BaseWidget;
use Plugins\Pagebuilder\Core\WidgetCategory;

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
        return 'mail';
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
        return [
            'form_settings' => [
                'type' => 'group',
                'label' => 'Form Settings',
                'fields' => [
                    'form_title' => [
                        'type' => 'text',
                        'label' => 'Form Title',
                        'default' => 'Contact Us',
                        'placeholder' => 'Enter form title'
                    ],
                    'form_description' => [
                        'type' => 'textarea',
                        'label' => 'Form Description',
                        'placeholder' => 'Enter form description',
                        'rows' => 3
                    ],
                    'submit_button_text' => [
                        'type' => 'text',
                        'label' => 'Submit Button Text',
                        'default' => 'Send Message',
                        'required' => true
                    ]
                ]
            ],
            'form_fields' => [
                'type' => 'group',
                'label' => 'Form Fields',
                'fields' => [
                    'fields' => [
                        'type' => 'repeater',
                        'label' => 'Form Fields',
                        'min' => 1,
                        'max' => 20,
                        'fields' => [
                            'field_type' => [
                                'type' => 'select',
                                'label' => 'Field Type',
                                'options' => [
                                    'text' => 'Text Input',
                                    'email' => 'Email Input',
                                    'tel' => 'Phone Input',
                                    'textarea' => 'Textarea',
                                    'select' => 'Select Dropdown',
                                    'checkbox' => 'Checkbox',
                                    'radio' => 'Radio Buttons',
                                    'file' => 'File Upload',
                                    'date' => 'Date Picker',
                                    'number' => 'Number Input'
                                ],
                                'default' => 'text',
                                'required' => true
                            ],
                            'field_label' => [
                                'type' => 'text',
                                'label' => 'Field Label',
                                'required' => true,
                                'placeholder' => 'Enter field label'
                            ],
                            'field_name' => [
                                'type' => 'text',
                                'label' => 'Field Name',
                                'required' => true,
                                'placeholder' => 'field_name'
                            ],
                            'placeholder' => [
                                'type' => 'text',
                                'label' => 'Placeholder Text',
                                'condition' => ['field_type' => ['text', 'email', 'tel', 'textarea', 'number']]
                            ],
                            'required' => [
                                'type' => 'toggle',
                                'label' => 'Required Field',
                                'default' => false
                            ],
                            'field_options' => [
                                'type' => 'textarea',
                                'label' => 'Field Options (one per line)',
                                'placeholder' => "Option 1\nOption 2\nOption 3",
                                'condition' => ['field_type' => ['select', 'radio', 'checkbox']],
                                'rows' => 4
                            ],
                            'field_width' => [
                                'type' => 'select',
                                'label' => 'Field Width',
                                'options' => [
                                    'full' => 'Full Width',
                                    'half' => 'Half Width',
                                    'third' => 'One Third',
                                    'two-thirds' => 'Two Thirds'
                                ],
                                'default' => 'full'
                            ]
                        ]
                    ]
                ]
            ],
            'submission' => [
                'type' => 'group',
                'label' => 'Form Submission',
                'fields' => [
                    'action_type' => [
                        'type' => 'select',
                        'label' => 'Submission Action',
                        'options' => [
                            'email' => 'Send Email',
                            'database' => 'Save to Database',
                            'both' => 'Email & Database',
                            'webhook' => 'Send to Webhook'
                        ],
                        'default' => 'email'
                    ],
                    'recipient_email' => [
                        'type' => 'email',
                        'label' => 'Recipient Email',
                        'placeholder' => 'admin@example.com',
                        'condition' => ['action_type' => ['email', 'both']]
                    ],
                    'webhook_url' => [
                        'type' => 'text',
                        'label' => 'Webhook URL',
                        'placeholder' => 'https://example.com/webhook',
                        'condition' => ['action_type' => 'webhook']
                    ],
                    'success_message' => [
                        'type' => 'textarea',
                        'label' => 'Success Message',
                        'default' => 'Thank you for your message! We will get back to you soon.',
                        'rows' => 2
                    ],
                    'redirect_url' => [
                        'type' => 'text',
                        'label' => 'Redirect URL (optional)',
                        'placeholder' => 'https://example.com/thank-you'
                    ]
                ]
            ],
            'validation' => [
                'type' => 'group',
                'label' => 'Validation & Security',
                'fields' => [
                    'enable_captcha' => [
                        'type' => 'toggle',
                        'label' => 'Enable CAPTCHA',
                        'default' => true
                    ],
                    'captcha_type' => [
                        'type' => 'select',
                        'label' => 'CAPTCHA Type',
                        'options' => [
                            'recaptcha' => 'Google reCAPTCHA',
                            'hcaptcha' => 'hCaptcha',
                            'simple' => 'Simple Math'
                        ],
                        'default' => 'recaptcha',
                        'condition' => ['enable_captcha' => true]
                    ],
                    'honeypot' => [
                        'type' => 'toggle',
                        'label' => 'Enable Honeypot Protection',
                        'default' => true
                    ]
                ]
            ]
        ];
    }

    public function getStyleFields(): array
    {
        return [
            'form_layout' => [
                'type' => 'group',
                'label' => 'Form Layout',
                'fields' => [
                    'form_max_width' => [
                        'type' => 'number',
                        'label' => 'Max Width',
                        'unit' => 'px',
                        'min' => 300,
                        'max' => 1200,
                        'default' => 600
                    ],
                    'field_spacing' => [
                        'type' => 'number',
                        'label' => 'Field Spacing',
                        'unit' => 'px',
                        'min' => 5,
                        'max' => 50,
                        'default' => 20
                    ],
                    'form_alignment' => [
                        'type' => 'select',
                        'label' => 'Form Alignment',
                        'options' => [
                            'left' => 'Left',
                            'center' => 'Center',
                            'right' => 'Right'
                        ],
                        'default' => 'left'
                    ]
                ]
            ],
            'title_styling' => [
                'type' => 'group',
                'label' => 'Title Styling',
                'fields' => [
                    'title_color' => [
                        'type' => 'color',
                        'label' => 'Title Color',
                        'default' => '#1F2937'
                    ],
                    'title_font_size' => [
                        'type' => 'number',
                        'label' => 'Title Font Size',
                        'unit' => 'px',
                        'min' => 16,
                        'max' => 48,
                        'default' => 24
                    ],
                    'title_font_weight' => [
                        'type' => 'select',
                        'label' => 'Title Font Weight',
                        'options' => [
                            '400' => 'Normal',
                            '500' => 'Medium',
                            '600' => 'Semi Bold',
                            '700' => 'Bold'
                        ],
                        'default' => '600'
                    ]
                ]
            ],
            'field_styling' => [
                'type' => 'group',
                'label' => 'Field Styling',
                'fields' => [
                    'label_color' => [
                        'type' => 'color',
                        'label' => 'Label Color',
                        'default' => '#374151'
                    ],
                    'label_font_size' => [
                        'type' => 'number',
                        'label' => 'Label Font Size',
                        'unit' => 'px',
                        'min' => 12,
                        'max' => 20,
                        'default' => 14
                    ],
                    'input_background' => [
                        'type' => 'color',
                        'label' => 'Input Background',
                        'default' => '#FFFFFF'
                    ],
                    'input_border_color' => [
                        'type' => 'color',
                        'label' => 'Input Border Color',
                        'default' => '#D1D5DB'
                    ],
                    'input_border_radius' => [
                        'type' => 'number',
                        'label' => 'Input Border Radius',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 20,
                        'default' => 6
                    ],
                    'input_padding' => [
                        'type' => 'number',
                        'label' => 'Input Padding',
                        'unit' => 'px',
                        'min' => 8,
                        'max' => 20,
                        'default' => 12
                    ],
                    'focus_border_color' => [
                        'type' => 'color',
                        'label' => 'Focus Border Color',
                        'default' => '#3B82F6'
                    ]
                ]
            ],
            'button_styling' => [
                'type' => 'group',
                'label' => 'Submit Button',
                'fields' => [
                    'button_background' => [
                        'type' => 'color',
                        'label' => 'Button Background',
                        'default' => '#3B82F6'
                    ],
                    'button_text_color' => [
                        'type' => 'color',
                        'label' => 'Button Text Color',
                        'default' => '#FFFFFF'
                    ],
                    'button_hover_background' => [
                        'type' => 'color',
                        'label' => 'Button Hover Background',
                        'default' => '#2563EB'
                    ],
                    'button_font_size' => [
                        'type' => 'number',
                        'label' => 'Button Font Size',
                        'unit' => 'px',
                        'min' => 14,
                        'max' => 20,
                        'default' => 16
                    ],
                    'button_padding' => [
                        'type' => 'spacing',
                        'label' => 'Button Padding',
                        'default' => '12px 24px'
                    ],
                    'button_border_radius' => [
                        'type' => 'number',
                        'label' => 'Button Border Radius',
                        'unit' => 'px',
                        'min' => 0,
                        'max' => 20,
                        'default' => 6
                    ],
                    'button_width' => [
                        'type' => 'select',
                        'label' => 'Button Width',
                        'options' => [
                            'auto' => 'Auto',
                            'full' => 'Full Width',
                            'half' => 'Half Width'
                        ],
                        'default' => 'auto'
                    ]
                ]
            ],
            'messages' => [
                'type' => 'group',
                'label' => 'Messages',
                'fields' => [
                    'success_color' => [
                        'type' => 'color',
                        'label' => 'Success Message Color',
                        'default' => '#10B981'
                    ],
                    'error_color' => [
                        'type' => 'color',
                        'label' => 'Error Message Color',
                        'default' => '#EF4444'
                    ],
                    'message_background' => [
                        'type' => 'color',
                        'label' => 'Message Background',
                        'default' => '#F9FAFB'
                    ]
                ]
            ]
        ];
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];
        
        $formTitle = $general['form_settings']['form_title'] ?? 'Contact Us';
        $formDescription = $general['form_settings']['form_description'] ?? '';
        $submitButtonText = $general['form_settings']['submit_button_text'] ?? 'Send Message';
        $fields = $general['form_fields']['fields'] ?? [];
        
        if (empty($fields)) {
            return '<div class="form-placeholder">Add form fields to display contact form</div>';
        }
        
        $classes = ['widget-contact-form'];
        $classString = implode(' ', $classes);
        
        $styles = [];
        if (isset($style['form_layout']['form_max_width'])) {
            $styles[] = 'max-width: ' . $style['form_layout']['form_max_width'] . 'px';
        }
        if (isset($style['form_layout']['form_alignment'])) {
            $alignment = $style['form_layout']['form_alignment'];
            if ($alignment === 'center') {
                $styles[] = 'margin: 0 auto';
            } elseif ($alignment === 'right') {
                $styles[] = 'margin-left: auto';
            }
        }
        
        $styleString = !empty($styles) ? 'style="' . implode('; ', $styles) . '"' : '';
        
        $html = "<div class=\"{$classString}\" {$styleString}>";
        
        if ($formTitle) {
            $titleStyles = [];
            if (isset($style['title_styling']['title_color'])) {
                $titleStyles[] = 'color: ' . $style['title_styling']['title_color'];
            }
            if (isset($style['title_styling']['title_font_size'])) {
                $titleStyles[] = 'font-size: ' . $style['title_styling']['title_font_size'] . 'px';
            }
            
            $titleStyleString = !empty($titleStyles) ? 'style="' . implode('; ', $titleStyles) . '"' : '';
            $html .= "<h2 class=\"form-title\" {$titleStyleString}>{$formTitle}</h2>";
        }
        
        if ($formDescription) {
            $html .= "<p class=\"form-description\">{$formDescription}</p>";
        }
        
        $html .= '<form class="contact-form" method="POST" action="/api/forms/submit">';
        
        foreach ($fields as $field) {
            $html .= $this->renderField($field, $style);
        }
        
        // Add CAPTCHA if enabled
        if ($general['validation']['enable_captcha'] ?? true) {
            $html .= '<div class="form-field captcha-field">';
            $html .= '<div class="captcha-placeholder">CAPTCHA verification</div>';
            $html .= '</div>';
        }
        
        // Submit button
        $buttonStyles = [];
        if (isset($style['button_styling']['button_background'])) {
            $buttonStyles[] = 'background-color: ' . $style['button_styling']['button_background'];
        }
        if (isset($style['button_styling']['button_text_color'])) {
            $buttonStyles[] = 'color: ' . $style['button_styling']['button_text_color'];
        }
        
        $buttonStyleString = !empty($buttonStyles) ? 'style="' . implode('; ', $buttonStyles) . '"' : '';
        
        $html .= "<div class=\"form-field submit-field\">";
        $html .= "<button type=\"submit\" class=\"submit-button\" {$buttonStyleString}>{$submitButtonText}</button>";
        $html .= "</div>";
        
        $html .= '</form>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function renderField(array $field, array $style): string
    {
        $fieldType = $field['field_type'] ?? 'text';
        $fieldLabel = $field['field_label'] ?? '';
        $fieldName = $field['field_name'] ?? '';
        $placeholder = $field['placeholder'] ?? '';
        $required = $field['required'] ?? false;
        $fieldWidth = $field['field_width'] ?? 'full';
        
        $fieldClasses = ['form-field', "field-width-{$fieldWidth}"];
        if ($required) {
            $fieldClasses[] = 'required';
        }
        
        $fieldClassString = implode(' ', $fieldClasses);
        
        $html = "<div class=\"{$fieldClassString}\">";
        
        if ($fieldLabel) {
            $requiredMark = $required ? ' <span class="required-mark">*</span>' : '';
            $html .= "<label for=\"{$fieldName}\">{$fieldLabel}{$requiredMark}</label>";
        }
        
        switch ($fieldType) {
            case 'textarea':
                $html .= "<textarea id=\"{$fieldName}\" name=\"{$fieldName}\" placeholder=\"{$placeholder}\"" . 
                        ($required ? ' required' : '') . "></textarea>";
                break;
            case 'select':
                $options = explode("\n", $field['field_options'] ?? '');
                $html .= "<select id=\"{$fieldName}\" name=\"{$fieldName}\"" . ($required ? ' required' : '') . ">";
                $html .= "<option value=\"\">Choose an option...</option>";
                foreach ($options as $option) {
                    $option = trim($option);
                    if ($option) {
                        $html .= "<option value=\"{$option}\">{$option}</option>";
                    }
                }
                $html .= "</select>";
                break;
            default:
                $html .= "<input type=\"{$fieldType}\" id=\"{$fieldName}\" name=\"{$fieldName}\" " .
                        "placeholder=\"{$placeholder}\"" . ($required ? ' required' : '') . ">";
                break;
        }
        
        $html .= "</div>";
        
        return $html;
    }
}