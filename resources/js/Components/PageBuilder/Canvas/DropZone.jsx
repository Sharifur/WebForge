import React from 'react';
import { useDroppable } from '@dnd-kit/core';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';

/**
 * DropZone - Visual indicator for section and widget drop areas
 *
 * Shows where sections and widgets can be dropped during drag operations
 * Provides visual feedback and handles drop events
 */
const DropZone = ({ 
  id,
  position, // 'before' | 'after'
  index,    // insertion index
  containerId = null, // container ID for 'after' positions
  className = ''
}) => {
  const { dragState, setActiveDropZone } = usePageBuilderStore();
  const { activeDropZone, isDragging, draggedItem } = dragState;

  const isActive = activeDropZone?.id === id;
  const isDraggingSection = dragState.isDraggingSection;
  const isDraggingWidgetFromPanel = isDragging && draggedItem?.type === 'widget-template';
  
  const {
    setNodeRef,
    isOver
  } = useDroppable({
    id: id,
    data: {
      type: isDraggingSection ? 'section-drop-zone' : 'widget-drop-zone',
      position: position,
      index: index,
      containerId: containerId
    }
  });

  // Don't render if not dragging a section or widget from panel
  if (!isDraggingSection && !isDraggingWidgetFromPanel) {
    return null;
  }

  const handleMouseEnter = () => {
    setActiveDropZone({
      id,
      position,
      index,
      containerId
    });
  };

  const handleMouseLeave = () => {
    if (activeDropZone?.id === id) {
      setActiveDropZone(null);
    }
  };

  return (
    <div
      ref={setNodeRef}
      className={`drop-zone ${position} ${isActive || isOver ? 'active' : ''} ${className}`}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      style={{
        height: isActive || isOver ? '60px' : '8px',
        borderRadius: '8px',
        background: isActive || isOver 
          ? 'linear-gradient(90deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1))'
          : 'transparent',
        border: isActive || isOver
          ? '2px dashed #3b82f6'
          : '2px dashed transparent',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        position: 'relative',
        zIndex: 10
      }}
    >
      {(isActive || isOver) && (
        <div className="drop-indicator">
          <div className="flex items-center space-x-2 text-blue-600 font-medium text-sm">
            <svg 
              className="w-5 h-5" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path 
                strokeLinecap="round" 
                strokeLinejoin="round" 
                strokeWidth={2} 
                d="M19 14l-7 7m0 0l-7-7m7 7V3" 
              />
            </svg>
            <span>
              {isDraggingSection
                ? (position === 'before'
                    ? 'Drop section at the beginning'
                    : `Drop section ${containerId ? 'after this section' : 'at the end'}`)
                : (position === 'before'
                    ? 'Create new section at the beginning'
                    : `Create new section ${containerId ? 'after this section' : 'at the end'}`)
              }
            </span>
            <svg 
              className="w-5 h-5" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path 
                strokeLinecap="round" 
                strokeLinejoin="round" 
                strokeWidth={2} 
                d="M19 14l-7 7m0 0l-7-7m7 7V3" 
              />
            </svg>
          </div>
        </div>
      )}
      
      {/* Subtle inactive indicator */}
      {!isActive && !isOver && (
        <div 
          className="drop-hint"
          style={{
            width: '100%',
            height: '2px',
            background: 'rgba(59, 130, 246, 0.3)',
            borderRadius: '1px'
          }}
        />
      )}
    </div>
  );
};

export default DropZone;