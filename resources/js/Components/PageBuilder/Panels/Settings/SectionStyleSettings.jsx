import React, { useState, useEffect } from 'react';
import PhpFieldRenderer from '@/Components/PageBuilder/Fields/PhpFieldRenderer';
import sectionSettingsMapper from '@/Services/sectionSettingsMapper';
import pageBuilderCSSService from '@/Services/pageBuilderCSSService';


const SectionStyleSettings = ({ container, onUpdate, onWidgetUpdate }) => {
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

    // Generate and apply CSS
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
      }
    });
  };

  return (
    <div className="p-4">
      <div className="space-y-6">
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Spacing</h4>
          <div className="space-y-4">
            <PhpFieldRenderer
              fieldKey="padding"
              fieldConfig={{
                type: 'spacing',
                label: 'Padding',
                responsive: true,
                default: '20px 20px 20px 20px',
                units: ['px', 'em', 'rem', '%'],
                linked: false,
                sides: ['top', 'right', 'bottom', 'left'],
                min: 0,
                max: 1000,
                step: 1
              }}
              value={container.settings?.padding || '20px 20px 20px 20px'}
              onChange={(value) => updateSetting('settings.padding', value)}
            />
            
            <PhpFieldRenderer
              fieldKey="margin"
              fieldConfig={{
                type: 'spacing',
                label: 'Margin',
                responsive: true,
                default: '0px 0px 0px 0px',
                units: ['px', 'em', 'rem', '%'],
                linked: false,
                sides: ['top', 'right', 'bottom', 'left'],
                min: 0,
                max: 1000,
                step: 1
              }}
              value={container.settings?.margin || '0px 0px 0px 0px'}
              onChange={(value) => updateSetting('settings.margin', value)}
            />
          </div>
        </div>

        {/* Enhanced Background Group */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Background</h4>
          <div className="space-y-4">
            <PhpFieldRenderer
              fieldKey="section_background"
              fieldConfig={{
                type: 'background_group',
                label: 'Section Background',
                responsive: true,
                default: {
                  type: 'none',
                  color: '#ffffff',
                  gradient: {
                    type: 'linear',
                    angle: 135,
                    colorStops: [
                      { color: '#667EEA', position: 0 },
                      { color: '#764BA2', position: 100 }
                    ]
                  },
                  image: {
                    url: '',
                    size: 'cover',
                    position: 'center center',
                    repeat: 'no-repeat',
                    attachment: 'scroll'
                  },
                  hover: {
                    color: ''
                  }
                }
              }}
              value={container.settings?.sectionBackground || {
                type: 'none',
                color: '#ffffff'
              }}
              onChange={(value) => updateSetting('settings.sectionBackground', value)}
            />
          </div>
        </div>

        {/* Border & Shadow Group */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Border & Shadow</h4>
          <div className="space-y-4">
            <PhpFieldRenderer
              fieldKey="border_shadow_group"
              fieldConfig={{
                type: 'border_shadow_group',
                label: 'Border & Shadow',
                responsive: true,
                showBorder: true,
                showShadow: true,
                default: {
                  border: {
                    width: { top: 0, right: 0, bottom: 0, left: 0 },
                    style: 'solid',
                    color: '#e2e8f0',
                    radius: { topLeft: 0, topRight: 0, bottomLeft: 0, bottomRight: 0, unit: 'px' }
                  },
                  shadow: {
                    enabled: false,
                    horizontal: 0,
                    vertical: 4,
                    blur: 6,
                    spread: 0,
                    color: 'rgba(0, 0, 0, 0.1)',
                    inset: false
                  }
                }
              }}
              value={container.settings?.borderShadow || {
                border: { width: { top: 0, right: 0, bottom: 0, left: 0 } },
                shadow: { enabled: false }
              }}
              onChange={(value) => updateSetting('settings.borderShadow', value)}
            />
          </div>
        </div>

        {/* Typography Group */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Typography</h4>
          <div className="space-y-4">
            <PhpFieldRenderer
              fieldKey="section_typography"
              fieldConfig={{
                type: 'typography_group',
                label: 'Section Typography',
                responsive: true,
                default: {
                  fontSize: '16px',
                  fontWeight: '400',
                  fontFamily: 'inherit',
                  lineHeight: '1.5',
                  letterSpacing: '0',
                  textTransform: 'none',
                  textDecoration: 'none',
                  textAlign: 'left'
                }
              }}
              value={container.settings?.sectionTypography || {
                fontSize: '16px',
                fontWeight: '400',
                lineHeight: '1.5'
              }}
              onChange={(value) => updateSetting('settings.sectionTypography', value)}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionStyleSettings;