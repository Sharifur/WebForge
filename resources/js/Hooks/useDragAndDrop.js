import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import Swal from 'sweetalert2';

export const useDragAndDrop = () => {
  const { 
    setIsDragging, 
    setActiveId, 
    setHoveredDropZone,
    addWidgetToColumn,
    addContainer,
    reorderWidgets,
    reorderContainers,
    updateWidget,
    moveWidgetBetweenColumns
  } = usePageBuilderStore();

  const handleDragStart = (event) => {
    const { active } = event;
    console.log('[DragAndDrop] Drag started:', {
      activeId: active.id,
      activeData: active.data.current
    });
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

    // Section widgets can be dropped on canvas or other sections (will be placed after)
    if (activeData?.widget?.type === 'section') {
      return overData?.type === 'canvas' || overData?.type === 'section';
    }

    // Container widgets can only be dropped on canvas
    if (activeData?.widget?.type === 'container') {
      return overData?.type === 'canvas';
    }

    // Regular widgets can be dropped in columns OR on canvas (with auto-section creation)
    if (activeData?.type === 'widget-template') {
      return overData?.type === 'column' || overData?.type === 'canvas';
    }

    // Sections can be dropped on canvas
    if (activeData?.type === 'section-template') {
      return overData?.type === 'canvas';
    }

    return true;
  };

  const handleDragEnd = (event) => {
    const { active, over } = event;
    
    console.log('[DragAndDrop] Drag ended:', {
      activeId: active.id,
      overId: over?.id,
      activeData: active.data.current,
      overData: over?.data.current
    });
    
    setActiveId(null);
    setIsDragging(false);
    setHoveredDropZone(null);

    if (!over) {
      console.log('[DragAndDrop] No drop target - drag cancelled');
      return;
    }

    try {
      const activeData = active.data.current;
      const overData = over.data.current;
      
      console.log('[DragAndDrop] Processing drag data:', {
        activeType: activeData?.type,
        overType: overData?.type,
        activeDataFull: activeData,
        overDataFull: overData
      });

    // Validation Rules
    // Rule 1: Handle section widget placement
    if (activeData?.widget?.type === 'section' && overData?.type === 'section') {
      // Section dropped on another section - place it after the target section
      const { pageContent } = usePageBuilderStore.getState();
      const targetSectionIndex = pageContent.containers.findIndex(c => c.id === over.id);
      
      if (targetSectionIndex !== -1) {
        // Create new section container
        const newContainerId = `section-${Date.now()}`;
        const newSection = {
          id: newContainerId,
          type: 'section',
          columns: [{
            id: `column-${Date.now()}`,
            width: '100%',
            widgets: [],
            settings: {}
          }],
          settings: {
            padding: '40px 20px',
            margin: '0px',
            backgroundColor: 'transparent'
          },
          widgetType: 'section',
          widgetSettings: activeData.widget.content || {}
        };
        
        // Insert after target section
        const newContainers = [...pageContent.containers];
        newContainers.splice(targetSectionIndex + 1, 0, newSection);
        
        const { setPageContent } = usePageBuilderStore.getState();
        setPageContent({
          ...pageContent,
          containers: newContainers
        });
        
        // Show success alert
        Swal.fire({
          icon: 'success',
          title: 'Section Added',
          text: 'New section has been added after the target section',
          timer: 2000,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });
      }
      return;
    }
    
    // Rule 1b: Section widgets can only be placed on canvas or other sections
    if (activeData?.widget?.type === 'section' && overData?.type !== 'canvas' && overData?.type !== 'section') {
      Swal.fire({
        icon: 'error',
        title: 'Invalid Placement',
        text: 'Section widgets can only be placed on the main canvas or after other sections',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Rule 2: Container widgets cannot be placed inside other containers/columns
    if (activeData?.widget?.type === 'container' && overData?.type === 'column') {
      Swal.fire({
        icon: 'warning',
        title: 'Invalid Placement',
        text: 'Container widgets cannot be placed inside other containers or columns',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Rule 3: Auto-create section for regular widgets dropped on canvas
    if (activeData?.type === 'widget-template' && 
        activeData?.widget?.type !== 'container' && 
        activeData?.widget?.type !== 'section' && 
        overData?.type === 'canvas') {
      
      console.log('Auto-creating section for widget dropped on canvas');
      
      // Auto-create a section container with the widget inside
      const newContainerId = `section-${Date.now()}`;
      const newColumnId = `column-${Date.now()}`;
      
      // Create widget with unique ID
      const newWidget = {
        ...activeData.widget,
        id: `widget-${Date.now()}`,
        content: activeData.widget.content || {}
      };
      
      addContainer({
        id: newContainerId,
        type: 'section',
        columns: [{
          id: newColumnId,
          width: '100%',
          widgets: [newWidget], // Place the widget inside the auto-created column
          settings: {}
        }],
        settings: {
          padding: '40px 20px',
          margin: '0px',
          backgroundColor: 'transparent'
        }
      });
      
      // Show user-friendly message
      console.info(`✅ Auto-created section for ${activeData.widget.type} widget`);
      return;
    }

    // Handle widget drop from panel to column
    if (activeData?.type === 'widget-template' && overData?.type === 'column') {
      addWidgetToColumn(activeData.widget, overData.columnId, overData.containerId);
      return;
    }

    // Handle section widget drop on canvas
    if (activeData?.type === 'widget-template' && 
        activeData?.widget?.type === 'section' && 
        overData?.type === 'canvas') {
      
      // Create a new section container
      const newContainerId = `section-${Date.now()}`;
      
      addContainer({
        id: newContainerId,
        type: 'section',
        columns: [{
          id: `column-${Date.now()}`,
          width: '100%',
          widgets: [],
          settings: {}
        }],
        settings: {
          padding: '40px 20px',
          margin: '0px',
          backgroundColor: 'transparent'
        },
        widgetType: 'section',
        widgetSettings: activeData.widget.content || {}
      });
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
        console.log('[DragAndDrop] Moving widget between columns:', {
          widgetId: activeData.widget.id,
          fromColumn: activeData.columnId,
          toColumn: overData.columnId,
          fromContainer: activeData.containerId,
          toContainer: overData.containerId
        });
        
        // Move widget to different column using store method
        moveWidgetBetweenColumns(
          activeData.widget.id, 
          activeData.columnId, 
          overData.columnId,
          overData.containerId
        );
      }
      return;
    }
    
    // Handle widget drop on another widget (for reordering between columns)
    if (activeData?.type === 'widget' && overData?.type === 'widget' && 
        activeData.columnId !== overData.columnId) {
      console.log('[DragAndDrop] Moving widget via widget drop:', {
        widgetId: activeData.widget.id,
        fromColumn: activeData.columnId,
        toColumn: overData.columnId,
        fromContainer: activeData.containerId,
        toContainer: overData.containerId
      });
      
      // Move widget to the same column as the target widget
      moveWidgetBetweenColumns(
        activeData.widget.id,
        activeData.columnId,
        overData.columnId,
        overData.containerId
      );
      return;
    }

    // Handle container reordering - check for container drops or drops within containers
    if (activeData?.type === 'container' && (overData?.type === 'container' || overData?.containerId)) {
      console.log('[DragAndDrop] Container reordering detected:', {
        activeId: active.id,
        overId: over.id,
        activeData,
        overData
      });
      
      const { pageContent } = usePageBuilderStore.getState();
      const oldIndex = pageContent.containers.findIndex(c => c.id === active.id);
      
      // Find target container ID - either direct container or container of the dropped element
      const targetContainerId = overData?.type === 'container' ? over.id : overData?.containerId;
      const newIndex = pageContent.containers.findIndex(c => c.id === targetContainerId);
      
      console.log('[DragAndDrop] Container reordering indices:', {
        oldIndex,
        newIndex,
        targetContainerId,
        containersCount: pageContent.containers.length
      });
      
      if (oldIndex !== newIndex && oldIndex !== -1 && newIndex !== -1) {
        console.log('[DragAndDrop] Executing container reorder');
        reorderContainers(oldIndex, newIndex);
        
        // Show success feedback
        console.log('✅ Section reordered successfully');
      } else {
        console.warn('[DragAndDrop] Container reorder skipped - invalid indices or same position');
      }
      return;
    }
    } catch (error) {
      console.error('Error in handleDragEnd:', error);
      console.error('Active data:', active.data.current);
      console.error('Over data:', over?.data?.current);
    }
  };

  return {
    handleDragStart,
    handleDragOver,
    handleDragEnd
  };
};