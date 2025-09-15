import React from 'react';
import RangeFieldComponent from '../../Fields/RangeFieldComponent';
import NumberFieldComponent from '../../Fields/NumberFieldComponent';
import SelectFieldComponent from '../../Fields/SelectFieldComponent';
import ToggleFieldComponent from '../../Fields/ToggleFieldComponent';
import TextFieldComponent from '../../Fields/TextFieldComponent';

const SectionGeneralSettings = ({ container, onUpdate, onWidgetUpdate }) => {
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

    // Also update the selected widget to reflect changes
    onWidgetUpdate({
      ...container,
      settings: {
        ...container.settings,
        [pathArray[pathArray.length - 1]]: value
      }
    });
  };

  const updateColumnStructure = (columns, gridTemplate = null) => {
    // Create exactly the specified number of columns, no more, no less
    const newColumns = [];
    const timestamp = Date.now();
    
    // Build exactly the number of columns requested
    for (let i = 0; i < columns; i++) {
      // Preserve existing column data if available
      const existingColumn = container.columns && container.columns[i];
      newColumns.push({
        id: existingColumn?.id || `column-${container.id}-${i}-${timestamp}`,
        width: gridTemplate ? 'auto' : `${100 / columns}%`,
        widgets: existingColumn?.widgets ? [...existingColumn.widgets] : [],
        settings: existingColumn?.settings ? {...existingColumn.settings} : {}
      });
    }

    const updatedSettings = {
      ...container.settings,
      gridTemplate: gridTemplate,
      columnCount: columns
    };

    // Create a completely new container object to force re-render
    const updatedContainer = {
      ...container,
      columns: newColumns,
      settings: updatedSettings
    };

    // Completely replace the container with new column structure
    onUpdate(prev => ({
      ...prev,
      containers: prev.containers.map(c =>
        c.id === container.id ? updatedContainer : c
      )
    }));

    // Update the selected widget immediately
    onWidgetUpdate(updatedContainer);
  };

  return (
    <div className="p-4">
      <div className="space-y-6">
        {/* Column Structure Section */}
        <div>
          <h4 className="font-medium text-gray-900 mb-3">Column Structure</h4>
          <div className="grid grid-cols-2 gap-2">
            {/* 1 Column */}
            <button
              onClick={() => updateColumnStructure(1)}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                (!container.settings?.gridTemplate && container.columns?.length === 1)
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="24" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">1 Column</span>
            </button>

            {/* 2 Columns */}
            <button
              onClick={() => updateColumnStructure(2)}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                (!container.settings?.gridTemplate && container.columns?.length === 2)
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="11" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="13" width="11" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">2 Columns</span>
            </button>

            {/* 3 Columns */}
            <button
              onClick={() => updateColumnStructure(3)}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                (!container.settings?.gridTemplate && container.columns?.length === 3)
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="7" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="8.5" width="7" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="17" width="7" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">3 Columns</span>
            </button>

            {/* 4 Columns */}
            <button
              onClick={() => updateColumnStructure(4)}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                (!container.settings?.gridTemplate && container.columns?.length === 4)
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="6.33" width="5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="12.67" width="5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="19" width="5" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">4 Columns</span>
            </button>

            {/* 5 Columns */}
            <button
              onClick={() => updateColumnStructure(5)}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                (!container.settings?.gridTemplate && container.columns?.length === 5)
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="4" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="5" width="4" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="10" width="4" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="15" width="4" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="20" width="4" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">5 Columns</span>
            </button>

            {/* 6 Columns */}
            <button
              onClick={() => updateColumnStructure(6)}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                (!container.settings?.gridTemplate && container.columns?.length === 6)
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="3.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="4.17" width="3.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="8.33" width="3.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="12.5" width="3.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="16.67" width="3.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="20.83" width="3.17" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">6 Columns</span>
            </button>

            {/* 30-70 Split */}
            <button
              onClick={() => updateColumnStructure(2, '30% 70%')}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                container.settings?.gridTemplate === '30% 70%'
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="7" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="8.5" width="15.5" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">30% - 70%</span>
            </button>

            {/* 70-30 Split */}
            <button
              onClick={() => updateColumnStructure(2, '70% 30%')}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                container.settings?.gridTemplate === '70% 30%'
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="15.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="17" width="7" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">70% - 30%</span>
            </button>

            {/* 25-50-25 Split */}
            <button
              onClick={() => updateColumnStructure(3, '25% 50% 25%')}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                container.settings?.gridTemplate === '25% 50% 25%'
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="5.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="6.5" width="11" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="18.5" width="5.5" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">25% - 50% - 25%</span>
            </button>

            {/* 40-60 Split */}
            <button
              onClick={() => updateColumnStructure(2, '40% 60%')}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                container.settings?.gridTemplate === '40% 60%'
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="9" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="10.5" width="13.5" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">40% - 60%</span>
            </button>

            {/* 60-40 Split */}
            <button
              onClick={() => updateColumnStructure(2, '60% 40%')}
              className={`p-2 border-2 rounded-lg transition-colors flex flex-col items-center ${
                container.settings?.gridTemplate === '60% 40%'
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 hover:border-gray-300 text-gray-700'
              }`}
            >
              <svg className="w-6 h-3 mb-1" viewBox="0 0 24 12" fill="currentColor">
                <rect width="13.5" height="12" rx="1" className="fill-current opacity-30" />
                <rect x="15" width="9" height="12" rx="1" className="fill-current opacity-30" />
              </svg>
              <span className="text-xs font-medium">60% - 40%</span>
            </button>
          </div>
        </div>

        {/* Enhanced Column Gap */}
        <div>
          <RangeFieldComponent
            fieldKey="column_gap"
            fieldConfig={{
              label: 'Column Gap',
              min: 0,
              max: 100,
              step: 1,
              unit: container.settings?.gap?.match(/[a-zA-Z%]+$/)?.[0] || 'px',
              default: 20
            }}
            value={parseInt(container.settings?.gap || '20px') || 20}
            onChange={(value) => {
              const unit = container.settings?.gap?.match(/[a-zA-Z%]+$/)?.[0] || 'px';
              updateSetting('settings.gap', `${value}${unit}`);
            }}
          />
          
          {/* Unit Selector */}
          <div className="mt-2">
            <SelectFieldComponent
              fieldKey="column_gap_unit"
              fieldConfig={{
                label: 'Gap Unit',
                options: {
                  'px': 'Pixels (px)',
                  '%': 'Percentage (%)',
                  'em': 'Em units (em)',
                  'rem': 'Rem units (rem)'
                },
                default: 'px'
              }}
              value={container.settings?.gap?.match(/[a-zA-Z%]+$/)?.[0] || 'px'}
              onChange={(unit) => {
                const value = parseInt(container.settings?.gap || '20px') || 20;
                updateSetting('settings.gap', `${value}${unit}`);
              }}
            />
          </div>
        </div>

        {/* Enhanced Section Layout Settings */}
        <div className="border-t pt-6">
          <h4 className="font-medium text-gray-900 mb-4">Layout Settings</h4>
          
          {/* Content Width */}
          <div className="mb-4">
            <SelectFieldComponent
              fieldKey="content_width"
              fieldConfig={{
                label: 'Content Width',
                options: {
                  'boxed': 'Boxed (Contained)',
                  'full_width': 'Full Width',
                  'full_width_contained': 'Full Width with Contained Content'
                },
                default: 'boxed'
              }}
              value={container.settings?.contentWidth || 'boxed'}
              onChange={(value) => updateSetting('settings.contentWidth', value)}
            />
          </div>

          {/* Max Width */}
          <div className="mb-4">
            <NumberFieldComponent
              fieldKey="max_width"
              fieldConfig={{
                label: 'Maximum Width (px)',
                min: 300,
                max: 1920,
                step: 50,
                default: 1200,
                placeholder: '1200'
              }}
              value={container.settings?.maxWidth || 1200}
              onChange={(value) => updateSetting('settings.maxWidth', value)}
            />
          </div>

          {/* Minimum Height */}
          <div className="mb-4">
            <NumberFieldComponent
              fieldKey="min_height"
              fieldConfig={{
                label: 'Minimum Height (px)',
                min: 0,
                max: 1000,
                step: 10,
                default: 0,
                placeholder: '0'
              }}
              value={container.settings?.minHeight || 0}
              onChange={(value) => updateSetting('settings.minHeight', value)}
            />
          </div>

          {/* Vertical Alignment */}
          <div className="mb-4">
            <SelectFieldComponent
              fieldKey="vertical_align"
              fieldConfig={{
                label: 'Vertical Alignment',
                options: {
                  'top': 'Top',
                  'center': 'Center',
                  'bottom': 'Bottom'
                },
                default: 'top'
              }}
              value={container.settings?.verticalAlign || 'top'}
              onChange={(value) => updateSetting('settings.verticalAlign', value)}
            />
          </div>
        </div>

        {/* Enhanced Section Identity */}
        <div className="border-t pt-6">
          <h4 className="font-medium text-gray-900 mb-4">Section Identity</h4>
          
          {/* Section ID */}
          <div className="mb-4">
            <TextFieldComponent
              fieldKey="section_id"
              fieldConfig={{
                label: 'Section ID',
                placeholder: 'unique-section-id',
                default: ''
              }}
              value={container.settings?.sectionId || ''}
              onChange={(value) => updateSetting('settings.sectionId', value)}
            />
          </div>

          {/* Section Label */}
          <div className="mb-4">
            <TextFieldComponent
              fieldKey="section_label"
              fieldConfig={{
                label: 'Section Label (Internal)',
                placeholder: 'Hero Section',
                default: ''
              }}
              value={container.settings?.sectionLabel || ''}
              onChange={(value) => updateSetting('settings.sectionLabel', value)}
            />
          </div>

          {/* Enable Full Height */}
          <div className="mb-4">
            <ToggleFieldComponent
              fieldKey="enable_full_height"
              fieldConfig={{
                label: 'Full Height Section (100vh)',
                default: false
              }}
              value={container.settings?.enableFullHeight || false}
              onChange={(value) => updateSetting('settings.enableFullHeight', value)}
            />
          </div>

          {/* Enable Sticky Section */}
          <div className="mb-4">
            <ToggleFieldComponent
              fieldKey="sticky_section"
              fieldConfig={{
                label: 'Sticky Section',
                default: false
              }}
              value={container.settings?.stickySection || false}
              onChange={(value) => updateSetting('settings.stickySection', value)}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionGeneralSettings;