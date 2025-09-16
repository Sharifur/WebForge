import React from 'react';
import ToggleFieldComponent from '../../Fields/ToggleFieldComponent';
import SelectFieldComponent from '../../Fields/SelectFieldComponent';
import TextFieldComponent from '../../Fields/TextFieldComponent';
import TextareaFieldComponent from '../../Fields/TextareaFieldComponent';
import NumberFieldComponent from '../../Fields/NumberFieldComponent';
import ResponsiveFieldWrapper from '../../Fields/ResponsiveFieldWrapper';

const ColumnAdvancedSettings = ({ column, onUpdate, onWidgetUpdate }) => {
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
        {/* Visibility Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Visibility</h4>

          <div className="space-y-4">
            <div className="space-y-2">
              <ToggleFieldComponent
                fieldKey="hideOnDesktop"
                fieldConfig={{
                  label: "Hide on Desktop",
                  default: false
                }}
                value={column?.settings?.hideOnDesktop || false}
                onChange={(value) => updateColumnSetting('hideOnDesktop', value)}
              />

              <ToggleFieldComponent
                fieldKey="hideOnTablet"
                fieldConfig={{
                  label: "Hide on Tablet",
                  default: false
                }}
                value={column?.settings?.hideOnTablet || false}
                onChange={(value) => updateColumnSetting('hideOnTablet', value)}
              />

              <ToggleFieldComponent
                fieldKey="hideOnMobile"
                fieldConfig={{
                  label: "Hide on Mobile",
                  default: false
                }}
                value={column?.settings?.hideOnMobile || false}
                onChange={(value) => updateColumnSetting('hideOnMobile', value)}
              />
            </div>
          </div>
        </div>

        {/* Custom Attributes Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Custom Attributes</h4>

          <div className="space-y-4">
            {/* CSS Classes */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                CSS Classes
              </label>
              <TextFieldComponent
                fieldKey="customClasses"
                fieldConfig={{
                  label: 'CSS Classes',
                  placeholder: 'my-custom-class another-class',
                  default: ''
                }}
                value={column?.settings?.customClasses || ''}
                onChange={(value) => updateColumnSetting('customClasses', value)}
              />
              <p className="text-xs text-gray-500 mt-1">
                Add custom CSS classes separated by spaces
              </p>
            </div>

            {/* Custom ID */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Custom ID
              </label>
              <TextFieldComponent
                fieldKey="customId"
                fieldConfig={{
                  label: 'Custom ID',
                  placeholder: `${column?.columnId || column?.id || 'column-id'}`,
                  default: column?.columnId || column?.id || ''
                }}
                value={column?.settings?.customId || column?.columnId || column?.id || ''}
                onChange={(value) => updateColumnSetting('customId', value)}
              />
              <p className="text-xs text-gray-500 mt-1">
                Unique identifier for this column. Default: <code className="bg-gray-100 px-1 rounded text-xs">{column?.columnId || column?.id || 'auto-generated'}</code>
              </p>
            </div>

            {/* Z-Index */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Z-Index
              </label>
              <NumberFieldComponent
                fieldKey="zIndex"
                fieldConfig={{
                  label: 'Z-Index',
                  min: -1000,
                  max: 1000,
                  default: 0,
                  placeholder: '0'
                }}
                value={column?.settings?.zIndex || 0}
                onChange={(value) => updateColumnSetting('zIndex', value)}
              />
              <p className="text-xs text-gray-500 mt-1">
                Controls stacking order (higher values appear on top)
              </p>
            </div>
          </div>
        </div>

        {/* Animation Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Animation</h4>

          <div className="space-y-4">
            {/* Animation Type */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Entrance Animation
              </label>
              <SelectFieldComponent
                fieldKey="animation"
                fieldConfig={{
                  label: 'Entrance Animation',
                  default: 'none',
                  options: {
                    'none': 'None',
                    'fade-in': 'Fade In',
                    'slide-up': 'Slide Up',
                    'slide-down': 'Slide Down',
                    'slide-left': 'Slide Left',
                    'slide-right': 'Slide Right',
                    'zoom-in': 'Zoom In',
                    'zoom-out': 'Zoom Out',
                    'bounce-in': 'Bounce In',
                    'rotate-in': 'Rotate In'
                  }
                }}
                value={column?.settings?.animation || 'none'}
                onChange={(value) => updateColumnSetting('animation', value)}
              />
            </div>

            {/* Animation Duration - Only show if animation is not 'none' */}
            {column?.settings?.animation && column?.settings?.animation !== 'none' && (
              <>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Animation Duration (ms)
                  </label>
                  <NumberFieldComponent
                    fieldKey="animationDuration"
                    fieldConfig={{
                      label: 'Animation Duration (ms)',
                      min: 100,
                      max: 3000,
                      step: 100,
                      default: 300,
                      placeholder: '300'
                    }}
                    value={column?.settings?.animationDuration || 300}
                    onChange={(value) => updateColumnSetting('animationDuration', value)}
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Animation Delay (ms)
                  </label>
                  <NumberFieldComponent
                    fieldKey="animationDelay"
                    fieldConfig={{
                      label: 'Animation Delay (ms)',
                      min: 0,
                      max: 2000,
                      step: 100,
                      default: 0,
                      placeholder: '0'
                    }}
                    value={column?.settings?.animationDelay || 0}
                    onChange={(value) => updateColumnSetting('animationDelay', value)}
                  />
                </div>
              </>
            )}
          </div>
        </div>

        {/* Custom CSS Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Custom CSS</h4>

          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Custom CSS
              </label>
              <TextareaFieldComponent
                fieldKey="customCSS"
                fieldConfig={{
                  label: 'Custom CSS',
                  placeholder: `/* Custom CSS for this column */
.my-column {
  /* Your styles here */
}`,
                  rows: 6,
                  default: ''
                }}
                value={column?.settings?.customCSS || ''}
                onChange={(value) => updateColumnSetting('customCSS', value)}
              />
              <p className="text-xs text-gray-500 mt-1">
                Add custom CSS styles for advanced customization. Use <code>{'{{WRAPPER}}'}</code> to target this specific column.
              </p>
            </div>
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

export default ColumnAdvancedSettings;