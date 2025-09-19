# How Responsive Device Switching Works

## Quick Overview

The Laravel Page Builder features a **unified responsive device system** that enables seamless editing across desktop, tablet, and mobile viewports with a single device selection that updates the entire interface.

## How It Works (Simple Version)

### 1. **User Clicks Device Icon** 📱
User clicks desktop, tablet, or mobile icon in the toolbar.

### 2. **Global State Updates** 🔄
```javascript
setCurrentDevice('tablet') // Updates global store
```

### 3. **Everything Syncs Automatically** ✨
- Canvas viewport changes to tablet width (768px)
- All spacing controls switch to tablet settings
- All responsive fields show tablet values
- Typography picker shows tablet typography
- Device indicator appears showing "Tablet (768px)"

### 4. **Visual Feedback** 👀
- Selected device icon highlighted
- Canvas shows device frame (for tablet/mobile)
- Device label appears above canvas
- Smooth transitions between device modes

## Key Benefits

✅ **One Click Updates Everything** - No repetitive device selection
✅ **Consistent Interface** - All controls show same device
✅ **Professional Feel** - Smooth animations and visual feedback
✅ **No Confusion** - Single source of truth for device state
✅ **Persistent** - Remembers device selection across page refreshes

## Technical Flow

```
Toolbar Device Click → pageBuilderStore.setCurrentDevice() → Global State Update
                                    ↓
    ┌─────────────────────────────────────────────────────────────┐
    ↓                    ↓                    ↓                   ↓
Canvas Viewport    Dimension Pickers    Typography Picker    Tab Groups
Changes Width      Sync to Device       Sync to Device      Sync to Device
```

## What Changes When You Switch Devices

### **Desktop Mode** (Default)
- Canvas: Full width (1450px)
- Controls: Show desktop values
- Visual: Clean editor interface

### **Tablet Mode**
- Canvas: 768px width with device frame
- Controls: Show tablet-specific values
- Visual: Browser-like frame with device indicator

### **Mobile Mode**
- Canvas: 375px width with device frame
- Controls: Show mobile-specific values
- Visual: Phone-like frame with device indicator

## Components That Auto-Sync

1. **EnhancedDimensionPicker** - All spacing, padding, margin controls
2. **ResponsiveFieldWrapper** - Any field wrapped for responsive editing
3. **DynamicTabGroup** - When used for responsive breakpoints
4. **EnhancedTypographyPicker** - Font, size, spacing controls
5. **Canvas** - Viewport and visual presentation

## Data Structure

Each responsive value stores device-specific settings:

```javascript
{
  desktop: { padding: "20px 15px", font_size: "18px" },
  tablet:  { padding: "15px 10px", font_size: "16px" },
  mobile:  { padding: "10px 5px",  font_size: "14px" }
}
```

When you switch devices, the interface shows the appropriate values for that device.

## Developer Notes

- **Global State**: All device logic uses `pageBuilderStore.currentDevice`
- **Session Persistence**: Device selection saved in `sessionStorage`
- **Smart Detection**: Components auto-detect if they should use global or local state
- **Defensive Programming**: Null-safe access prevents crashes
- **CSS Integration**: Responsive CSS generated automatically for frontend

---

**Result**: Professional responsive editing experience where selecting a device once updates the entire page builder interface automatically! 🎉