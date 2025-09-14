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
  widgetSnapshots: {}, // Store original widget states for reverting changes
  
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
  
  setIsDragging: (isDragging) => set({ isDragging }),
  
  setActiveId: (activeId) => set({ activeId }),
  
  setHoveredDropZone: (zone) => set({ hoveredDropZone: zone }),
  
  setSettingsPanelVisible: (visible) => set({ settingsPanelVisible: visible }),
  
  toggleSettingsPanel: () => set(state => ({ settingsPanelVisible: !state.settingsPanelVisible })),
  
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
  
  reorderWidgets: (columnId, oldIndex, newIndex) => set(state => ({
    pageContent: {
      ...state.pageContent,
      containers: state.pageContent.containers.map(container => ({
        ...container,
        columns: container.columns.map(column => {
          if (column.id === columnId) {
            const newWidgets = [...column.widgets];
            const [removed] = newWidgets.splice(oldIndex, 1);
            newWidgets.splice(newIndex, 0, removed);
            return { ...column, widgets: newWidgets };
          }
          return column;
        })
      }))
    },
    isDirty: true
  })),
  
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