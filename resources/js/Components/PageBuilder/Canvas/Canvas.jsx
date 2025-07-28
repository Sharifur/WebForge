import React from 'react';
import { useDroppable } from '@dnd-kit/core';
import { SortableContext, verticalListSortingStrategy } from '@dnd-kit/sortable';
import SortableContainer from './SortableContainer';
import EmptyCanvasState from './EmptyCanvasState';

const Canvas = ({ content, onUpdate, onSelectWidget, selectedWidget, hoveredDropZone }) => {
  const { setNodeRef, isOver } = useDroppable({
    id: 'canvas',
    data: { type: 'canvas' }
  });

  const containerIds = content?.containers?.map(c => c.id) || [];
  
  // Check if this canvas is being hovered with valid/invalid drop
  const isCanvasHovered = hoveredDropZone?.id === 'canvas';
  const isValidDrop = hoveredDropZone?.isValid;

  return (
    <div className="flex-1 overflow-auto bg-gray-100 p-6">
      <div className="max-w-6xl mx-auto">
        <div 
          ref={setNodeRef}
          className={`min-h-screen bg-white shadow-sm rounded-lg transition-all duration-200 p-4 ${
            isCanvasHovered && isValidDrop ? 'ring-2 ring-blue-400 bg-blue-50' : ''
          } ${
            isCanvasHovered && !isValidDrop ? 'ring-2 ring-red-400 bg-red-50' : ''
          }`}
        >
          {containerIds.length > 0 ? (
            <SortableContext 
              items={containerIds}
              strategy={verticalListSortingStrategy}
            >
              {content.containers.map((container, index) => (
                <SortableContainer
                  key={container.id}
                  container={container}
                  index={index}
                  onUpdate={onUpdate}
                  onSelectWidget={onSelectWidget}
                  selectedWidget={selectedWidget}
                  hoveredDropZone={hoveredDropZone}
                />
              ))}
            </SortableContext>
          ) : (
            <EmptyCanvasState />
          )}
        </div>
      </div>
    </div>
  );
};

export default Canvas;