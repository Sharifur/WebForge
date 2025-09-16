import React from 'react';
import SelectFieldComponent from '../../Fields/SelectFieldComponent';
import RangeFieldComponent from '../../Fields/RangeFieldComponent';
import DisplayModeField from '../../Fields/DisplayModeField';
import FlexDirectionField from '../../Fields/FlexDirectionField';
import JustifyContentField from '../../Fields/JustifyContentField';
import AlignItemsField from '../../Fields/AlignItemsField';
import FlexGapField from '../../Fields/FlexGapField';
import FlexWrapField from '../../Fields/FlexWrapField';
import ResponsiveFieldWrapper from '../../Fields/ResponsiveFieldWrapper';

const ColumnGeneralSettings = ({ column, onUpdate, onWidgetUpdate }) => {
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

  const updateMultipleColumnSettings = (settingsObject) => {
    const updatedColumn = {
      ...column,
      settings: {
        ...column.settings,
        ...settingsObject
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

  const displayType = column?.settings?.display || 'block';
  const isFlexDisplay = displayType === 'flex';

  return (
    <div className="p-4">
      <div className="space-y-6">
        {/* Items Section */}
        <div className="bg-white border border-gray-200 rounded-lg p-4">
          <h4 className="font-medium text-gray-900 mb-4">Items</h4>
          
          <div className="space-y-4">
            {/* Display Mode Selector */}
            <DisplayModeField
              value={displayType}
              onChange={(value) => updateColumnSetting('display', value)}
            />

            {/* Flex Controls - Only show when display is flex */}
            {isFlexDisplay && (
              <>
                {/* Direction */}
                <ResponsiveFieldWrapper
                  label="Direction"
                  value={column?.settings?.flexDirection}
                  onChange={(value) => updateColumnSetting('flexDirection', value)}
                  defaultValue="column"
                >
                  <FlexDirectionField />
                </ResponsiveFieldWrapper>

                {/* Justify Content */}
                <ResponsiveFieldWrapper
                  label="Justify Content"
                  value={column?.settings?.justifyContent}
                  onChange={(value) => updateColumnSetting('justifyContent', value)}
                  defaultValue="flex-start"
                >
                  <JustifyContentField />
                </ResponsiveFieldWrapper>

                {/* Align Items */}
                <ResponsiveFieldWrapper
                  label="Align Items"
                  value={column?.settings?.alignItems}
                  onChange={(value) => updateColumnSetting('alignItems', value)}
                  defaultValue="stretch"
                >
                  <AlignItemsField />
                </ResponsiveFieldWrapper>

                {/* Gaps */}
                <ResponsiveFieldWrapper
                  label="Gaps"
                  value={column?.settings?.gap}
                  onChange={(value) => updateColumnSetting('gap', value)}
                  defaultValue="0px"
                >
                  <FlexGapField />
                </ResponsiveFieldWrapper>

                {/* Wrap */}
                <ResponsiveFieldWrapper
                  label="Wrap"
                  value={column?.settings?.flexWrap}
                  onChange={(value) => updateColumnSetting('flexWrap', value)}
                  defaultValue="nowrap"
                >
                  <FlexWrapField />
                </ResponsiveFieldWrapper>
              </>
            )}
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

export default ColumnGeneralSettings;