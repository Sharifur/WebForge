import { create } from 'zustand';
import { router } from '@inertiajs/react';

// Helper function to move array items
const arrayMove = (array, oldIndex, newIndex) => {
  const newArray = [...array];
  const [removed] = newArray.splice(oldIndex, 1);
  newArray.splice(newIndex, 0, removed);
  return newArray;
};

const usePageBuilderStore = create((set, get) => ({
  // State
  pageContent: {
    containers: []
  },
  originalContent: null,
  selectedWidget: null,
  activePanel: 'widgets',
  isDirty: false,
  isDragging: false,
  activeId: null,
  hoveredDropZone: null,
  settingsPanelVisible: false,
  navigationDialogVisible: false, // Navigation dialog visibility
  navigationDialogPosition: { x: 100, y: 100 }, // Dialog position
  sidebarCollapsed: false, // Left sidebar collapse state
  widgetSnapshots: {}, // Store original widget states for reverting changes
  
  // Enhanced global drag state for cross-container always-visible drop zones
  dragState: {
    // Section dragging
    isDraggingSection: false,
    draggedSectionId: null,
    availableDropZones: [], // [{ id, position, index, type }]
    activeDropZone: null,   // Currently highlighted drop zone

    // Global widget dragging (supports cross-container operations)
    isDragging: false,      // Global drag state for all widgets
    draggedItem: null,      // Currently dragged item (widget, template, or section)
    draggedItemType: null,  // 'widget', 'widget-template', 'section'
    dragStartContainer: null, // Container where drag started
    dragStartColumn: null,  // Column where drag started

    // Drop positioning and targeting
    dropPosition: null,     // Current drop position ('before' or 'after')
    activeDropTarget: null, // Currently active drop target info
    crossContainerMode: false, // Whether dragging across containers

    // Visual feedback state
    showAllDropZones: false, // Show drop zones in all containers
    dropZoneVisibility: {},  // Per-container drop zone visibility

    // Performance optimization
    dragStartTime: null,    // When drag started (for velocity calculations)
    lastMousePosition: null, // Last recorded mouse position
    dragVelocity: { x: 0, y: 0 } // Mouse movement velocity
  },
  
  // Actions
  initializePageContent: (content) => set({ 
    pageContent: content || { containers: [] },
    originalContent: content || { containers: [] }
  }),
  
  setPageContent: (updater) => set(state => {
    const newContent = typeof updater === 'function' ? updater(state.pageContent) : updater;
    return {
      pageContent: newContent,
      isDirty: JSON.stringify(newContent) !== JSON.stringify(state.originalContent)
    };
  }),
  
  setSelectedWidget: (widget) => set(state => {
    // Create snapshot when selecting a widget for the first time
    if (widget && widget.id && !state.widgetSnapshots[widget.id]) {
      return {
        selectedWidget: widget,
        settingsPanelVisible: widget !== null,
        widgetSnapshots: {
          ...state.widgetSnapshots,
          [widget.id]: {
            content: JSON.parse(JSON.stringify(widget.content || {})),
            style: JSON.parse(JSON.stringify(widget.style || {})),
            advanced: JSON.parse(JSON.stringify(widget.advanced || {}))
          }
        }
      };
    }
    
    return {
      selectedWidget: widget,
      settingsPanelVisible: widget !== null
    };
  }),
  
  setActivePanel: (panel) => set({ activePanel: panel }),
  
  setIsDragging: (isDragging, draggedItem = null) => set(state => ({
    isDragging,
    dragState: {
      ...state.dragState,
      isDragging,
      draggedItem: isDragging ? draggedItem : null,
      draggedItemType: isDragging && draggedItem ? (draggedItem.type || 'widget') : null,
      showAllDropZones: isDragging, // Always show drop zones when dragging
      dragStartTime: isDragging ? Date.now() : null,
      // Reset positioning when drag ends
      dropPosition: isDragging ? state.dragState.dropPosition : null,
      activeDropTarget: isDragging ? state.dragState.activeDropTarget : null,
      crossContainerMode: isDragging ? state.dragState.crossContainerMode : false
    }
  })),
  
  setActiveId: (activeId) => set({ activeId }),
  
  setHoveredDropZone: (zone) => set({ hoveredDropZone: zone }),

  setDropPosition: (position) => set(state => ({
    dragState: {
      ...state.dragState,
      dropPosition: position
    }
  })),

  // Enhanced drag state management actions
  setGlobalDragState: (isDragging, draggedItem = null, options = {}) => set(state => ({
    dragState: {
      ...state.dragState,
      isDragging,
      draggedItem: isDragging ? draggedItem : null,
      draggedItemType: isDragging && draggedItem ? (draggedItem.type || options.itemType || 'widget') : null,
      dragStartContainer: isDragging ? options.containerId : null,
      dragStartColumn: isDragging ? options.columnId : null,
      showAllDropZones: isDragging,
      dragStartTime: isDragging ? Date.now() : null,
      lastMousePosition: isDragging ? options.mousePosition : null,
      // Reset when drag ends
      dropPosition: isDragging ? state.dragState.dropPosition : null,
      activeDropTarget: isDragging ? state.dragState.activeDropTarget : null,
      crossContainerMode: isDragging ? state.dragState.crossContainerMode : false,
      dropZoneVisibility: isDragging ? state.dragState.dropZoneVisibility : {}
    }
  })),

  setActiveDropTarget: (target) => set(state => ({
    dragState: {
      ...state.dragState,
      activeDropTarget: target,
      crossContainerMode: target && target.containerId !== state.dragState.dragStartContainer
    }
  })),

  updateMousePosition: (position) => set(state => {
    const lastPos = state.dragState.lastMousePosition;
    const velocity = lastPos ? {
      x: position.x - lastPos.x,
      y: position.y - lastPos.y
    } : { x: 0, y: 0 };

    return {
      dragState: {
        ...state.dragState,
        lastMousePosition: position,
        dragVelocity: velocity
      }
    };
  }),

  setDropZoneVisibility: (containerId, visible) => set(state => ({
    dragState: {
      ...state.dragState,
      dropZoneVisibility: {
        ...state.dragState.dropZoneVisibility,
        [containerId]: visible
      }
    }
  })),

  resetGlobalDragState: () => set(state => ({
    dragState: {
      ...state.dragState,
      isDragging: false,
      draggedItem: null,
      draggedItemType: null,
      dragStartContainer: null,
      dragStartColumn: null,
      dropPosition: null,
      activeDropTarget: null,
      crossContainerMode: false,
      showAllDropZones: false,
      dropZoneVisibility: {},
      dragStartTime: null,
      lastMousePosition: null,
      dragVelocity: { x: 0, y: 0 }
    }
  })),
  
  setSettingsPanelVisible: (visible) => set({ settingsPanelVisible: visible }),
  
  setSidebarCollapsed: (collapsed) => set({ sidebarCollapsed: collapsed }),
  
  toggleSidebar: () => set(state => ({ sidebarCollapsed: !state.sidebarCollapsed })),
  
  // Section drag actions
  setIsDraggingSection: (isDragging) => set(state => ({
    dragState: {
      ...state.dragState,
      isDraggingSection: isDragging,
      draggedSectionId: isDragging ? state.dragState.draggedSectionId : null,
      availableDropZones: isDragging ? state.dragState.availableDropZones : [],
      activeDropZone: isDragging ? state.dragState.activeDropZone : null
    }
  })),
  
  setDraggedSectionId: (sectionId) => set(state => ({
    dragState: {
      ...state.dragState,
      draggedSectionId: sectionId
    }
  })),
  
  setAvailableDropZones: (dropZones) => set(state => ({
    dragState: {
      ...state.dragState,
      availableDropZones: dropZones
    }
  })),
  
  setActiveDropZone: (dropZone) => set(state => ({
    dragState: {
      ...state.dragState,
      activeDropZone: dropZone
    }
  })),
  
  calculateDropZones: () => set(state => {
    const { containers } = state.pageContent;
    const dropZones = [];
    
    // Add drop zone before first container
    dropZones.push({
      id: 'drop-zone-before-0',
      position: 'before',
      index: 0,
      type: 'section-drop-zone'
    });
    
    // Add drop zones after each container
    containers.forEach((container, index) => {
      dropZones.push({
        id: `drop-zone-after-${index}`,
        position: 'after',
        index: index + 1,
        containerId: container.id,
        type: 'section-drop-zone'
      });
    });
    
    return {
      dragState: {
        ...state.dragState,
        availableDropZones: dropZones
      }
    };
  }),
  
  clearDragState: () => set(state => ({
    dragState: {
      isDraggingSection: false,
      draggedSectionId: null,
      availableDropZones: [],
      activeDropZone: null
    }
  })),
  
  toggleSettingsPanel: () => set(state => ({ settingsPanelVisible: !state.settingsPanelVisible })),

  // Navigation dialog methods
  toggleNavigationDialog: () => set(state => ({ navigationDialogVisible: !state.navigationDialogVisible })),
  setNavigationDialogPosition: (position) => set(state => ({ navigationDialogPosition: position })),
  
  // Widget snapshot methods
  createWidgetSnapshot: (widgetId, widget) => set(state => ({
    widgetSnapshots: {
      ...state.widgetSnapshots,
      [widgetId]: {
        content: JSON.parse(JSON.stringify(widget.content || {})),
        style: JSON.parse(JSON.stringify(widget.style || {})),
        advanced: JSON.parse(JSON.stringify(widget.advanced || {}))
      }
    }
  })),
  
  revertWidgetToSnapshot: (widgetId) => set(state => {
    const snapshot = state.widgetSnapshots[widgetId];
    if (!snapshot) return state;
    
    return {
      pageContent: {
        ...state.pageContent,
        containers: state.pageContent.containers.map(container => ({
          ...container,
          columns: container.columns.map(column => ({
            ...column,
            widgets: column.widgets.map(widget =>
              widget.id === widgetId 
                ? { 
                    ...widget, 
                    content: JSON.parse(JSON.stringify(snapshot.content)),
                    style: JSON.parse(JSON.stringify(snapshot.style)),
                    advanced: JSON.parse(JSON.stringify(snapshot.advanced))
                  } 
                : widget
            )
          }))
        }))
      },
      selectedWidget: state.selectedWidget?.id === widgetId 
        ? { 
            ...state.selectedWidget,
            content: JSON.parse(JSON.stringify(snapshot.content)),
            style: JSON.parse(JSON.stringify(snapshot.style)),
            advanced: JSON.parse(JSON.stringify(snapshot.advanced))
          }
        : state.selectedWidget
    };
  }),
  
  clearWidgetSnapshot: (widgetId) => set(state => {
    const newSnapshots = { ...state.widgetSnapshots };
    delete newSnapshots[widgetId];
    return { widgetSnapshots: newSnapshots };
  }),
  
  clearAllWidgetSnapshots: () => set({ widgetSnapshots: {} }),
  
  // Container Actions
  addContainer: (container) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: [...state.pageContent.containers, {
        id: `container-${Date.now()}`,
        type: 'section',
        columns: [
          {
            id: `column-${Date.now()}`,
            width: '100%',
            widgets: [],
            settings: {}
          }
        ],
        settings: {
          padding: '20px',
          margin: '0px',
          backgroundColor: '#ffffff'
        },
        ...container
      }]
    },
    isDirty: true
  })),

  insertSectionAt: (position, section) => set(state => {
    const newContainers = [...state.pageContent.containers];
    newContainers.splice(position, 0, {
      id: section.id || `container-${Date.now()}`,
      type: 'section',
      columns: section.columns || [
        {
          id: `column-${Date.now()}`,
          width: '100%',
          widgets: [],
          settings: {}
        }
      ],
      settings: {
        padding: '20px',
        margin: '0px',
        backgroundColor: '#ffffff',
        ...section.settings
      },
      ...section
    });

    return {
      pageContent: {
        ...state.pageContent,
        containers: newContainers
      },
      isDirty: true
    };
  }),

  // Widget Actions
  addWidgetToColumn: (widgetTemplate, columnId, containerId) => set(state => {
    try {
      const newWidget = {
        id: `widget-${Date.now()}`,
        type: widgetTemplate.type,
        content: { ...widgetTemplate.defaultContent },
        style: { ...widgetTemplate.defaultStyle },
        advanced: { ...widgetTemplate.defaultAdvanced }
      };

    // Special handling for container widgets
    if (widgetTemplate.type === 'container') {
      const columns = widgetTemplate.defaultContent?.columns || 1;
      newWidget.containerData = {
        id: `container-${Date.now()}`,
        columns: Array.from({ length: columns }).map((_, index) => ({
          id: `column-${Date.now()}-${index}`,
          width: `${100 / columns}%`,
          widgets: [],
          settings: {}
        })),
        settings: {
          padding: widgetTemplate.defaultContent?.padding || '20px',
          backgroundColor: widgetTemplate.defaultContent?.backgroundColor || '#ffffff',
          gap: widgetTemplate.defaultContent?.gap || '20px'
        }
      };
    }

    return {
      pageContent: {
        ...state.pageContent,
        containers: state.pageContent.containers.map(container =>
          container.id === containerId
            ? {
                ...container,
                columns: container.columns.map(column =>
                  column.id === columnId
                    ? { ...column, widgets: [...column.widgets, newWidget] }
                    : column
                )
              }
            : container
        )
      },
      isDirty: true
    };
    } catch (error) {
      console.error('[PageBuilderStore] Error in addWidgetToColumn:', error);
      return state;
    }
  }),
  
  updateWidget: (widgetId, updates) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: state.pageContent.containers.map(container => ({
        ...container,
        columns: container.columns.map(column => ({
          ...column,
          widgets: column.widgets.map(widget =>
            widget.id === widgetId ? { ...widget, ...updates } : widget
          )
        }))
      }))
    },
    selectedWidget: state.selectedWidget?.id === widgetId 
      ? { ...state.selectedWidget, ...updates } 
      : state.selectedWidget,
    isDirty: true
  })),
  
  removeWidget: (widgetId) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: state.pageContent.containers.map(container => ({
        ...container,
        columns: container.columns.map(column => ({
          ...column,
          widgets: column.widgets.filter(widget => widget.id !== widgetId)
        }))
      }))
    },
    selectedWidget: state.selectedWidget?.id === widgetId ? null : state.selectedWidget,
    isDirty: true
  })),
  
  reorderWidgets: (columnId, oldIndex, newIndex) => set(state => {
    console.log('[Store] 🔄 REORDER WIDGETS START:', {
      columnId,
      oldIndex,
      newIndex,
      timestamp: new Date().toISOString()
    });

    return {
      pageContent: {
        ...state.pageContent,
        containers: state.pageContent.containers.map(container => ({
          ...container,
          columns: container.columns.map(column => {
            if (column.id === columnId) {
              const beforeWidgets = column.widgets.map(w => ({ id: w.id, type: w.type }));
              const newWidgets = [...column.widgets];
              const [removed] = newWidgets.splice(oldIndex, 1);
              newWidgets.splice(newIndex, 0, removed);

              const afterWidgets = newWidgets.map(w => ({ id: w.id, type: w.type }));

              console.log('[Store] ✅ REORDER WIDGETS SUCCESS:', {
                columnId,
                oldIndex,
                newIndex,
                movedWidget: removed.id,
                beforeOrder: beforeWidgets,
                afterOrder: afterWidgets
              });

              return { ...column, widgets: newWidgets };
            }
            return column;
          })
        }))
      },
      isDirty: true
    };
  }),

  moveWidgetBetweenColumns: (widgetId, fromColumnId, toColumnId, toContainerId) => set(state => {
    console.log('[Store] moveWidgetBetweenColumns called:', {
      widgetId,
      fromColumnId,
      toColumnId,
      toContainerId
    });
    
    let widgetToMove = null;
    let sourceContainerId = null;
    
    // First pass: find and remove the widget from source column
    const containersAfterRemoval = state.pageContent.containers.map(container => {
      const hasSourceColumn = container.columns.some(col => col.id === fromColumnId);
      if (hasSourceColumn) {
        sourceContainerId = container.id;
      }
      
      return {
        ...container,
        columns: container.columns.map(column => {
          if (column.id === fromColumnId) {
            const widget = column.widgets.find(w => w.id === widgetId);
            if (widget) {
              widgetToMove = widget;
              console.log('[Store] Found widget to move:', widget);
              return {
                ...column,
                widgets: column.widgets.filter(w => w.id !== widgetId)
              };
            }
          }
          return column;
        })
      };
    });
    
    // Second pass: add widget to destination column
    if (widgetToMove) {
      const finalContainers = containersAfterRemoval.map(container => {
        // Check if this container contains the destination column
        const hasDestColumn = container.columns.some(col => col.id === toColumnId);
        
        if (hasDestColumn || container.id === toContainerId) {
          console.log('[Store] Adding widget to container:', container.id);
          return {
            ...container,
            columns: container.columns.map(column => {
              if (column.id === toColumnId) {
                console.log('[Store] Adding widget to column:', column.id);
                return {
                  ...column,
                  widgets: [...column.widgets, widgetToMove]
                };
              }
              return column;
            })
          };
        }
        return container;
      });
      
      return {
        pageContent: {
          ...state.pageContent,
          containers: finalContainers
        },
        isDirty: true
      };
    } else {
      console.warn('[Store] Widget not found for move:', widgetId);
    }
    
    return state;
  }),
  
  // Container Actions
  updateContainer: (containerId, updates) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: state.pageContent.containers.map(container =>
        container.id === containerId ? { ...container, ...updates } : container
      )
    },
    isDirty: true
  })),
  
  removeContainer: (containerId) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: state.pageContent.containers.filter(container => container.id !== containerId)
    },
    selectedWidget: null,
    isDirty: true
  })),
  
  reorderContainers: (oldIndex, newIndex) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: arrayMove(state.pageContent.containers, oldIndex, newIndex)
    },
    isDirty: true
  })),
  
  // Save Actions
  savePage: async (pageId) => {
    const { pageContent } = get();
    try {
      // Use the new page builder API endpoint with page_id in request body
      const response = await fetch('/api/page-builder/save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          page_id: pageId,
          content: pageContent,
          is_published: false, // Save as draft by default
          version: '1.0'
        })
      });

      if (!response.ok) {
        // Check if response is HTML (likely redirect to login)
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
          throw new Error('Authentication required. Please log in as admin.');
        }
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const text = await response.text();
      
      // Check if response is HTML instead of JSON
      if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
        throw new Error('Authentication required. Please log in as admin.');
      }
      
      const data = JSON.parse(text);
      
      if (data.success) {
        set({ 
          isDirty: false,
          originalContent: pageContent
        });
        
        // Show success message (you can use a toast library here)
        console.log('Page saved successfully:', data.message);
        
        return data;
      } else {
        throw new Error(data.message || 'Save failed');
      }
    } catch (error) {
      console.error('Save failed:', error);
      
      // Handle authentication errors
      if (error.message.includes('Authentication required')) {
        // Redirect to admin login
        window.location.href = '/admin/login';
      }
      
      throw error;
    }
  },

  // Load page content from the new API
  loadPageContent: async (pageId) => {
    try {
      const response = await fetch(`/api/page-builder/pages/${pageId}/content`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
      });

      if (!response.ok) {
        // Check if response is HTML (likely redirect to login)
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
          throw new Error('Authentication required. Please log in as admin.');
        }
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const text = await response.text();
      
      // Check if response is HTML instead of JSON
      if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
        throw new Error('Authentication required. Please log in as admin.');
      }
      
      const data = JSON.parse(text);
      
      if (data.success) {
        const content = data.data.content || { containers: [] };
        set({
          pageContent: content,
          originalContent: content,
          isDirty: false
        });
        
        return data.data;
      } else {
        throw new Error(data.message || 'Failed to load content');
      }
    } catch (error) {
      console.error('Load content failed:', error);
      
      // Handle authentication errors
      if (error.message.includes('Authentication required')) {
        // Redirect to admin login
        window.location.href = '/admin/login';
      }
      
      throw error;
    }
  },

  // Publish page content
  publishPage: async (pageId) => {
    try {
      const response = await fetch('/api/page-builder/publish', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          page_id: pageId
        })
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      
      if (data.success) {
        console.log('Page published successfully:', data.message);
        return data;
      } else {
        throw new Error(data.message || 'Publish failed');
      }
    } catch (error) {
      console.error('Publish failed:', error);
      throw error;
    }
  },
  
  resetChanges: () => set(state => ({ 
    pageContent: state.originalContent,
    selectedWidget: null,
    isDirty: false 
  })),
  
  // Preview Actions
  setPreviewMode: (mode) => set({ previewMode: mode }),
  
  // Utility Actions
  findWidget: (widgetId) => {
    const { pageContent } = get();
    for (const container of pageContent.containers) {
      for (const column of container.columns) {
        const widget = column.widgets.find(w => w.id === widgetId);
        if (widget) {
          return { widget, columnId: column.id, containerId: container.id };
        }
      }
    }
    return null;
  },
  
  findContainer: (containerId) => {
    const { pageContent } = get();
    return pageContent.containers.find(c => c.id === containerId);
  }
}));

export { usePageBuilderStore };