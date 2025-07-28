import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { usePageBuilderStore } from '@/Store/pageBuilderStore';
import { Monitor, Tablet, Smartphone } from 'lucide-react';

const CanvasToolbar = ({ page }) => {
  const [previewMode, setPreviewMode] = useState('desktop');
  const [isSaving, setIsSaving] = useState(false);
  
  const { pageContent, isDirty, savePage, resetChanges } = usePageBuilderStore();

  const devices = [
    { id: 'desktop', label: 'Desktop', icon: Monitor, width: '100%' },
    { id: 'tablet', label: 'Tablet', icon: Tablet, width: '768px' },
    { id: 'mobile', label: 'Mobile', icon: Smartphone, width: '375px' }
  ];

  const handleSave = async () => {
    setIsSaving(true);
    try {
      await savePage(page.id);
    } catch (error) {
      console.error('Save failed:', error);
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
    <div className="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
      {/* Left Section */}
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

        {/* Page Title */}
        <div>
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

      {/* Center Section - Device Preview */}
      <div className="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
        {devices.map(device => (
          <button
            key={device.id}
            onClick={() => setPreviewMode(device.id)}
            className={`px-3 py-1.5 text-sm font-medium rounded transition-colors ${
              previewMode === device.id
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-600 hover:text-gray-900'
            }`}
            title={device.label}
          >
            <device.icon className="w-4 h-4 mr-1.5" />
            {device.label}
          </button>
        ))}
      </div>

      {/* Right Section */}
      <div className="flex items-center space-x-3">
        {/* Undo/Redo */}
        <div className="flex space-x-1">
          <button 
            onClick={handleUndo}
            className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors"
            title="Undo"
            disabled
          >
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
            </svg>
          </button>
          <button 
            onClick={handleRedo}
            className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors"
            title="Redo"
            disabled
          >
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 10h-10a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" />
            </svg>
          </button>
        </div>

        {/* Preview Button */}
        <button
          onClick={handlePreview}
          className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
        >
          <svg className="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          Preview
        </button>

        {/* Reset Button */}
        {isDirty && (
          <button
            onClick={resetChanges}
            className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          >
            Reset
          </button>
        )}

        {/* Save Button */}
        <button
          onClick={handleSave}
          disabled={!isDirty || isSaving}
          className={`px-4 py-2 text-sm font-medium rounded-md transition-colors ${
            isDirty && !isSaving
              ? 'bg-blue-600 text-white hover:bg-blue-700'
              : 'bg-gray-300 text-gray-500 cursor-not-allowed'
          }`}
        >
          {isSaving ? (
            <>
              <svg className="w-4 h-4 mr-2 inline animate-spin" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Saving...
            </>
          ) : (
            <>
              <svg className="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
              </svg>
              Save Page
            </>
          )}
        </button>

        {/* Standard Edit Button */}
        <button
          onClick={() => router.get(route('admin.pages.edit', page.id))}
          className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
        >
          <svg className="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Standard Edit
        </button>
      </div>
    </div>
  );
};

export default CanvasToolbar;