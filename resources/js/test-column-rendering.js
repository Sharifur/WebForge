// Test script to verify column rendering fix
// This demonstrates how widgets maintain proper column bindings when moved

import { usePageBuilderStore } from './Store/pageBuilderStore';

// Test scenario: Moving widgets between columns in different containers
const testColumnRendering = () => {
  const store = usePageBuilderStore.getState();
  
  // Setup test data
  const testContent = {
    containers: [
      {
        id: 'container-1',
        type: 'section',
        columns: [
          {
            id: 'column-1-1',
            width: '50%',
            widgets: [
              {
                id: 'widget-1',
                type: 'heading',
                content: { text: 'Widget 1 in Column 1-1' }
              }
            ]
          },
          {
            id: 'column-1-2',
            width: '50%',
            widgets: [
              {
                id: 'widget-2',
                type: 'paragraph',
                content: { text: 'Widget 2 in Column 1-2' }
              }
            ]
          }
        ]
      },
      {
        id: 'container-2',
        type: 'section',
        columns: [
          {
            id: 'column-2-1',
            width: '33.33%',
            widgets: []
          },
          {
            id: 'column-2-2',
            width: '33.33%',
            widgets: [
              {
                id: 'widget-3',
                type: 'button',
                content: { text: 'Widget 3 in Column 2-2' }
              }
            ]
          },
          {
            id: 'column-2-3',
            width: '33.33%',
            widgets: []
          }
        ]
      }
    ]
  };
  
  // Initialize store with test data
  store.initializePageContent(testContent);
  
  console.log('Initial state:', JSON.stringify(store.pageContent, null, 2));
  
  // Test 1: Move widget within same container (column 1-1 to column 1-2)
  console.log('\n=== Test 1: Moving widget-1 from column-1-1 to column-1-2 ===');
  store.moveWidgetBetweenColumns('widget-1', 'column-1-1', 'column-1-2', 'container-1');
  
  // Verify widget moved correctly
  const container1 = store.pageContent.containers.find(c => c.id === 'container-1');
  const column11 = container1.columns.find(col => col.id === 'column-1-1');
  const column12 = container1.columns.find(col => col.id === 'column-1-2');
  
  console.log('Column 1-1 widgets:', column11.widgets.map(w => w.id));
  console.log('Column 1-2 widgets:', column12.widgets.map(w => w.id));
  
  // Test 2: Move widget across containers (column 1-2 to column 2-1)
  console.log('\n=== Test 2: Moving widget-1 from column-1-2 to column-2-1 ===');
  store.moveWidgetBetweenColumns('widget-1', 'column-1-2', 'column-2-1', 'container-2');
  
  // Verify widget moved to different container
  const container2 = store.pageContent.containers.find(c => c.id === 'container-2');
  const column21 = container2.columns.find(col => col.id === 'column-2-1');
  
  console.log('Column 1-2 widgets:', column12.widgets.map(w => w.id));
  console.log('Column 2-1 widgets:', column21.widgets.map(w => w.id));
  
  // Test 3: Move multiple widgets to verify order preservation
  console.log('\n=== Test 3: Moving multiple widgets ===');
  store.moveWidgetBetweenColumns('widget-2', 'column-1-2', 'column-2-1', 'container-2');
  store.moveWidgetBetweenColumns('widget-3', 'column-2-2', 'column-2-1', 'container-2');
  
  console.log('Final Column 2-1 widgets:', column21.widgets.map(w => w.id));
  
  console.log('\nFinal state:', JSON.stringify(store.pageContent, null, 2));
};

// Export for use in browser console
window.testColumnRendering = testColumnRendering;

console.log('Test script loaded. Run window.testColumnRendering() in console to test.');