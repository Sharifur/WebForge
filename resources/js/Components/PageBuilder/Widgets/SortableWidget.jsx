import React, { useState, useEffect, useRef } from 'react';
import { useSortable } from '@dnd-kit/sortable';
import { useDroppable } from '@dnd-kit/core';
import { CSS } from '@dnd-kit/utilities';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import WidgetRenderer from './WidgetRenderer';

const SortableWidget = ({ 
  widget, 
  widgetIndex, 
  columnId, 
  containerId, 
  onUpdate, 
  onSelect, 
  isSelected 
}) => {
  const [isHovered, setIsHovered] = useState(false);
  const [contentHeight, setContentHeight] = useState(0);
  const widgetRef = useRef(null);
  const { removeWidget, updateWidget, dragState } = usePageBuilderStore();
  
  // Track content height for better drop positioning
  useEffect(() => {
    const updateContentHeight = () => {
      if (widgetRef.current) {
        const height = widgetRef.current.scrollHeight;
        setContentHeight(height);
      }
    };
    
    updateContentHeight();
    
    // Update height when content changes
    const resizeObserver = new ResizeObserver(updateContentHeight);
    if (widgetRef.current) {
      resizeObserver.observe(widgetRef.current);
    }
    
    return () => {
      resizeObserver.disconnect();
    };
  }, [widget]);

  // Determine if this is a large content widget (over 200px) - must be defined before useDroppable
  const isLargeContent = contentHeight > 200;

  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging
  } = useSortable({
    id: widget.id,
    data: {
      type: 'widget',
      widget,
      widgetIndex,
      columnId,
      containerId
    }
  });

  // Add droppable zones for large widgets
  const {
    setNodeRef: setDroppableRef,
    isOver: isDroppableOver
  } = useDroppable({
    id: `${widget.id}-drop-target`,
    data: {
      type: 'widget-target',
      widget,
      widgetIndex,
      columnId,
      containerId,
      isLargeContent
    }
  });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.5 : 1
  };

  const handleDeleteWidget = () => {
    if (confirm('Are you sure you want to delete this widget?')) {
      removeWidget(widget.id);
    }
  };

  const handleDuplicateWidget = () => {
    const duplicatedWidget = {
      ...widget,
      id: `widget-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
    };

    onUpdate(prev => ({
      ...prev,
      containers: prev.containers.map(container => {
        if (container.id === containerId) {
          return {
            ...container,
            columns: container.columns.map(column => {
              if (column.id === columnId) {
                const newWidgets = [...column.widgets];
                newWidgets.splice(widgetIndex + 1, 0, duplicatedWidget);
                return { ...column, widgets: newWidgets };
              }
              return column;
            })
          };
        }
        return container;
      })
    }));
  };

  const handleSelectWidget = (e) => {
    e.stopPropagation();
    onSelect(widget);
  };

  // Check if we're currently dragging another widget
  const { isDragging: isGlobalDragging, draggedItem } = dragState;
  const isDraggingOtherWidget = isGlobalDragging && draggedItem?.type === 'widget' && draggedItem?.widget?.id !== widget.id;

  return (
    <div
      ref={(node) => {
        setNodeRef(node);
        setDroppableRef(node);
        widgetRef.current = node;
      }}
      style={style}
      className={`relative group mb-4 ${isDragging ? 'z-50' : ''} ${
        isLargeContent ? 'widget-large-content' : ''
      } ${
        isDroppableOver && isLargeContent ? 'widget-drop-over' : ''
      }`}
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      onClick={handleSelectWidget}
      data-widget-height={contentHeight}
      data-widget-large={isLargeContent}
    >
      {/* Widget Controls - Fixed position at top-right */}
      {(isHovered || isSelected) && !isDragging && (
        <div className="absolute top-1 right-1 z-20 flex space-x-1">
        <button 
          {...attributes}
          {...listeners}
          className="p-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors flex items-center shadow-sm"
          title="Drag to reorder"
          onClick={(e) => e.stopPropagation()}
        >
          <svg className="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 8h16M4 16h16" />
          </svg>
        </button>
        <button 
          onClick={(e) => {
            e.stopPropagation();
            handleDuplicateWidget();
          }}
          className="p-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition-colors flex items-center shadow-sm"
          title="Duplicate widget"
        >
          <svg className="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
          </svg>
        </button>
        <button 
          onClick={(e) => {
            e.stopPropagation();
            handleSelectWidget(e);
          }}
          className="p-1 bg-gray-600 text-white rounded text-xs hover:bg-gray-700 transition-colors flex items-center shadow-sm"
          title="Edit widget settings"
        >
          <svg className="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
        <button 
          onClick={(e) => {
            e.stopPropagation();
            handleDeleteWidget();
          }}
          className="p-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition-colors flex items-center shadow-sm"
          title="Delete widget"
        >
          <svg className="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
      )}

      {/* Large Widget Drop Zone Overlays - Only show when dragging another widget over large content */}
      {isLargeContent && isDraggingOtherWidget && (
        <>
          {/* Drop Before Overlay */}
          <div
            className={`absolute top-0 left-0 right-0 z-30 transition-all duration-200 ${
              isDroppableOver ? 'h-20 bg-blue-100 border-2 border-dashed border-blue-400' : 'h-8 bg-blue-50 border border-dashed border-blue-300'
            }`}
            style={{
              background: isDroppableOver ? 'linear-gradient(180deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.1))' : 'rgba(59, 130, 246, 0.05)',
              borderRadius: '4px 4px 0 0'
            }}
          >
            {isDroppableOver && (
              <div className="flex items-center justify-center h-full text-blue-600 font-medium text-sm">
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                </svg>
                Drop widget before
              </div>
            )}
          </div>

          {/* Drop After Overlay */}
          <div
            className={`absolute bottom-0 left-0 right-0 z-30 transition-all duration-200 ${
              isDroppableOver ? 'h-20 bg-green-100 border-2 border-dashed border-green-400' : 'h-8 bg-green-50 border border-dashed border-green-300'
            }`}
            style={{
              background: isDroppableOver ? 'linear-gradient(0deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1))' : 'rgba(16, 185, 129, 0.05)',
              borderRadius: '0 0 4px 4px'
            }}
          >
            {isDroppableOver && (
              <div className="flex items-center justify-center h-full text-green-600 font-medium text-sm">
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                </svg>
                Drop widget after
              </div>
            )}
          </div>

          {/* Center Content Overlay - for indicating replacement */}
          {isDroppableOver && (
            <div className="absolute inset-0 z-25 bg-yellow-100 bg-opacity-20 border-2 border-dashed border-yellow-400 rounded flex items-center justify-center">
              <div className="bg-white bg-opacity-90 px-4 py-2 rounded-md shadow-lg text-yellow-700 font-medium text-sm">
                <svg className="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                Widget drop area
              </div>
            </div>
          )}
        </>
      )}

      {/* Widget Content */}
      <div 
        className={`transition-all duration-200 ${
          isSelected ? 'ring-2 ring-blue-500 ring-opacity-50' : ''
        } ${isHovered ? 'ring-1 ring-blue-300 ring-opacity-30' : ''}`}
        style={{
          margin: widget.style?.margin || '0',
          padding: widget.style?.padding || '0',
          ...widget.style
        }}
      >
        <WidgetRenderer widget={widget} />
      </div>
    </div>
  );
};

export default SortableWidget;