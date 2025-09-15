import React from 'react';
import TextFieldComponent from '../../Fields/TextFieldComponent';
import TextareaFieldComponent from '../../Fields/TextareaFieldComponent';
import SelectFieldComponent from '../../Fields/SelectFieldComponent';
import ToggleFieldComponent from '../../Fields/ToggleFieldComponent';
import NumberFieldComponent from '../../Fields/NumberFieldComponent';
import CheckboxFieldComponent from '../../Fields/CheckboxFieldComponent';

const SectionAdvancedSettings = ({ container, onUpdate, onWidgetUpdate }) => {
  const updateSetting = (path, value) => {
    const pathArray = path.split('.');
    
    onUpdate(prev => ({
      ...prev,
      containers: prev.containers.map(c =>
        c.id === container.id
          ? {
              ...c,
              settings: {
                ...c.settings,
                [pathArray[pathArray.length - 1]]: value
              }
            }
          : c
      )
    }));

    onWidgetUpdate({
      ...container,
      settings: {
        ...container.settings,
        [pathArray[pathArray.length - 1]]: value
      }
    });
  };

  return (
    <div className="p-4">
      <div className="space-y-6">
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Visibility</h4>
          <div className="space-y-4">
            <div>
              <ToggleFieldComponent
                fieldKey="visible"
                fieldConfig={{
                  label: 'Section Visible',
                  default: true
                }}
                value={container.settings?.visible !== false}
                onChange={(value) => updateSetting('settings.visible', value)}
              />
            </div>
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Custom CSS</h4>
          <div className="space-y-4">
            <div>
              <TextFieldComponent
                fieldKey="css_class"
                fieldConfig={{
                  label: 'CSS Class',
                  placeholder: 'custom-class-name',
                  default: ''
                }}
                value={container.settings?.cssClass || ''}
                onChange={(value) => updateSetting('settings.cssClass', value)}
              />
            </div>

            <div>
              <TextareaFieldComponent
                fieldKey="custom_css"
                fieldConfig={{
                  label: 'Custom CSS',
                  placeholder: '/* Custom CSS rules */',
                  rows: 6,
                  default: ''
                }}
                value={container.settings?.customCSS || ''}
                onChange={(value) => updateSetting('settings.customCSS', value)}
              />
            </div>
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Animation</h4>
          <div className="space-y-4">
            <div>
              <SelectFieldComponent
                fieldKey="animation"
                fieldConfig={{
                  label: 'Animation Type',
                  options: {
                    'none': 'None',
                    'fade-in': 'Fade In',
                    'slide-up': 'Slide Up',
                    'slide-down': 'Slide Down',
                    'slide-left': 'Slide Left',
                    'slide-right': 'Slide Right',
                    'zoom-in': 'Zoom In',
                    'bounce': 'Bounce'
                  },
                  default: 'none'
                }}
                value={container.settings?.animation || 'none'}
                onChange={(value) => updateSetting('settings.animation', value)}
              />
            </div>

            {container.settings?.animation && container.settings.animation !== 'none' && (
              <div>
                <NumberFieldComponent
                  fieldKey="animation_duration"
                  fieldConfig={{
                    label: 'Animation Duration (ms)',
                    min: 100,
                    max: 3000,
                    step: 100,
                    default: 500,
                    placeholder: '500'
                  }}
                  value={container.settings?.animationDuration || 500}
                  onChange={(value) => updateSetting('settings.animationDuration', value)}
                />
              </div>
            )}
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Responsive Settings</h4>
          <div className="space-y-4">
            <div className="grid grid-cols-3 gap-4">
              <div>
                <ToggleFieldComponent
                  fieldKey="show_desktop"
                  fieldConfig={{
                    label: 'Show on Desktop',
                    default: true
                  }}
                  value={container.settings?.hideOnDesktop !== true}
                  onChange={(value) => updateSetting('settings.hideOnDesktop', !value)}
                />
              </div>
              <div>
                <ToggleFieldComponent
                  fieldKey="show_tablet"
                  fieldConfig={{
                    label: 'Show on Tablet',
                    default: true
                  }}
                  value={container.settings?.hideOnTablet !== true}
                  onChange={(value) => updateSetting('settings.hideOnTablet', !value)}
                />
              </div>
              <div>
                <ToggleFieldComponent
                  fieldKey="show_mobile"
                  fieldConfig={{
                    label: 'Show on Mobile',
                    default: true
                  }}
                  value={container.settings?.hideOnMobile !== true}
                  onChange={(value) => updateSetting('settings.hideOnMobile', !value)}
                />
              </div>
            </div>
          </div>
        </div>

        <div>
          <h4 className="font-medium text-gray-900 mb-3">Section ID</h4>
          <div className="space-y-4">
            <div>
              <TextFieldComponent
                fieldKey="html_id"
                fieldConfig={{
                  label: 'HTML ID',
                  placeholder: 'section-id',
                  default: ''
                }}
                value={container.settings?.htmlId || ''}
                onChange={(value) => updateSetting('settings.htmlId', value)}
              />
              <div className="text-xs text-gray-500 mt-1">
                Used for anchor links and JavaScript targeting
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionAdvancedSettings;