import React from 'react';
import EnhancedBackgroundPicker from '../../Fields/EnhancedBackgroundPicker';
import EnhancedDimensionPicker from '../../Fields/EnhancedDimensionPicker';
import BorderShadowGroup from '../../Fields/BorderShadowGroup';
import ResponsiveFieldWrapper from '../../Fields/ResponsiveFieldWrapper';

const ColumnStyleSettings = ({ column, onUpdate, onWidgetUpdate }) => {
  const updateColumnSetting = (path, value) => {
    const updatedColumn = {
      ...column,
      settings: {
        ...column.settings,
        [path]: value
      }
    };

    // Update the column in the container
    onUpdate(prev => ({
      ...prev,
      containers: prev.containers.map(container =>
        container.id === column.containerId
          ? {
              ...container,
              columns: container.columns.map(col =>
                col.id === column.columnId
                  ? updatedColumn
                  : col
              )
            }
          : container
      )
    }));

    // Also update the selected widget
    if (onWidgetUpdate) {
      onWidgetUpdate(updatedColumn);
    }
  };

  return (
    <div className="p-4">
      <div className="space-y-6">
        {/* Background Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Background</h4>

          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Column Background
              </label>
              <EnhancedBackgroundPicker
                value={column?.settings?.columnBackground || {
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
                  }
                }}
                onChange={(value) => updateColumnSetting('columnBackground', value)}
              />
            </div>
          </div>
        </div>

        {/* Spacing Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Spacing</h4>

          <div className="space-y-4">
            {/* Padding */}
            <ResponsiveFieldWrapper
              label="Padding"
              value={column?.settings?.padding}
              onChange={(value) => updateColumnSetting('padding', value)}
              defaultValue={{ top: 10, right: 10, bottom: 10, left: 10, unit: 'px' }}
            >
              <EnhancedDimensionPicker
                value={column?.settings?.padding || { top: 10, right: 10, bottom: 10, left: 10, unit: 'px' }}
                onChange={(value) => updateColumnSetting('padding', value)}
                units={['px', 'em', 'rem', '%']}
                min={0}
                max={200}
                label="Padding"
                showLabels={true}
                linked={false}
                responsive={true}
              />
            </ResponsiveFieldWrapper>

            {/* Margin */}
            <ResponsiveFieldWrapper
              label="Margin"
              value={column?.settings?.margin}
              onChange={(value) => updateColumnSetting('margin', value)}
              defaultValue={{ top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }}
            >
              <EnhancedDimensionPicker
                value={column?.settings?.margin || { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }}
                onChange={(value) => updateColumnSetting('margin', value)}
                units={['px', 'em', 'rem', '%']}
                allowNegative={true}
                min={-200}
                max={200}
                label="Margin"
                showLabels={true}
                linked={false}
                responsive={true}
              />
            </ResponsiveFieldWrapper>
          </div>
        </div>

        {/* Border & Shadow Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Border & Shadow</h4>

          <div className="space-y-4">
            <BorderShadowGroup
              value={{
                border: {
                  width: column?.settings?.borderWidth || 0,
                  color: column?.settings?.borderColor || '#e2e8f0',
                  style: column?.settings?.borderStyle || 'solid',
                  radius: column?.settings?.borderRadius || { top: 0, right: 0, bottom: 0, left: 0, unit: 'px' }
                },
                shadow: {
                  enabled: column?.settings?.shadowEnabled || false,
                  x: column?.settings?.shadowX || 0,
                  y: column?.settings?.shadowY || 0,
                  blur: column?.settings?.shadowBlur || 0,
                  spread: column?.settings?.shadowSpread || 0,
                  color: column?.settings?.shadowColor || 'rgba(0, 0, 0, 0.1)',
                  inset: column?.settings?.shadowInset || false
                }
              }}
              onChange={(value) => {
                // Update border settings
                updateColumnSetting('borderWidth', value.border.width);
                updateColumnSetting('borderColor', value.border.color);
                updateColumnSetting('borderStyle', value.border.style);
                updateColumnSetting('borderRadius', value.border.radius);

                // Update shadow settings
                updateColumnSetting('shadowEnabled', value.shadow.enabled);
                updateColumnSetting('shadowX', value.shadow.x);
                updateColumnSetting('shadowY', value.shadow.y);
                updateColumnSetting('shadowBlur', value.shadow.blur);
                updateColumnSetting('shadowSpread', value.shadow.spread);
                updateColumnSetting('shadowColor', value.shadow.color);
                updateColumnSetting('shadowInset', value.shadow.inset);
              }}
              showBorder={true}
              showShadow={true}
              responsive={false}
            />
          </div>
        </div>

        {/* Debug Info */}
        <div className="text-xs text-gray-400 mt-4 p-2 bg-gray-50 rounded">
          <div>Column ID: {column?.columnId || column?.id}</div>
          <div>Container ID: {column?.containerId}</div>
          <div>Current Settings: {JSON.stringify(column?.settings || {}, null, 2)}</div>
        </div>
      </div>
    </div>
  );
};

export default ColumnStyleSettings;