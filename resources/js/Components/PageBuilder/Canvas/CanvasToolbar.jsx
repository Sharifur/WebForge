import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import { Monitor, Tablet, Smartphone, Settings, List } from 'lucide-react';

const CanvasToolbar = ({ page }) => {
  const [isSaving, setIsSaving] = useState(false);

  const {
    pageContent,
    isDirty,
    settingsPanelVisible,
    selectedWidget,
    currentDevice,
    savePage,
    resetChanges,
    toggleSettingsPanel,
    publishPage,
    navigationDialogVisible,
    toggleNavigationDialog,
    setCurrentDevice,
    initializeDeviceFromStorage
  } = usePageBuilderStore();

  // Initialize device from storage on mount
  useEffect(() => {
    initializeDeviceFromStorage();
  }, [initializeDeviceFromStorage]);

  const devices = [
    { id: 'desktop', label: 'Desktop', icon: Monitor, width: '100%' },
    { id: 'tablet', label: 'Tablet', icon: Tablet, width: '768px' },
    { id: 'mobile', label: 'Mobile', icon: Smartphone, width: '375px' }
  ];

  const handleSave = async () => {
    setIsSaving(true);
    try {
      await savePage(page.id);
      // Show success message (could use a toast library)
      console.log('Page saved successfully');
    } catch (error) {
      console.error('Save failed:', error);
      // Show error message (could use a toast library)
      alert('Failed to save page. Please try again.');
    } finally {
      setIsSaving(false);
    }
  };

  const handlePublish = async () => {
    setIsSaving(true);
    try {
      // First save the current state
      await savePage(page.id);
      // Then publish
      await publishPage(page.id);
      console.log('Page published successfully');
      alert('Page published successfully!');
    } catch (error) {
      console.error('Publish failed:', error);
      alert('Failed to publish page. Please try again.');
    } finally {
      setIsSaving(false);
    }
  };

  const handlePreview = () => {
    // Open preview in new tab
    window.open(route('page.show', page.slug), '_blank');
  };

  const handleUndo = () => {
    // TODO: Implement undo functionality
    console.log('Undo functionality to be implemented');
  };

  const handleRedo = () => {
    // TODO: Implement redo functionality
    console.log('Redo functionality to be implemented');
  };

  return (
    <div className="bg-white border-b border-gray-200">
      {/* Row 1 (Primary) */}
      <div className="px-6 py-3 flex items-center justify-between border-b border-gray-100">
        {/* Left Section - Page Info */}
        <div className="flex items-center space-x-4">
          {/* Back Button */}
          <button
            onClick={() => router.get(route('admin.pages.index'))}
            className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors"
            title="Back to Pages"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>

          {/* Page Title and Slug */}
          <div className="flex flex-col">
            <h1 className="text-lg font-semibold text-gray-900">{page.title}</h1>
            <p className="text-sm text-gray-500">/{page.slug}</p>
          </div>

          {/* Dirty Indicator */}
          {isDirty && (
            <div className="flex items-center text-orange-600">
              <div className="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
              <span className="text-sm">Unsaved changes</span>
            </div>
          )}
        </div>

        {/* Right Section - Primary Actions */}
        <div className="flex items-center space-x-2">
          {/* Preview Button (Icon Only) */}
          <button
            onClick={handlePreview}
            className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors"
            title="Preview Page"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>

          {/* Save Button (Icon Only) */}
          <button
            onClick={handleSave}
            disabled={!isDirty || isSaving}
            className={`p-2 rounded transition-colors ${
              isDirty && !isSaving
                ? 'bg-blue-600 text-white hover:bg-blue-700'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
            }`}
            title={isSaving ? 'Saving...' : 'Save Draft'}
          >
            {isSaving ? (
              <svg className="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            ) : (
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
              </svg>
            )}
          </button>

          {/* Publish Button */}
          <button
            onClick={handlePublish}
            disabled={isSaving}
            className={`px-3 py-2 rounded transition-colors ${
              isSaving
                ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                : 'bg-green-600 text-white hover:bg-green-700'
            }`}
            title={isSaving ? 'Publishing...' : 'Publish Page'}
          >
            {isSaving ? (
              <div className="flex items-center space-x-2">
                <svg className="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span className="text-sm">Publishing...</span>
              </div>
            ) : (
              <div className="flex items-center space-x-2">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <span className="text-sm">Publish</span>
              </div>
            )}
          </button>
        </div>
      </div>

      {/* Row 2 (Secondary) */}
      <div className="px-6 py-2 flex items-center justify-between">
        {/* Left Section - Viewport Controls */}
        <div className="flex items-center space-x-4">
          {/* Responsive Viewport Options (Icons Only) */}
          <div className="flex items-center space-x-1 bg-gray-100 rounded-lg p-1">
            {devices.map(device => (
              <button
                key={device.id}
                onClick={() => setCurrentDevice(device.id)}
                className={`p-2 rounded transition-colors ${
                  currentDevice === device.id
                    ? 'bg-white text-gray-900 shadow-sm'
                    : 'text-gray-600 hover:text-gray-900'
                }`}
                title={device.label}
              >
                <device.icon className="w-4 h-4" />
              </button>
            ))}
          </div>
        </div>

        {/* Right Section - Secondary Actions */}
        <div className="flex items-center space-x-3">
          {/* Navigation Toggle Button */}
          <button
            onClick={toggleNavigationDialog}
            data-navigation-toggle
            className={`p-2 rounded transition-colors ${
              navigationDialogVisible
                ? 'bg-blue-100 text-blue-600 hover:bg-blue-200'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
            }`}
            title={navigationDialogVisible ? 'Hide Page Structure' : 'Show Page Structure'}
          >
            <List className="w-4 h-4" />
          </button>

          {/* Settings Toggle Button */}
          <button
            onClick={toggleSettingsPanel}
            className={`p-2 rounded transition-colors ${
              settingsPanelVisible
                ? 'bg-blue-100 text-blue-600 hover:bg-blue-200'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
            }`}
            title={
              settingsPanelVisible
                ? 'Hide Settings Panel'
                : selectedWidget
                  ? 'Show Settings Panel'
                  : 'Show Settings Panel (Select a widget to edit its properties)'
            }
          >
            <Settings className="w-4 h-4" />
          </button>
          
          {/* Undo/Redo Buttons */}
          <div className="flex items-center space-x-1">
            <button 
              onClick={handleUndo}
              className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              title="Undo"
              disabled
            >
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
              </svg>
            </button>
            <button 
              onClick={handleRedo}
              className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              title="Redo"
              disabled
            >
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 10h-10a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" />
              </svg>
            </button>
          </div>

          {/* Reset Button (shown when dirty) */}
          {isDirty && (
            <button
              onClick={resetChanges}
              className="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
            >
              Reset
            </button>
          )}
        </div>
      </div>
    </div>
  );
};

export default CanvasToolbar;