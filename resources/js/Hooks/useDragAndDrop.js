import { usePageBuilderStore } from '@/Store/pageBuilderStore';

export const useDragAndDrop = () => {
  const { 
    setIsDragging, 
    setActiveId, 
    setHoveredDropZone,
    addWidgetToColumn,
    addContainer,
    reorderWidgets,
    reorderContainers,
    updateWidget
  } = usePageBuilderStore();

  const handleDragStart = (event) => {
    const { active } = event;
    setActiveId(active.id);
    setIsDragging(true);
  };

  const handleDragOver = (event) => {
    const { active, over } = event;
    if (over) {
      const activeData = active.data.current;
      const overData = over.data.current;
      
      // Check if this is a valid drop target
      const isValidDrop = validateDropTarget(activeData, overData);
      
      setHoveredDropZone({
        id: over.id,
        isValid: isValidDrop
      });
    }
  };

  // Helper function to validate drop targets
  const validateDropTarget = (activeData, overData) => {
    if (!activeData || !overData) return false;

    // Container widgets can only be dropped on canvas
    if (activeData?.widget?.type === 'container') {
      return overData?.type === 'canvas';
    }

    // Regular widgets can only be dropped in columns
    if (activeData?.type === 'widget-template') {
      return overData?.type === 'column';
    }

    // Sections can be dropped on canvas
    if (activeData?.type === 'section-template') {
      return overData?.type === 'canvas';
    }

    return true;
  };

  const handleDragEnd = (event) => {
    const { active, over } = event;
    
    setActiveId(null);
    setIsDragging(false);
    setHoveredDropZone(null);

    if (!over) return;

    const activeData = active.data.current;
    const overData = over.data.current;

    // Validation Rules
    // Rule 1: Container widgets cannot be placed inside other containers/columns
    if (activeData?.widget?.type === 'container' && overData?.type === 'column') {
      console.warn('Cannot place container widget inside another container');
      return;
    }

    // Rule 2: Regular widgets can only be placed in columns, not directly on canvas
    if (activeData?.type === 'widget-template' && 
        activeData?.widget?.type !== 'container' && 
        overData?.type === 'canvas') {
      console.warn('Widgets must be placed inside containers/columns, not directly on canvas');
      return;
    }

    // Handle widget drop from panel to column
    if (activeData?.type === 'widget-template' && overData?.type === 'column') {
      addWidgetToColumn(activeData.widget, overData.columnId, overData.containerId);
      return;
    }

    // Handle container widget drop on canvas - only containers allowed on canvas
    if (activeData?.type === 'widget-template' && 
        activeData?.widget?.type === 'container' && 
        overData?.type === 'canvas') {
      
      // Create a new container based on the container widget settings
      const newContainerId = `container-${Date.now()}`;
      const columns = activeData.widget.defaultContent?.columns || 1;
      
      const containerColumns = Array.from({ length: columns }).map((_, index) => ({
        id: `column-${Date.now()}-${index}`,
        width: `${100 / columns}%`,
        widgets: [],
        settings: {}
      }));
      
      addContainer({
        id: newContainerId,
        type: 'section',
        columns: containerColumns,
        settings: {
          padding: activeData.widget.defaultContent?.padding || '40px 20px',
          margin: '0px',
          backgroundColor: activeData.widget.defaultContent?.backgroundColor || '#ffffff',
          gap: activeData.widget.defaultContent?.gap || '20px'
        }
      });
      return;
    }

    // Handle section drop from panel to canvas
    if (activeData?.type === 'section-template' && overData?.type === 'canvas') {
      addContainer({
        type: 'section',
        columns: activeData.section.columns || [
          {
            id: `column-${Date.now()}`,
            width: '100%',
            widgets: [],
            settings: {}
          }
        ],
        settings: activeData.section.settings || {}
      });
      return;
    }

    // Handle widget reordering within the same column
    if (activeData?.type === 'widget' && overData?.type === 'widget') {
      const activeWidget = activeData.widget;
      const overWidget = overData.widget;
      
      if (activeData.columnId === overData.columnId) {
        const { pageContent } = usePageBuilderStore.getState();
        const container = pageContent.containers.find(c => c.id === activeData.containerId);
        const column = container?.columns.find(c => c.id === activeData.columnId);
        
        if (column) {
          const oldIndex = column.widgets.findIndex(w => w.id === activeWidget.id);
          const newIndex = column.widgets.findIndex(w => w.id === overWidget.id);
          
          if (oldIndex !== newIndex) {
            reorderWidgets(activeData.columnId, oldIndex, newIndex);
          }
        }
      }
      return;
    }

    // Handle widget drop to different column
    if (activeData?.type === 'widget' && overData?.type === 'column') {
      if (activeData.columnId !== overData.columnId) {
        // Move widget to different column
        moveWidgetToColumn(
          activeData.widget.id, 
          activeData.columnId, 
          overData.columnId,
          overData.containerId
        );
      }
      return;
    }

    // Handle container reordering
    if (activeData?.type === 'container' && overData?.type === 'container') {
      const { pageContent } = usePageBuilderStore.getState();
      const oldIndex = pageContent.containers.findIndex(c => c.id === active.id);
      const newIndex = pageContent.containers.findIndex(c => c.id === over.id);
      
      if (oldIndex !== newIndex) {
        reorderContainers(oldIndex, newIndex);
      }
      return;
    }
  };

  const moveWidgetToColumn = (widgetId, fromColumnId, toColumnId, toContainerId) => {
    const { pageContent, setPageContent } = usePageBuilderStore.getState();
    
    // Find the widget to move
    let widgetToMove = null;
    const updatedContainers = pageContent.containers.map(container => ({
      ...container,
      columns: container.columns.map(column => {
        if (column.id === fromColumnId) {
          // Remove widget from source column
          const widgetIndex = column.widgets.findIndex(w => w.id === widgetId);
          if (widgetIndex !== -1) {
            widgetToMove = column.widgets[widgetIndex];
            return {
              ...column,
              widgets: column.widgets.filter(w => w.id !== widgetId)
            };
          }
        }
        return column;
      })
    }));

    if (widgetToMove) {
      // Add widget to destination column
      const finalContainers = updatedContainers.map(container => ({
        ...container,
        columns: container.columns.map(column => {
          if (column.id === toColumnId) {
            return {
              ...column,
              widgets: [...column.widgets, widgetToMove]
            };
          }
          return column;
        })
      }));

      setPageContent({
        ...pageContent,
        containers: finalContainers
      });
    }
  };

  return {
    handleDragStart,
    handleDragOver,
    handleDragEnd
  };
};