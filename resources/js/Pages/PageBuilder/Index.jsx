import React from 'react';
import { Head } from '@inertiajs/react';
import { DndContext, DragOverlay, closestCenter } from '@dnd-kit/core';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import { useDragAndDrop } from '@/Hooks/useDragAndDrop';
import WidgetPanel from '@/Components/PageBuilder/Panels/WidgetPanel';
import Canvas from '@/Components/PageBuilder/Canvas/Canvas';
import SettingsPanel from '@/Components/PageBuilder/Panels/SettingsPanel';
import CanvasToolbar from '@/Components/PageBuilder/Canvas/CanvasToolbar';
import DragOverlayContent from '@/Components/PageBuilder/DragDrop/DragOverlayContent';

const PageBuilder = ({ page, widgets, sections, templates }) => {
  const { 
    pageContent, 
    selectedWidget, 
    activePanel,
    isDragging,
    activeId,
    hoveredDropZone,
    settingsPanelVisible,
    sidebarCollapsed,
    setPageContent,
    setSelectedWidget,
    setActivePanel,
    initializePageContent,
    loadPageContent,
    toggleSidebar
  } = usePageBuilderStore();

  const { handleDragStart, handleDragEnd, handleDragOver } = useDragAndDrop();

  // Keyboard shortcut for toggling sidebar
  React.useEffect(() => {
    const handleKeyPress = (event) => {
      // Toggle sidebar with Ctrl+B or Cmd+B
      if ((event.ctrlKey || event.metaKey) && event.key === 'b') {
        event.preventDefault();
        toggleSidebar();
      }
    };

    document.addEventListener('keydown', handleKeyPress);
    return () => document.removeEventListener('keydown', handleKeyPress);
  }, [toggleSidebar]);

  // Initialize page content on mount
  React.useEffect(() => {
    const initContent = async () => {
      try {
        // First try to load from the page builder API
        if (page?.use_page_builder) {
          await loadPageContent(page.id);
        } else if (page?.content && page.content.containers && page.content.containers.length > 0) {
          // Fallback to legacy content format
          initializePageContent(page.content);
        } else {
          // Create default container with one column
          const defaultContainer = {
            id: `container-${Date.now()}`,
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
              backgroundColor: '#ffffff'
            }
          };
          
          initializePageContent({ containers: [defaultContainer] });
        }
      } catch (error) {
        console.error('Failed to load page content:', error);
        
        // Fallback to default content if API fails
        const defaultContainer = {
          id: `container-${Date.now()}`,
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
            backgroundColor: '#ffffff'
          }
        };
        
        initializePageContent({ containers: [defaultContainer] });
      }
    };

    initContent();
  }, [page?.id, page?.use_page_builder]); // Only depend on stable values, not functions

  return (
    <>
      <Head title={`Page Builder - ${page.title}`}>
        <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
        <link href="/css/drop-zones.css" rel="stylesheet" />
      </Head>
      
      <div className="h-screen flex bg-gray-50 overflow-hidden">
        <DndContext 
          collisionDetection={closestCenter}
          onDragStart={handleDragStart}
          onDragOver={handleDragOver}
          onDragEnd={handleDragEnd}
        >
          {/* Left Sidebar - Widget Panel */}
          <WidgetPanel 
            widgets={widgets}
            sections={sections}
            templates={templates}
            activeTab={activePanel}
            onTabChange={setActivePanel}
            collapsed={sidebarCollapsed}
            onToggleCollapse={toggleSidebar}
          />
          
          {/* Main Canvas Area */}
          <div className="flex-1 flex flex-col min-w-0">
            {/* Canvas Toolbar */}
            <CanvasToolbar page={page} />
            
            {/* Canvas */}
            <Canvas 
              content={pageContent}
              onUpdate={setPageContent}
              onSelectWidget={setSelectedWidget}
              selectedWidget={selectedWidget}
              hoveredDropZone={hoveredDropZone}
            />
          </div>
          
          {/* Right Sidebar - Settings Panel */}
          {settingsPanelVisible && (
            <SettingsPanel 
              widget={selectedWidget}
              page={page}
              onUpdate={setPageContent}
              onWidgetUpdate={setSelectedWidget}
            />
          )}
          
          {/* Drag Overlay */}
          <DragOverlay>
            {activeId ? (
              <DragOverlayContent 
                activeId={activeId} 
                widgets={widgets}
                sections={sections}
              />
            ) : null}
          </DragOverlay>
        </DndContext>
      </div>
    </>
  );
};

export default PageBuilder;