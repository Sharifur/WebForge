import React from 'react';
import { Head } from '@inertiajs/react';
import { DndContext, DragOverlay, closestCenter } from '@dnd-kit/core';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import { useDragAndDrop } from '@/Hooks/useDragAndDrop';
import { useDynamicCSS } from '@/Hooks/useDynamicCSS';
import { useEditingSession } from '@/Hooks/useEditingSession';
import WidgetPanel from '@/Components/PageBuilder/Panels/WidgetPanel';
import Canvas from '@/Components/PageBuilder/Canvas/Canvas';
import SettingsPanel from '@/Components/PageBuilder/Panels/SettingsPanel';
import CanvasToolbar from '@/Components/PageBuilder/Canvas/CanvasToolbar';
import MovableNavigationDialog from '@/Components/PageBuilder/Navigation/MovableNavigationDialog';
import DragOverlayContent from '@/Components/PageBuilder/DragDrop/DragOverlayContent';
import EditingConflictModal from '@/Components/PageBuilder/EditingConflictModal';

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
  const { injectPageCSS } = useDynamicCSS();

  // Editing session management
  const {
    conflictModal,
    handleTakeover,
    handleConflictExit,
    closeConflictModal,
    startSession,
    endSession,
    hasActiveSession,
    activeEditors,
    sessionState
  } = useEditingSession(page.id);

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

  // Initialize editing session and page content on mount
  React.useEffect(() => {
    const initContent = async () => {
      try {
        // Set current page ID for auto-save
        usePageBuilderStore.getState().setCurrentPageId(page.id);
        
        // Start editing session first
        await startSession('full_page');

        // Then load page content
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

  // Inject CSS whenever page content changes
  React.useEffect(() => {
    if (pageContent && pageContent.containers && pageContent.containers.length > 0) {
      // Debounce CSS injection to avoid excessive API calls
      const timeoutId = setTimeout(() => {
        injectPageCSS(pageContent);
      }, 300);

      return () => clearTimeout(timeoutId);
    }
  }, [pageContent, injectPageCSS]);

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

          {/* Movable Navigation Dialog */}
          <MovableNavigationDialog />
        </DndContext>

        {/* Editing Conflict Modal */}
        <EditingConflictModal
          isOpen={conflictModal.isOpen}
          conflictData={conflictModal.conflictData}
          onTakeover={handleTakeover}
          onExit={handleConflictExit}
          onClose={closeConflictModal}
        />

        {/* Active Editors Indicator */}
        {hasActiveSession && activeEditors.length > 1 && (
          <div className="fixed top-4 right-4 z-40">
            <div className="bg-white border border-gray-200 rounded-lg shadow-lg p-3">
              <div className="flex items-center space-x-2">
                <div className="flex -space-x-2">
                  {activeEditors.slice(0, 3).map((editor, index) => (
                    <div
                      key={editor.id}
                      className="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center border-2 border-white text-xs font-medium text-blue-600"
                      title={`${editor.admin?.name} (${editor.editing_section})`}
                    >
                      {editor.admin?.name?.[0]?.toUpperCase() || '?'}
                    </div>
                  ))}
                  {activeEditors.length > 3 && (
                    <div className="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center border-2 border-white text-xs font-medium text-gray-600">
                      +{activeEditors.length - 3}
                    </div>
                  )}
                </div>
                <div className="text-sm text-gray-600">
                  {activeEditors.length === 2 ? 'One other editor' : `${activeEditors.length - 1} other editors`}
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
};

export default PageBuilder;