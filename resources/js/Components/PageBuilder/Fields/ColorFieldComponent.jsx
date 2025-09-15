import React from 'react';

/**
 * ColorFieldComponent - Renders color picker fields
 */
const ColorFieldComponent = ({ fieldKey, fieldConfig, value, onChange }) => {
  const {
    label,
    default: defaultValue
  } = fieldConfig;

  const colorValue = value || defaultValue || '#000000';

  return (
    <div className="flex items-center space-x-2">
      <input
        type="color"
        value={colorValue}
        onChange={(e) => onChange(e.target.value)}
        className="w-12 h-8 border border-gray-300 rounded cursor-pointer"
      />
      <input
        type="text"
        value={colorValue}
        onChange={(e) => onChange(e.target.value)}
        className="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="#000000"
      />
    </div>
  );
};

export default ColorFieldComponent;