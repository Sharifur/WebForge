# Field Structure Best Practices & Error Prevention

## 🎯 **Overview**

This guide explains how to properly structure fields using tabs and groups in the page builder system, common errors developers face, and clear error messages to help debug issues.

## ⚠️ **Common Structure Errors**

### **❌ Problem: Mixed Tabs and Groups at Same Level**
```php
// BAD: This will cause "Undefined array key 'type'" error
public function getStyleFields(): array
{
    $control = new ControlManager();

    // Tab without endTab()
    $control->addTab('normal', 'Normal State')
        ->registerField('text_color', FieldManager::COLOR()...)

    // Group mixed at same level - CONFLICT!
    $control->addGroup('colors', 'Colors')
        ->registerField('background', FieldManager::COLOR()...)
        ->endGroup();

    return $control->getFields(); // ERROR: Tab never closed
}
```

**🔍 Error Message:**
```
Widget field structure error: Tab 'normal' was opened but never closed.
Make sure to call endTab() after adding fields to a tab.
Example: $control->addTab('normal', 'Normal')->registerField(...)->endTab();
```

### **✅ Solution: Proper Tab/Group Structure**
```php
// GOOD: Clean structure with proper closing
public function getStyleFields(): array
{
    $control = new ControlManager();

    // Properly closed tab
    $control->addTab('normal', 'Normal State')
        ->registerField('text_color', FieldManager::COLOR()...)
        ->endTab(); // ← IMPORTANT: Close the tab

    // Separate group
    $control->addGroup('colors', 'Colors')
        ->registerField('background', FieldManager::COLOR()...)
        ->endGroup(); // ← IMPORTANT: Close the group

    return $control->getFields();
}
```

## 🏗️ **Supported Structure Patterns**

### **Pattern 1: Tabs with Groups Inside (Recommended)**
```php
public function getStyleFields(): array
{
    $control = new ControlManager();

    $control->addTab('normal', 'Normal State')
        // Group within tab
        ->addGroup('colors', 'Colors')
            ->registerField('text_color', FieldManager::COLOR()...)
            ->registerField('background_color', FieldManager::COLOR()...)
            ->endGroup()

        // Another group within same tab
        ->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()...)
            ->registerField('margin', FieldManager::DIMENSION()...)
            ->endGroup()
        ->endTab();

    $control->addTab('hover', 'Hover State')
        ->addGroup('hover_colors', 'Hover Colors')
            ->registerField('text_color_hover', FieldManager::COLOR()...)
            ->endGroup()
        ->endTab();

    return $control->getFields();
}
```

### **Pattern 2: Mixed Tabs and Standalone Groups**
```php
public function getStyleFields(): array
{
    $control = new ControlManager();

    // State-specific tabs
    $control->addTab('normal', 'Normal State')
        ->registerField('text_color', FieldManager::COLOR()...)
        ->endTab();

    $control->addTab('hover', 'Hover State')
        ->registerField('text_color_hover', FieldManager::COLOR()...)
        ->endTab();

    // Global styling group (outside tabs)
    $control->addGroup('advanced', 'Advanced Styling')
        ->registerField('custom_css', FieldManager::TEXTAREA()...)
        ->endGroup();

    return $control->getFields();
}
```

### **Pattern 3: Pure Groups (No Tabs)**
```php
public function getStyleFields(): array
{
    $control = new ControlManager();

    $control->addGroup('typography', 'Typography')
        ->registerField('font_size', FieldManager::NUMBER()...)
        ->registerField('font_weight', FieldManager::SELECT()...)
        ->endGroup();

    $control->addGroup('colors', 'Colors')
        ->registerField('text_color', FieldManager::COLOR()...)
        ->registerField('background_color', FieldManager::COLOR()...)
        ->endGroup();

    return $control->getFields();
}
```

### **Pattern 4: Pure Tabs (No Groups)**
```php
public function getStyleFields(): array
{
    $control = new ControlManager();

    $control->addTab('design', 'Design')
        ->registerField('color', FieldManager::COLOR()...)
        ->registerField('background', FieldManager::COLOR()...)
        ->endTab();

    $control->addTab('effects', 'Effects')
        ->registerField('shadow', FieldManager::TEXT()...)
        ->registerField('transform', FieldManager::SELECT()...)
        ->endTab();

    return $control->getFields();
}
```

## 🚨 **Error Messages & Solutions**

### **Error 1: Unclosed Tab**
```
Widget field structure error: Tab 'normal' was opened but never closed.
Make sure to call endTab() after adding fields to a tab.
Example: $control->addTab('normal', 'Normal')->registerField(...)->endTab();
```

**Solution:** Add `->endTab()` after adding fields to a tab.

### **Error 2: Unclosed Group**
```
Widget field structure error: Group 'colors' was opened but never closed.
Make sure to call endGroup() after adding fields to a group.
Example: $control->addGroup('styling', 'Styling')->registerField(...)->endGroup();
```

**Solution:** Add `->endGroup()` after adding fields to a group.

### **Error 3: Empty Tab**
```
Widget field structure error: Tab 'hover' is empty.
Tabs must contain at least one field or group.
Add fields using $control->addTab('hover', 'Label')->registerField(...)
or groups using addGroup() within the tab.
```

**Solution:** Add at least one field or group to the tab.

### **Error 4: Empty Group in Tab**
```
Widget field structure error: Group 'effects' within tab 'hover' is empty.
Groups must contain at least one field.
Add fields using $control->addTab('hover', 'Label')->addGroup('effects', 'Group Label')->registerField(...);
```

**Solution:** Add at least one field to the group.

### **Error 5: "Undefined array key 'type'"**
This typically happens when:
1. A tab or group is not properly closed
2. Field configuration is malformed
3. Structure validation fails

**Debug Steps:**
1. Enable debug mode: `APP_DEBUG=true`
2. Check widget preview API response for detailed stack trace
3. Ensure all `addTab()` calls have matching `endTab()`
4. Ensure all `addGroup()` calls have matching `endGroup()`

## 🎯 **Best Practices**

### **1. Always Close Tabs and Groups**
```php
// Always close what you open
$control->addTab('normal', 'Normal')
    // ... add fields
    ->endTab(); // ← Required

$control->addGroup('spacing', 'Spacing')
    // ... add fields
    ->endGroup(); // ← Required
```

### **2. Use Descriptive Names and Labels**
```php
// Good: Clear, descriptive names
$control->addTab('normal_state', 'Normal State')
$control->addGroup('text_styling', 'Text Styling')

// Bad: Unclear names
$control->addTab('tab1', 'Tab')
$control->addGroup('group', 'Group')
```

### **3. Organize Related Fields**
```php
// Group related fields together
$control->addTab('normal', 'Normal State')
    ->addGroup('colors', 'Colors')
        ->registerField('text_color', FieldManager::COLOR()...)
        ->registerField('background_color', FieldManager::COLOR()...)
        ->endGroup()

    ->addGroup('typography', 'Typography')
        ->registerField('font_size', FieldManager::NUMBER()...)
        ->registerField('font_weight', FieldManager::SELECT()...)
        ->endGroup()
    ->endTab();
```

### **4. Use State-Based Tabs for Interactive Elements**
```php
// Perfect for buttons, links, form elements
$control->addTab('normal', 'Normal State')
    // Normal state styling
    ->endTab();

$control->addTab('hover', 'Hover State')
    // Hover state styling
    ->endTab();

$control->addTab('active', 'Active State')
    // Active state styling
    ->endTab();
```

### **5. Use Groups for Feature Organization**
```php
// Group by styling category
$control->addGroup('layout', 'Layout & Position')
$control->addGroup('styling', 'Appearance')
$control->addGroup('effects', 'Effects & Animation')
$control->addGroup('advanced', 'Advanced Options')
```

## 📊 **Data Structure Output**

### **Tabs with Groups Structure**
```json
{
  "_tabs": {
    "normal": {
      "type": "tab",
      "label": "Normal State",
      "icon": null,
      "fields": {},
      "groups": {
        "colors": {
          "type": "group",
          "label": "Colors",
          "fields": {
            "text_color": {...},
            "background_color": {...}
          }
        }
      }
    }
  }
}
```

### **Mixed Structure**
```json
{
  "_tabs": {
    "normal": {
      "type": "tab",
      "label": "Normal State",
      "fields": {
        "text_color": {...}
      }
    }
  },
  "advanced_group": {
    "type": "group",
    "label": "Advanced",
    "fields": {
      "custom_css": {...}
    }
  }
}
```

## 🔧 **Debugging Tools**

### **1. Structure Validation**
The system automatically validates structure and provides clear error messages.

### **2. Debug Mode**
Enable detailed error reporting:
```env
APP_DEBUG=true
```

### **3. Widget Preview API**
Test widget structure via API:
```bash
POST /api/widgets/{type}/preview
{
  "settings": {
    "style": {
      "normal": {
        "text_color": "#000000"
      }
    }
  }
}
```

### **4. Log Warnings**
Check Laravel logs for structure warnings:
```
Widget structure warning: You have both tabs and standalone fields.
Consider organizing standalone fields into groups or tabs for better UX.
```

## 📝 **Quick Reference**

| Structure | Use Case | Example |
|-----------|----------|---------|
| **Tabs Only** | State management | Normal/Hover/Active |
| **Groups Only** | Feature organization | Colors/Typography/Spacing |
| **Tabs + Groups** | Complex widgets | States with organized features |
| **Mixed** | Advanced widgets | State tabs + global groups |

## 🚀 **Summary**

1. **Always close tabs and groups** with `endTab()` and `endGroup()`
2. **Use clear, descriptive names** for tabs and groups
3. **Organize related fields together** in logical groups
4. **Use state-based tabs** for interactive elements
5. **Enable debug mode** when troubleshooting structure issues
6. **Follow the error messages** - they provide specific solutions

The enhanced error system will guide you to proper structure and help prevent common mistakes that cause the "Undefined array key 'type'" error.