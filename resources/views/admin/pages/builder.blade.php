@extends('admin.layouts.admin')

@section('title', 'Page Builder - ' . $page->title)

@section('breadcrumbs')
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a></li>
        <li class="text-gray-500">/</li>
        <li><a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-700">Pages</a></li>
        <li class="text-gray-500">/</li>
        <li class="text-gray-900 font-medium">Page Builder</li>
    </ol>
@endsection

@section('page-header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Page Builder</h2>
            <p class="text-gray-600 mt-1">{{ $page->title }}</p>
        </div>
        <div class="flex space-x-3">
            <x-admin.button href="{{ route('admin.pages.edit', $page) }}" variant="outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Standard Edit
            </x-admin.button>
            <x-admin.button href="{{ route('admin.pages.show', $page) }}" variant="outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Preview
            </x-admin.button>
            <x-admin.button variant="primary" onclick="savePage()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Save Page
            </x-admin.button>
        </div>
    </div>
@endsection

@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar with Components -->
    <div class="w-80 bg-white shadow-lg border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Components</h3>
            <p class="text-sm text-gray-600">Drag components to build your page</p>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4">
            <div class="space-y-4">
                <!-- Text Components -->
                <div class="border border-gray-200 rounded-lg p-3">
                    <h4 class="font-medium text-gray-900 mb-3">Text</h4>
                    <div class="space-y-2">
                        <div class="draggable-component p-2 border border-dashed border-gray-300 rounded cursor-move hover:border-purple-400 hover:bg-purple-50 transition-colors" 
                             data-component="heading">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                                <span class="text-sm">Heading</span>
                            </div>
                        </div>
                        
                        <div class="draggable-component p-2 border border-dashed border-gray-300 rounded cursor-move hover:border-purple-400 hover:bg-purple-50 transition-colors" 
                             data-component="paragraph">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <span class="text-sm">Paragraph</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Layout Components -->
                <div class="border border-gray-200 rounded-lg p-3">
                    <h4 class="font-medium text-gray-900 mb-3">Layout</h4>
                    <div class="space-y-2">
                        <div class="draggable-component p-2 border border-dashed border-gray-300 rounded cursor-move hover:border-purple-400 hover:bg-purple-50 transition-colors" 
                             data-component="container">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                                <span class="text-sm">Container</span>
                            </div>
                        </div>
                        
                        <div class="draggable-component p-2 border border-dashed border-gray-300 rounded cursor-move hover:border-purple-400 hover:bg-purple-50 transition-colors" 
                             data-component="columns">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a1 1 0 00-1 1v14a1 1 0 001 1h4m0-16v16m0-16h6m-6 0v16m6-16h4a1 1 0 011 1v14a1 1 0 01-1 1h-4m0-16v16" />
                                </svg>
                                <span class="text-sm">Columns</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Components -->
                <div class="border border-gray-200 rounded-lg p-3">
                    <h4 class="font-medium text-gray-900 mb-3">Media</h4>
                    <div class="space-y-2">
                        <div class="draggable-component p-2 border border-dashed border-gray-300 rounded cursor-move hover:border-purple-400 hover:bg-purple-50 transition-colors" 
                             data-component="image">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm">Image</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Canvas -->
    <div class="flex-1 flex flex-col">
        <!-- Canvas Toolbar -->
        <div class="bg-white border-b border-gray-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <select class="text-sm border border-gray-300 rounded-md px-3 py-1">
                        <option>Desktop (1200px)</option>
                        <option>Tablet (768px)</option>
                        <option>Mobile (375px)</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded" title="Undo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </button>
                    <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded" title="Redo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Canvas Area -->
        <div class="flex-1 bg-gray-100 p-8 overflow-auto">
            <div class="max-w-6xl mx-auto">
                <!-- Page Canvas -->
                <div id="page-canvas" class="bg-white shadow-lg rounded-lg min-h-screen p-8 drop-zone">
                    <div class="text-center py-20 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <p class="text-lg font-medium">Drop components here to start building</p>
                        <p class="text-sm mt-2">Drag components from the sidebar to create your page</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties Panel -->
    <div id="properties-panel" class="w-80 bg-white shadow-lg border-l border-gray-200 hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Properties</h3>
            <p class="text-sm text-gray-600">Configure selected component</p>
        </div>
        
        <div class="p-4">
            <div id="properties-content">
                <p class="text-gray-500 text-center py-8">Select a component to edit its properties</p>
            </div>
        </div>
    </div>
</div>

<script>
function savePage() {
    const canvas = document.getElementById('page-canvas');
    const content = canvas.innerHTML;
    
    // Here you would normally send the content to your backend
    // For now, just show a success message
    alert('Page saved successfully! (This is a demo - implement actual save functionality)');
}

// Basic drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const draggableComponents = document.querySelectorAll('.draggable-component');
    const dropZones = document.querySelectorAll('.drop-zone');
    
    draggableComponents.forEach(component => {
        component.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', this.dataset.component);
        });
        component.setAttribute('draggable', 'true');
    });
    
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-purple-400', 'bg-purple-50');
        });
        
        zone.addEventListener('dragleave', function(e) {
            this.classList.remove('border-purple-400', 'bg-purple-50');
        });
        
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-400', 'bg-purple-50');
            
            const componentType = e.dataTransfer.getData('text/plain');
            const componentHtml = getComponentHtml(componentType);
            
            if (this.querySelector('.text-center.py-20')) {
                this.innerHTML = componentHtml;
            } else {
                this.insertAdjacentHTML('beforeend', componentHtml);
            }
        });
    });
});

function getComponentHtml(componentType) {
    const templates = {
        heading: '<h2 class="text-2xl font-bold text-gray-900 mb-4 editable" contenteditable="true">Your Heading Here</h2>',
        paragraph: '<p class="text-gray-700 mb-4 editable" contenteditable="true">Your paragraph text goes here. Click to edit this content.</p>',
        container: '<div class="container mx-auto px-4 py-8 border border-dashed border-gray-300 drop-zone"><p class="text-center text-gray-500">Drop components here</p></div>',
        columns: '<div class="grid grid-cols-2 gap-6 mb-6"><div class="p-4 border border-dashed border-gray-300 drop-zone"><p class="text-center text-gray-500">Column 1</p></div><div class="p-4 border border-dashed border-gray-300 drop-zone"><p class="text-center text-gray-500">Column 2</p></div></div>',
        image: '<img src="https://via.placeholder.com/600x300?text=Click+to+change+image" alt="Placeholder" class="w-full h-auto mb-4 cursor-pointer editable-image" />'
    };
    
    return templates[componentType] || '<div>Unknown component</div>';
}
</script>

<style>
.drop-zone {
    min-height: 50px;
}

.editable:focus {
    outline: 2px solid #8B5CF6;
    outline-offset: 2px;
    border-radius: 4px;
}

.draggable-component:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>
@endsection