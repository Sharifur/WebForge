# Navigation & Drag-Drop System Guide

## Overview

The page builder features a sophisticated dual drag-and-drop system that provides both canvas-based and navigation-based interactions for professional content management. This guide covers the technical implementation, customization options, and best practices for extending the navigation and drag-drop functionality.

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Movable Navigation Dialog](#movable-navigation-dialog)
3. [Navigation Tree Drag & Drop](#navigation-tree-drag--drop)
4. [Canvas Drop Zone System](#canvas-drop-zone-system)
5. [State Management](#state-management)
6. [Performance Optimizations](#performance-optimizations)
7. [Customization Guide](#customization-guide)
8. [Troubleshooting](#troubleshooting)
9. [API Reference](#api-reference)

## System Architecture

### Component Hierarchy

```
PageBuilder/Index.jsx (Root DndContext)
├── CanvasToolbar.jsx
│   └── Navigation Toggle Button [data-navigation-toggle]
├── Canvas.jsx
│   └── DropZone.jsx (Canvas drop zones)
├── MovableNavigationDialog.jsx
│   ├── Native drag system (dialog positioning)
│   └── NavigationTree.jsx
│       ├── DndContext (navigation-specific)
│       ├── DropZoneIndicator.jsx (navigation drop zones)
│       └── TreeNode components (sections, columns, widgets)
└── useDragAndDrop.js (global drag logic)
```

### Dual Drag System

The system operates two independent but coordinated drag contexts:

1. **Canvas Drag System**: Handles widget panel → canvas interactions
2. **Navigation Drag System**: Handles tree-based reordering and management

## Movable Navigation Dialog

### Glass Morphism Implementation

The navigation dialog uses advanced CSS for a professional, semi-transparent appearance that doesn't block page builder interaction:

```jsx
// MovableNavigationDialog.jsx
<div
  className="fixed bg-white rounded-xl border-2 z-50"
  style={{
    left: `${navigationDialogPosition.x}px`,
    top: `${navigationDialogPosition.y}px`,
    width: '320px',
    height: '400px',
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    backdropFilter: 'blur(8px)'
  }}
>
```

### Native Drag System

Custom drag implementation with viewport constraints:

```jsx
const handleMouseDown = (e) => {
  if (e.target.closest('.dialog-content')) return; // Don't drag when clicking content

  setIsDragging(true);
  const rect = dialogRef.current.getBoundingClientRect();
  setDragOffset({
    x: e.clientX - rect.left,
    y: e.clientY - rect.top
  });
};

// Viewport constraints
const maxX = window.innerWidth - 320;  // Dialog width
const maxY = window.innerHeight - 400; // Dialog height
const constrainedX = Math.max(0, Math.min(newX, maxX));
const constrainedY = Math.max(0, Math.min(newY, maxY));
```

### Smart Click Detection

Prevents accidental dialog closing when clicking toolbar buttons:

```jsx
const handleClickOutside = (e) => {
  if (navigationDialogVisible && dialogRef.current && !dialogRef.current.contains(e.target)) {
    // Don't close if clicking on toolbar button
    if (!e.target.closest('[data-navigation-toggle]')) {
      toggleNavigationDialog();
    }
  }
};
```

## Navigation Tree Drag & Drop

### Enhanced Drop Zone Indicators

Improved visual feedback with responsive hover detection:

```jsx
// DropZoneIndicator.jsx
const DropZoneIndicator = ({ dropId, position, level = 0, isDragOverActive = false }) => {
  const { setNodeRef, isOver } = useDroppable({
    id: dropId,
    data: { type: 'tree-drop-zone', position, level }
  });

  const isActive = isOver || isDragOverActive;

  return (
    <div
      ref={setNodeRef}
      className={`transition-all duration-200 ${
        isActive ? 'h-8 opacity-100' : 'h-4 opacity-30 hover:opacity-70'
      }`}
    >
      <div className={`w-full transition-all duration-200 ${
        isActive
          ? 'h-6 bg-gradient-to-r from-blue-100 to-green-100 border-2 border-dashed border-blue-400'
          : 'h-2 bg-transparent border border-dashed border-gray-400 hover:border-blue-400 hover:bg-blue-50'
      }`}>
        {/* Visual indicators */}
      </div>
    </div>
  );
};
```

### Section Renaming System

Click-to-rename functionality with database persistence:

```jsx
// Section rename implementation in NavigationTree.jsx
{node.type === 'section' && renamingSectionId === node.id ? (
  <input
    type="text"
    value={renameValue}
    onChange={(e) => setRenameValue(e.target.value)}
    onBlur={() => handleRenameSubmit(node.id)}
    onKeyDown={(e) => {
      if (e.key === 'Enter') {
        handleRenameSubmit(node.id);
      } else if (e.key === 'Escape') {
        handleRenameCancel();
      }
    }}
    autoFocus
    className="flex-1 text-sm font-medium bg-white border border-blue-300 rounded px-1 py-0.5"
  />
) : (
  <span
    onClick={(e) => {
      if (node.type === 'section') {
        e.stopPropagation();
        handleSectionRename(node.id, node.name);
      }
    }}
    className="flex-1 text-sm font-medium cursor-pointer hover:text-blue-600"
  >
    {node.name}
  </span>
)}
```

### Widget Hierarchy Enforcement

Prevents structural violations by restricting widget drops:

```jsx
// Drop validation in NavigationTree.jsx
const handleNavigationDragEnd = useCallback((event) => {
  const { active, over } = event;

  if (overData?.type === 'tree-drop-zone') {
    // Only allow widget drops if target is a widget (inside a column)
    const targetContainer = pageContent.containers.find(c =>
      c.columns.some(col => col.widgets.some(w => w.id === targetWidgetId))
    );

    if (!targetContainer) {
      console.log('[NavigationTree] Widget drop rejected - target is not a widget inside a column');
      return;
    }
  }
}, [pageContent]);
```

### Drag Sensitivity Improvements

Enhanced drop zone areas for better user experience:

- **Before**: `h-1` height with `opacity-0` (invisible)
- **After**: `h-4` height with `opacity-30` and hover effects
- **Individual Widget Drop Zones**: Both "before" and "after" zones for precise positioning

```jsx
// Both before and after drop zones for widgets
{shouldShowDropZone && (
  <>
    {/* Before drop zone */}
    <DropZoneIndicator
      dropId={`before-${node.id}`}
      position="before"
      level={level}
      isDragOverActive={activeDropZone === `before-${node.id}`}
    />

    {/* Widget content */}

    {/* After drop zone for widgets */}
    {node.type === 'widget' && isDraggingWidget && (
      <DropZoneIndicator
        dropId={`after-${node.id}`}
        position="after"
        level={level}
        isDragOverActive={activeDropZone === `after-${node.id}`}
      />
    )}
  </>
)}
```

## Canvas Drop Zone System

### Context-Aware Drop Zones

Different visual indicators for sections vs widgets:

```jsx
// DropZone.jsx - Context-aware content
const getDropZoneContent = () => {
  if (isDraggingSection) {
    return {
      icon: Layers,
      iconColor: 'text-purple-500',
      bgColor: 'from-purple-50 to-blue-50',
      borderColor: 'border-purple-300',
      message: position === 'before'
        ? 'Drop section at the beginning'
        : `Drop section ${containerId ? 'after this section' : 'at the end'}`
    };
  } else {
    return {
      icon: Component,
      iconColor: 'text-green-500',
      bgColor: 'from-green-50 to-blue-50',
      borderColor: 'border-green-300',
      message: position === 'before'
        ? 'Create new section at the beginning'
        : `Create new section ${containerId ? 'after this section' : 'at the end'}`
    };
  }
};
```

### Performance Optimization

Drop zones only render during active drag operations:

```jsx
// Only render when dragging relevant items
if (!isDraggingSection && !isDraggingWidgetFromPanel) {
  return null;
}
```

## State Management

### pageBuilderStore Structure

```javascript
const usePageBuilderStore = create((set, get) => ({
  // Navigation dialog state
  navigationDialogVisible: false,
  navigationDialogPosition: { x: 100, y: 100 },

  // Enhanced global drag state
  dragState: {
    // Section dragging
    isDraggingSection: false,
    draggedSectionId: null,

    // Global widget dragging
    isDragging: false,
    draggedItem: null,
    draggedItemType: null,

    // Drop positioning
    activeDropZone: null,
    showAllDropZones: false,

    // Performance optimization
    dragStartTime: null,
    lastMousePosition: null,
    dragVelocity: { x: 0, y: 0 }
  },

  // Navigation methods
  toggleNavigationDialog: () => set(state => ({
    navigationDialogVisible: !state.navigationDialogVisible
  })),

  setNavigationDialogPosition: (position) => set({
    navigationDialogPosition: position
  }),

  // Section management
  updateContainer: (containerId, updates) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: state.pageContent.containers.map(container =>
        container.id === containerId
          ? { ...container, ...updates }
          : container
      )
    }
  }))
}));
```

## Performance Optimizations

### Memoized Components

Prevent unnecessary re-renders with React.memo and custom comparison:

```jsx
const TreeNode = React.memo(({ node, level, isNavigationDragging, activeDropZone, ... }) => {
  // Component implementation
}, (prevProps, nextProps) => {
  // Custom comparison to prevent unnecessary re-renders
  return (
    prevProps.node.id === nextProps.node.id &&
    prevProps.level === nextProps.level &&
    prevProps.isNavigationDragging === nextProps.isNavigationDragging &&
    prevProps.activeDropZone === nextProps.activeDropZone &&
    prevProps.node.name === nextProps.node.name
  );
});
```

### State Management Optimizations

Remove unstable functions from useCallback dependencies:

```jsx
// ❌ Problematic - causes infinite re-renders
const handleDragEnd = useCallback((event) => {
  // Implementation
}, [reorderContainers, reorderWidgets, moveWidgetBetweenColumns]);

// ✅ Optimized - stable dependencies only
const handleDragEnd = useCallback((event) => {
  // Implementation
}, [pageContent]); // Only stable values
```

### Drag State Cleanup

Delayed cleanup to prevent visual glitches:

```jsx
const handleNavigationDragEnd = useCallback((event) => {
  // Perform all drag operations first
  if (sourceIndex !== newIndex) {
    reorderWidgets(sourceColumn.id, sourceIndex, newIndex);
  }

  // THEN cleanup drag state (delayed to prevent visual glitches)
  setTimeout(() => {
    setIsNavigationDragging(false);
    setDraggedItem(null);
    setActiveDropZone(null);
  }, 0);
}, []);
```

## Customization Guide

### Adding New Drop Zone Types

1. **Define the drop zone data**:
```jsx
const { setNodeRef, isOver } = useDroppable({
  id: dropId,
  data: {
    type: 'custom-drop-zone', // Your custom type
    customData: 'additional-info'
  }
});
```

2. **Handle in drag end logic**:
```jsx
const handleDragEnd = (event) => {
  const { over } = event;

  if (over?.data.current?.type === 'custom-drop-zone') {
    // Handle your custom drop zone logic
    handleCustomDrop(over.data.current);
  }
};
```

### Extending Navigation Tree

Add new node types by extending the navigation tree structure:

```jsx
const navigationTree = useMemo(() => {
  return pageContent.containers.map((container, index) => ({
    id: container.id,
    type: 'section',
    name: container.name || `Section ${index + 1}`,
    icon: Layers,
    customType: 'my-custom-section', // Add custom properties
    children: [
      // Existing children
      ...customChildren // Add custom child types
    ]
  }));
}, [pageContent]);
```

### Custom Drop Zone Styling

Extend the DropZoneIndicator component:

```jsx
const CustomDropZoneIndicator = ({ dropId, customTheme, ...props }) => {
  const themeStyles = {
    success: 'from-green-50 to-green-100 border-green-400',
    warning: 'from-yellow-50 to-yellow-100 border-yellow-400',
    danger: 'from-red-50 to-red-100 border-red-400'
  };

  return (
    <DropZoneIndicator
      {...props}
      className={`${themeStyles[customTheme]} ${props.className}`}
    />
  );
};
```

## Troubleshooting

### Common Issues

#### 1. Infinite Re-renders During Drag

**Symptoms**: Console shows "Maximum update depth exceeded" errors
**Cause**: Unstable dependencies in useCallback hooks
**Solution**: Only include stable values in dependency arrays

```jsx
// ❌ Problematic
const callback = useCallback(() => {
  // Implementation
}, [storeFunction, anotherStoreFunction]);

// ✅ Fixed
const callback = useCallback(() => {
  // Implementation
}, [stableValue]);
```

#### 2. Drop Zones Not Appearing

**Symptoms**: No visual feedback when dragging
**Cause**: Incorrect drag state or missing drop zone conditions
**Solution**: Check drag state and rendering conditions

```jsx
// Ensure proper drag state
const shouldShowDropZone = isNavigationDragging && (
  (isDraggingSection && node.type === 'section') ||
  (isDraggingWidget && node.type === 'widget')
);
```

#### 3. Widgets Disappearing During Drag

**Symptoms**: Items vanish when starting to drag
**Cause**: Premature drag state cleanup
**Solution**: Delay state cleanup until after operations complete

```jsx
// Delay cleanup to prevent visual glitches
setTimeout(() => {
  setIsNavigationDragging(false);
  setDraggedItem(null);
}, 0);
```

#### 4. Navigation Dialog Positioning Issues

**Symptoms**: Dialog appears outside viewport
**Cause**: Missing viewport constraints
**Solution**: Add proper boundary checking

```jsx
const maxX = window.innerWidth - 320;
const maxY = window.innerHeight - 400;
const constrainedX = Math.max(0, Math.min(newX, maxX));
const constrainedY = Math.max(0, Math.min(newY, maxY));
```

### Debugging Tools

Add debugging to track drag operations:

```jsx
const handleDragStart = (event) => {
  console.log('[DragDebug] Drag started:', {
    activeId: event.active.id,
    activeData: event.active.data.current,
    dragType: event.active.data.current?.type
  });
};

const handleDragEnd = (event) => {
  console.log('[DragDebug] Drag ended:', {
    activeId: event.active.id,
    overId: event.over?.id,
    overData: event.over?.data.current
  });
};
```

## API Reference

### Store Actions

#### Navigation Dialog
```javascript
toggleNavigationDialog() // Toggle dialog visibility
setNavigationDialogPosition({ x: number, y: number }) // Set dialog position
```

#### Drag State Management
```javascript
setIsNavigationDragging(boolean) // Set navigation drag state
setDraggedItem(object) // Set currently dragged item
setActiveDropZone(string|null) // Set active drop zone ID
```

#### Content Management
```javascript
updateContainer(containerId, updates) // Update section properties
reorderContainers(oldIndex, newIndex) // Reorder sections
reorderWidgets(columnId, oldIndex, newIndex) // Reorder widgets in column
moveWidgetBetweenColumns(widgetId, sourceColumnId, targetColumnId, targetIndex) // Move widget between columns
```

### Component Props

#### MovableNavigationDialog
```typescript
interface MovableNavigationDialogProps {
  // No external props - uses store state
}
```

#### NavigationTree
```typescript
interface NavigationTreeProps {
  // No external props - uses store state
}
```

#### DropZoneIndicator
```typescript
interface DropZoneIndicatorProps {
  dropId: string;
  position: 'before' | 'after';
  level?: number;
  isDragOverActive?: boolean;
}
```

### Events

#### Drag Events
- `handleNavigationDragStart(event)` - Navigation drag start
- `handleNavigationDragOver(event)` - Navigation drag over
- `handleNavigationDragEnd(event)` - Navigation drag end

#### Section Events
- `handleSectionRename(sectionId, currentName)` - Start section rename
- `handleRenameSubmit(sectionId)` - Submit section rename
- `handleRenameCancel()` - Cancel section rename

This comprehensive guide provides the foundation for understanding, using, and extending the navigation and drag-drop system. For additional customization needs, refer to the source code and existing implementations as examples.

## Related Documentation

- **[Main Project Documentation](../CLAUDE.md)** - Complete project overview and architecture
- **[Page Builder UX Guide](PAGE_BUILDER_UX_GUIDE.md)** - User experience principles and workflows
- **[Comprehensive Field Examples](COMPREHENSIVE_FIELD_EXAMPLES.md)** - Navigation components and usage patterns
- **[Field Type Registration Guide](FIELD_TYPE_REGISTRATION_GUIDE.md)** - Creating custom field components
- **[Dynamic CSS Generation Guide](DYNAMIC_CSS_GENERATION_GUIDE.md)** - CSS generation system integration