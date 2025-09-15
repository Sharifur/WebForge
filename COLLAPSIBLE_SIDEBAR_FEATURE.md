# Collapsible Sidebar Feature

## Overview

Added a collapsible left sidebar functionality to the Page Builder, similar to modern code editors and design tools. This feature allows users to maximize their workspace while maintaining access to essential widgets and tools.

## Features

### ✅ **Collapse/Expand Toggle**
- **Toggle Button**: Floating button positioned on the right edge of the sidebar
- **Visual Indicators**: ChevronLeft/ChevronRight icons indicating collapse direction
- **Smooth Animation**: 300ms transition with easing for smooth state changes

### ✅ **Keyboard Shortcut**
- **Shortcut**: `Ctrl+B` (Windows/Linux) or `Cmd+B` (Mac)
- **Global Listener**: Works anywhere in the page builder interface
- **Tooltip**: Shows keyboard shortcut in button tooltip

### ✅ **Collapsed State Features**
- **Minimal Width**: Sidebar collapses to 64px (16 Tailwind units)
- **Icon-Only Tabs**: Vertical stack of tab icons with tooltips
- **Widget Icons**: Show essential widget icons for quick access
- **Drag & Drop**: Widgets remain fully draggable in collapsed state
- **Active Tab Indicator**: Right border highlight for active tab

### ✅ **Expanded State Features**
- **Full Width**: 320px sidebar with complete functionality
- **Search Bar**: Full search functionality for widgets
- **Category Labels**: Complete category names and widget counts
- **Detailed View**: Full widget cards with names and descriptions

### ✅ **State Management**
- **Zustand Store**: Persistent state across page builder sessions
- **State Actions**: `setSidebarCollapsed()`, `toggleSidebar()`
- **React Integration**: Smooth integration with existing store architecture

## Implementation Details

### **Store Changes**
```javascript
// Added to pageBuilderStore.js
sidebarCollapsed: false,
setSidebarCollapsed: (collapsed) => set({ sidebarCollapsed: collapsed }),
toggleSidebar: () => set(state => ({ sidebarCollapsed: !state.sidebarCollapsed }))
```

### **Component Updates**
- **PageBuilder/Index.jsx**: Added keyboard shortcut listener and sidebar props
- **WidgetPanel.jsx**: Complete redesign with collapsed/expanded states
- **New Components**: `CollapsedWidgetList`, `CollapsedDraggableWidget`, `CollapsedDraggableSection`

### **CSS Enhancements**
Added to `/public/css/drop-zones.css`:
- `.sidebar-collapse-toggle`: Enhanced button styling with backdrop blur
- `.collapsed-widget-item`: Interactive hover effects for collapsed widgets
- Responsive breakpoints for mobile devices

## Visual Design

### **Toggle Button**
- **Position**: Floating outside sidebar on the right
- **Style**: Circular white button with subtle shadow
- **Hover Effect**: Scale animation and enhanced shadow
- **Accessibility**: Clear tooltips with keyboard shortcuts

### **Collapsed Sidebar**
- **Width**: 64px fixed width
- **Tabs**: Vertical icon stack with right border highlight
- **Widgets**: Icon-only grid with hover animations
- **Pro Indicators**: Small dots for premium widgets

### **Smooth Transitions**
- **Sidebar Width**: 300ms cubic-bezier animation
- **Button Hover**: Scale and shadow effects
- **Widget Hover**: Background and scale animations

## User Experience

### **Workflow Benefits**
1. **More Canvas Space**: Collapse sidebar for larger design area
2. **Quick Access**: Essential widgets still accessible when collapsed
3. **Keyboard Efficiency**: Fast toggle with Ctrl+B shortcut
4. **Visual Clarity**: Clean, minimal interface options

### **Drag & Drop**
- **Full Functionality**: All drag operations work in both states
- **Visual Feedback**: Hover states and animations maintained
- **Tooltips**: Widget names shown on hover in collapsed mode

### **Responsive Design**
- **Mobile Optimized**: Smaller sidebar width on mobile devices
- **Touch Friendly**: Adequate touch targets for mobile interaction

## Technical Implementation

### **State Architecture**
```javascript
const {
  sidebarCollapsed,
  toggleSidebar
} = usePageBuilderStore();

// Keyboard shortcut
React.useEffect(() => {
  const handleKeyPress = (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key === 'b') {
      event.preventDefault();
      toggleSidebar();
    }
  };
  // ... event listener setup
}, [toggleSidebar]);
```

### **Conditional Rendering**
```javascript
{collapsed ? (
  <CollapsedWidgetList />
) : (
  <FullWidgetList />
)}
```

### **CSS Classes**
- **Dynamic Width**: `${collapsed ? 'w-16' : 'w-80'}`
- **Transition**: `transition-all duration-300 ease-in-out`
- **Custom Classes**: `sidebar-collapse-toggle`, `collapsed-widget-item`

## Future Enhancements

### **Potential Improvements**
- **Auto-collapse**: Automatically collapse on smaller screens
- **Mini-preview**: Widget preview on hover in collapsed mode  
- **Categories**: Collapsible categories in collapsed state
- **Search Integration**: Quick search overlay for collapsed mode
- **Custom Width**: User-adjustable sidebar width
- **Remember State**: Persist collapse state in user preferences

### **Advanced Features**
- **Sidebar Docking**: Dock to left or right side
- **Multi-panel**: Multiple collapsible panels
- **Contextual Collapse**: Smart collapse based on content
- **Gesture Support**: Touch gestures for mobile collapse

## Browser Support

- **Modern Browsers**: Full support in Chrome, Firefox, Safari, Edge
- **CSS Grid/Flexbox**: Uses modern layout techniques
- **Backdrop Filter**: Enhanced visual effects where supported
- **Smooth Transitions**: Hardware-accelerated animations

This feature significantly improves the page builder's usability by providing users with flexible workspace control while maintaining full functionality in both expanded and collapsed states.