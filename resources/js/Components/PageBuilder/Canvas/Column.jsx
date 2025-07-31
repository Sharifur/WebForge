import React from 'react';
import { useDroppable } from '@dnd-kit/core';
import { SortableContext, verticalListSortingStrategy } from '@dnd-kit/sortable';
import SortableWidget from '../Widgets/SortableWidget';

const Column = ({ column, columnIndex, containerId, onUpdate, onSelectWidget, selectedWidget, hoveredDropZone }) => {
  const [isHovered, setIsHovered] = React.useState(false);
  const { setNodeRef, isOver } = useDroppable({
    id: column.id,
    data: { 
      type: 'column', 
      columnId: column.id, 
      containerId,
      columnIndex
    }
  });

  const widgetIds = column.widgets?.map(w => w.id) || [];
  
  // Check if this column is being hovered with valid/invalid drop
  const isColumnHovered = hoveredDropZone?.id === column.id;
  const isValidDrop = hoveredDropZone?.isValid;

  return (
    <div
      ref={setNodeRef}
      className={`relative min-h-20 transition-all duration-200 ${
        isColumnHovered && isValidDrop
          ? 'bg-blue-50 border-2 border-dashed border-blue-400' 
          : isColumnHovered && !isValidDrop
          ? 'bg-red-50 border-2 border-dashed border-red-400'
          : 'border-2 border-dashed border-transparent hover:border-gray-300'
      }`}
      style={{
        flexBasis: column.width || 'auto',
        flexGrow: column.width ? 0 : 1,
        flexShrink: 0,
        padding: column.settings?.padding || '10px',
        // Only spread CSS-related properties, filter out internal settings
        ...Object.fromEntries(
          Object.entries(column.settings || {}).filter(([key]) => 
            !['padding'].includes(key) // Filter out properties already handled
          )
        )
      }}
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
    >
      {/* Column Controls - Fixed position at top-left */}
      {isHovered && (
        <div className="absolute top-1 left-1 z-10">
        <button 
          onClick={() => onSelectWidget({ type: 'column', ...column, columnId: column.id, containerId })}
          className="p-1 bg-purple-600 text-white rounded text-xs hover:bg-purple-700 transition-colors flex items-center shadow-sm"
          title="Edit column settings"
        >
          <svg className="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
      </div>
      )}
      {/* Column Content */}
      {widgetIds.length > 0 ? (
        <SortableContext 
          items={widgetIds}
          strategy={verticalListSortingStrategy}
        >
          {column.widgets.map((widget, widgetIndex) => (
            <SortableWidget
              key={widget.id}
              widget={widget}
              widgetIndex={widgetIndex}
              columnId={column.id}
              containerId={containerId}
              onUpdate={onUpdate}
              onSelect={onSelectWidget}
              isSelected={selectedWidget?.id === widget.id}
            />
          ))}
        </SortableContext>
      ) : (
        /* Empty Column State */
        <div className="flex items-center justify-center h-24 text-gray-400 text-sm">
          {isOver ? (
            <div className="flex flex-col items-center">
              <svg className="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              <span>Drop widget here</span>
            </div>
          ) : (
            <div className="flex flex-col items-center opacity-50">
              <svg className="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              <span>Drop widgets here</span>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default Column;