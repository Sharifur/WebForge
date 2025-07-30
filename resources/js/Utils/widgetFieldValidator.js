/**
 * Widget Field Validator
 * 
 * Validates widget field definitions to ensure they follow the expected structure
 * and contain all required properties for proper rendering.
 */

// Supported field types
const SUPPORTED_FIELD_TYPES = [
  'text',
  'textarea',
  'select',
  'checkbox',
  'toggle',
  'number',
  'url',
  'color',
  'range',
  'icon',
  'image',
  'dimension',
  'responsive_dimension',
  'spacing',
  'repeater',
  'group'
];

// Required properties for each field type
const FIELD_REQUIREMENTS = {
  text: ['type', 'label'],
  textarea: ['type', 'label'],
  select: ['type', 'label', 'options'],
  checkbox: ['type', 'label'],
  toggle: ['type', 'label'],
  number: ['type', 'label'],
  url: ['type', 'label'],
  color: ['type', 'label'],
  range: ['type', 'label'],
  icon: ['type', 'label'],
  image: ['type', 'label'],
  dimension: ['type', 'label'],
  responsive_dimension: ['type', 'label'],
  spacing: ['type', 'label'],
  repeater: ['type', 'label', 'fields'],
  group: ['type', 'label', 'fields']
};

/**
 * Validate a single field configuration
 */
export function validateFieldConfig(fieldKey, fieldConfig) {
  const errors = [];

  if (!fieldConfig || typeof fieldConfig !== 'object') {
    errors.push(`Field '${fieldKey}' must be an object`);
    return errors;
  }

  const { type } = fieldConfig;

  // Check if type is provided
  if (!type) {
    errors.push(`Field '${fieldKey}' is missing required 'type' property`);
    return errors;
  }

  // Check if type is supported
  if (!SUPPORTED_FIELD_TYPES.includes(type)) {
    errors.push(`Field '${fieldKey}' has unsupported type '${type}'. Supported types: ${SUPPORTED_FIELD_TYPES.join(', ')}`);
  }

  // Check required properties for this field type
  const requirements = FIELD_REQUIREMENTS[type] || [];
  for (const requirement of requirements) {
    if (!fieldConfig[requirement]) {
      errors.push(`Field '${fieldKey}' of type '${type}' is missing required property '${requirement}'`);
    }
  }

  // Type-specific validations
  switch (type) {
    case 'select':
      if (fieldConfig.options && typeof fieldConfig.options !== 'object') {
        errors.push(`Field '${fieldKey}' options must be an object`);
      }
      break;

    case 'range':
      if (fieldConfig.min !== undefined && fieldConfig.max !== undefined) {
        if (fieldConfig.min >= fieldConfig.max) {
          errors.push(`Field '${fieldKey}' min value must be less than max value`);
        }
      }
      break;

    case 'number':
      if (fieldConfig.min !== undefined && typeof fieldConfig.min !== 'number') {
        errors.push(`Field '${fieldKey}' min must be a number`);
      }
      if (fieldConfig.max !== undefined && typeof fieldConfig.max !== 'number') {
        errors.push(`Field '${fieldKey}' max must be a number`);
      }
      break;

    case 'repeater':
      if (!fieldConfig.fields || typeof fieldConfig.fields !== 'object') {
        errors.push(`Field '${fieldKey}' of type 'repeater' must have a 'fields' object`);
      } else {
        // Recursively validate repeater fields
        Object.entries(fieldConfig.fields).forEach(([subFieldKey, subFieldConfig]) => {
          const subErrors = validateFieldConfig(`${fieldKey}.${subFieldKey}`, subFieldConfig);
          errors.push(...subErrors);
        });
      }
      
      // Validate min/max values
      if (fieldConfig.min !== undefined && typeof fieldConfig.min !== 'number') {
        errors.push(`Field '${fieldKey}' min must be a number`);
      }
      if (fieldConfig.max !== undefined && typeof fieldConfig.max !== 'number') {
        errors.push(`Field '${fieldKey}' max must be a number`);
      }
      if (fieldConfig.min !== undefined && fieldConfig.max !== undefined && fieldConfig.min > fieldConfig.max) {
        errors.push(`Field '${fieldKey}' min value must be less than or equal to max value`);
      }
      break;

    case 'group':
      if (!fieldConfig.fields || typeof fieldConfig.fields !== 'object') {
        errors.push(`Field '${fieldKey}' of type 'group' must have a 'fields' object`);
      } else {
        // Recursively validate group fields
        Object.entries(fieldConfig.fields).forEach(([subFieldKey, subFieldConfig]) => {
          const subErrors = validateFieldConfig(`${fieldKey}.${subFieldKey}`, subFieldConfig);
          errors.push(...subErrors);
        });
      }
      break;
  }

  return errors;
}

/**
 * Validate widget field groups (general, style, advanced)
 */
export function validateWidgetFields(widgetType, fieldsData) {
  const errors = [];

  if (!fieldsData || typeof fieldsData !== 'object') {
    errors.push(`Widget '${widgetType}' fields data must be an object`);
    return errors;
  }

  if (!fieldsData.fields || typeof fieldsData.fields !== 'object') {
    errors.push(`Widget '${widgetType}' must have a 'fields' object`);
    return errors;
  }

  // Validate each field/group
  Object.entries(fieldsData.fields).forEach(([fieldKey, fieldConfig]) => {
    const fieldErrors = validateFieldConfig(fieldKey, fieldConfig);
    errors.push(...fieldErrors);
  });

  return errors;
}

/**
 * Validate complete widget configuration
 */
export function validateWidgetConfiguration(widgetType, configuration) {
  const errors = [];

  if (!configuration || typeof configuration !== 'object') {
    errors.push(`Widget '${widgetType}' configuration must be an object`);
    return errors;
  }

  // Validate each tab (general, style, advanced)
  const tabs = ['general', 'style', 'advanced'];
  
  for (const tab of tabs) {
    if (configuration[tab]) {
      const tabErrors = validateWidgetFields(`${widgetType}.${tab}`, configuration[tab]);
      errors.push(...tabErrors.map(error => `[${tab}] ${error}`));
    }
  }

  return errors;
}

/**
 * Get field type information and supported properties
 */
export function getFieldTypeInfo(type) {
  if (!SUPPORTED_FIELD_TYPES.includes(type)) {
    return null;
  }

  const info = {
    type,
    required: FIELD_REQUIREMENTS[type] || [],
    supported: true
  };

  // Add type-specific information
  switch (type) {
    case 'text':
      info.description = 'Single line text input';
      info.optional = ['placeholder', 'default', 'required', 'maxlength'];
      break;
    case 'textarea':
      info.description = 'Multi-line text input';
      info.optional = ['placeholder', 'default', 'required', 'rows'];
      break;
    case 'select':
      info.description = 'Dropdown selection';
      info.optional = ['default', 'required', 'searchable', 'clearable'];
      break;
    case 'checkbox':
    case 'toggle':
      info.description = 'Boolean checkbox/toggle';
      info.optional = ['default'];
      break;
    case 'number':
      info.description = 'Numeric input';
      info.optional = ['min', 'max', 'step', 'default', 'required'];
      break;
    case 'range':
      info.description = 'Range slider input';
      info.optional = ['min', 'max', 'step', 'default'];
      break;
    case 'color':
      info.description = 'Color picker';
      info.optional = ['default'];
      break;
    case 'url':
      info.description = 'URL input with validation';
      info.optional = ['placeholder', 'default', 'required'];
      break;
    case 'icon':
      info.description = 'Icon selector';
      info.optional = ['default', 'icon_set', 'searchable'];
      break;
    case 'image':
      info.description = 'Image URL input';
      info.optional = ['default', 'allowed_types'];
      break;
    case 'dimension':
      info.description = 'Four-value dimension input (top, right, bottom, left)';
      info.optional = ['default', 'units', 'min', 'max'];
      break;
    case 'responsive_dimension':
      info.description = 'Responsive dimension input with device-specific values (desktop, tablet, mobile)';
      info.optional = ['default'];
      break;
    case 'spacing':
      info.description = 'Responsive spacing input (padding/margin) with device-specific values';
      info.optional = ['default', 'responsive'];
      break;
    case 'repeater':
      info.description = 'Repeatable field group for dynamic lists of items';
      info.optional = ['default', 'min', 'max'];
      break;
    case 'group':
      info.description = 'Group container for organizing related fields';
      info.optional = [];
      break;
  }

  return info;
}

/**
 * Get all supported field types with their information
 */
export function getAllSupportedFieldTypes() {
  return SUPPORTED_FIELD_TYPES.map(type => getFieldTypeInfo(type));
}

export default {
  validateFieldConfig,
  validateWidgetFields,
  validateWidgetConfiguration,
  getFieldTypeInfo,
  getAllSupportedFieldTypes,
  SUPPORTED_FIELD_TYPES
};