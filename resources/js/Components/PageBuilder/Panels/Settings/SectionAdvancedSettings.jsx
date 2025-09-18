import React, { useEffect } from 'react';
import TextFieldComponent from '../../Fields/TextFieldComponent';
import TextareaFieldComponent from '../../Fields/TextareaFieldComponent';
import SelectFieldComponent from '../../Fields/SelectFieldComponent';
import ToggleFieldComponent from '../../Fields/ToggleFieldComponent';
import NumberFieldComponent from '../../Fields/NumberFieldComponent';
import CheckboxFieldComponent from '../../Fields/CheckboxFieldComponent';
import sectionSettingsMapper from '@/Services/sectionSettingsMapper';
import pageBuilderCSSService from '@/Services/pageBuilderCSSService';

const SectionAdvancedSettings = ({ container, onUpdate, onWidgetUpdate }) => {
  // Generate consistent section ID based on container
  const generateConsistentSectionId = () => {
    // Use container ID to create consistent, predictable section IDs
    if (container.id) {
      const containerId = container.id.toString();
      
      // Clean the container ID - remove special characters and convert to lowercase
      let cleanId = containerId.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
      
      // Remove 'section' prefix if it exists to avoid redundancy
      if (cleanId.startsWith('section')) {
        cleanId = cleanId.substring(7); // Remove 'section' (7 characters)
      }
      
      // If cleanId is empty after removing 'section', use a fallback
      if (!cleanId) {
        cleanId = Date.now().toString().slice(-8);
      }
      
      return `section-${cleanId}`;
    }
    // Fallback to simple incremental ID
    return `section-${Date.now().toString().slice(-8)}`;
  };

  // Auto-generate section ID on mount if not exists
  useEffect(() => {
    if (!container.settings?.htmlId) {
      const consistentId = generateConsistentSectionId();
      updateSetting('settings.htmlId', consistentId);
    }
  }, [container.id]); // Only run when container ID changes

  const updateSetting = (path, value) => {
    const pathArray = path.split('.');

    const updatedContainer = {
      ...container,
      settings: {
        ...container.settings,
        [pathArray[pathArray.length - 1]]: value
      }
    };

    // Update state
    onUpdate(prev => ({
      ...prev,
      containers: prev.containers.map(c =>
        c.id === container.id ? updatedContainer : c
      )
    }));

    onWidgetUpdate(updatedContainer);

    // Generate and apply CSS for advanced settings changes (visibility, animation, custom CSS)
    requestAnimationFrame(() => {
      const element = document.querySelector(`[data-container-id="${container.id}"]`);
      if (element) {
        const transformedSettings = sectionSettingsMapper.transformToCSS(updatedContainer.settings);
        const responsiveSettings = sectionSettingsMapper.transformResponsive(
          updatedContainer.settings,
          updatedContainer.responsiveSettings || {}
        );

        pageBuilderCSSService.applySettings(
          element,
          'section',
          container.id,
          transformedSettings,
          responsiveSettings
        );

        // Handle custom CSS injection
        if (value && pathArray[pathArray.length - 1] === 'customCSS') {
          pageBuilderCSSService.injectCSS(`section-${container.id}-custom`, value);
        }
      }
    });
  };

  return (
    <div className="p-4">
      <div className="space-y-6">
        {/* Responsive Settings - Moved after Visibility */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Responsive Settings</h4>
          <div className="space-y-4">
            <div className="space-y-2">
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

            {/* Section ID - Consistent Auto-generated */}
            <div>
              <TextFieldComponent
                fieldKey="html_id"
                fieldConfig={{
                  label: 'Section ID',
                  placeholder: 'section-12345',
                  default: ''
                }}
                value={container.settings?.htmlId || generateConsistentSectionId()}
                onChange={(value) => updateSetting('settings.htmlId', value)}
              />
              <div className="text-xs text-gray-500 mt-1">
                Consistent ID for database storage, CSS generation, and JavaScript targeting
              </div>
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

      </div>
    </div>
  );
};

export default SectionAdvancedSettings;