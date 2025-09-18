import React, { Fragment } from 'react';
import { useDroppable } from '@dnd-kit/core';
import { SortableContext, verticalListSortingStrategy } from '@dnd-kit/sortable';
import SortableContainer from './SortableContainer';
import EmptyCanvasState from './EmptyCanvasState';
import DropZone from './DropZone';
import DragGhost from '../DragPreview/DragGhost';
import RealTimePreview from '../DragPreview/RealTimePreview';
import CanvasPlusButton from './CanvasPlusButton';
import SectionQuickAdd from './SectionQuickAdd';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';

const Canvas = ({ content, onUpdate, onSelectWidget, selectedWidget, hoveredDropZone }) => {
  const { dragState } = usePageBuilderStore();
  const { isDraggingSection } = dragState;
  
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
          className={`page-builder-content min-h-screen bg-white shadow-sm rounded-lg transition-all duration-200 p-4 ${
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
              {/* Section Quick Add - Before first section */}
              <SectionQuickAdd
                position={0}
                key="quick-add-before-0"
              />

              {/* Drop zone before first section */}
              {isDraggingSection && (
                <DropZone
                  id="drop-zone-before-0"
                  position="before"
                  index={0}
                />
              )}

              {content.containers.map((container, index) => (
                <Fragment key={container.id}>
                  <SortableContainer
                    container={container}
                    index={index}
                    onUpdate={onUpdate}
                    onSelectWidget={onSelectWidget}
                    selectedWidget={selectedWidget}
                    hoveredDropZone={hoveredDropZone}
                  />

                  {/* Section Quick Add - After each section */}
                  <SectionQuickAdd
                    position={index + 1}
                    containerId={container.id}
                    key={`quick-add-after-${index}`}
                  />

                  {/* Drop zone after each section */}
                  {isDraggingSection && (
                    <DropZone
                      id={`drop-zone-after-${index}`}
                      position="after"
                      index={index + 1}
                      containerId={container.id}
                    />
                  )}
                </Fragment>
              ))}
            </SortableContext>
          ) : (
            <EmptyCanvasState />
          )}
        </div>
      </div>

      {/* Global Drag Ghost - Temporarily disabled to fix drop zone conflicts */}
      {false && <DragGhost />}

      {/* Canvas Plus Button - Always available for adding sections */}
      <CanvasPlusButton />
    </div>
  );
};

export default Canvas;