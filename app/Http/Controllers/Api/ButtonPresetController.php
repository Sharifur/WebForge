<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ButtonPreset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Plugins\Pagebuilder\Widgets\Basic\ButtonWidget;

class ButtonPresetController extends Controller
{
    /**
     * Get all available button presets (built-in + custom)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ButtonPreset::query();
            
            // Filter by category
            if ($request->has('category')) {
                $query->byCategory($request->get('category'));
            }
            
            // Filter by type (builtin, custom, public)
            if ($request->has('type')) {
                switch ($request->get('type')) {
                    case 'builtin':
                        $query->builtin();
                        break;
                    case 'custom':
                        $query->custom()->where('created_by', Auth::guard('admin')->id());
                        break;
                    case 'public':
                        $query->public();
                        break;
                }
            }
            
            // Search functionality
            if ($request->has('search')) {
                $query->search($request->get('search'));
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            switch ($sortBy) {
                case 'popularity':
                    $query->orderBy('usage_count', $sortOrder);
                    break;
                case 'recent':
                    $query->orderBy('created_at', $sortOrder);
                    break;
                default:
                    $query->orderBy('name', $sortOrder);
            }
            
            $presets = $query->with('creator:id,name')->get();
            
            // Include built-in presets if not filtered out
            if (!$request->has('type') || $request->get('type') === 'builtin') {
                $builtinPresets = $this->getBuiltinPresetsAsCollection();
                $presets = $presets->merge($builtinPresets);
            }
            
            return response()->json([
                'success' => true,
                'data' => $presets->values()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch presets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get popular presets
     */
    public function popular(): JsonResponse
    {
        try {
            $presets = ButtonPreset::popular(10)->with('creator:id,name')->get();
            
            return response()->json([
                'success' => true,
                'data' => $presets
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch popular presets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent presets
     */
    public function recent(): JsonResponse
    {
        try {
            $presets = ButtonPreset::recent(10)->with('creator:id,name')->get();
            
            return response()->json([
                'success' => true,
                'data' => $presets
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent presets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a new custom preset
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|in:custom,solid,outline,ghost,size',
            'style_settings' => 'required|array',
            'is_public' => 'boolean',
            'tags' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $preset = ButtonPreset::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'style_settings' => $request->input('style_settings'),
                'is_public' => $request->boolean('is_public', false),
                'tags' => $request->input('tags', []),
                'created_by' => Auth::guard('admin')->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Preset created successfully',
                'data' => $preset->load('creator:id,name')
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create preset',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific preset
     */
    public function show(ButtonPreset $preset): JsonResponse
    {
        try {
            $preset->incrementUsage();
            
            return response()->json([
                'success' => true,
                'data' => $preset->load('creator:id,name')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch preset',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update a custom preset
     */
    public function update(Request $request, ButtonPreset $preset): JsonResponse
    {
        // Check if user can edit this preset
        if (!$preset->canEdit(Auth::guard('admin')->id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this preset'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|in:custom,solid,outline,ghost,size',
            'style_settings' => 'required|array',
            'is_public' => 'boolean',
            'tags' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $preset->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'style_settings' => $request->input('style_settings'),
                'is_public' => $request->boolean('is_public', false),
                'tags' => $request->input('tags', [])
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Preset updated successfully',
                'data' => $preset->load('creator:id,name')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update preset',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a custom preset
     */
    public function destroy(ButtonPreset $preset): JsonResponse
    {
        // Check if user can delete this preset
        if (!$preset->canEdit(Auth::guard('admin')->id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this preset'
            ], 403);
        }
        
        try {
            $preset->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Preset deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete preset',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get preset categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = [
                'solid' => [
                    'name' => 'Solid',
                    'description' => 'Buttons with solid backgrounds',
                    'icon' => 'square'
                ],
                'outline' => [
                    'name' => 'Outline',
                    'description' => 'Buttons with borders only',
                    'icon' => 'square-outline'
                ],
                'ghost' => [
                    'name' => 'Ghost',
                    'description' => 'Subtle buttons with hover effects',
                    'icon' => 'ghost'
                ],
                'size' => [
                    'name' => 'Size Variants',
                    'description' => 'Different button sizes',
                    'icon' => 'resize'
                ],
                'custom' => [
                    'name' => 'Custom',
                    'description' => 'User-created presets',
                    'icon' => 'user'
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Apply a preset to button settings
     */
    public function apply(Request $request, string $presetId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_settings' => 'required|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $currentSettings = $request->input('current_settings');
            
            // Check if it's a built-in preset
            $builtinPresets = ButtonWidget::getBuiltinPresets();
            
            if (isset($builtinPresets[$presetId])) {
                // Apply built-in preset
                $widget = new ButtonWidget();
                $newSettings = $widget->applyPreset($presetId, $currentSettings);
            } else {
                // Apply custom preset
                $preset = ButtonPreset::findOrFail($presetId);
                $preset->incrementUsage();
                
                $styleSettings = $preset->getFormattedStyleSettings();
                $newSettings = $currentSettings;
                
                // Apply preset styles
                foreach ($styleSettings as $property => $value) {
                    $newSettings['style'][$property] = $value;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Preset applied successfully',
                'data' => $newSettings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply preset',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Convert built-in presets to collection format
     */
    private function getBuiltinPresetsAsCollection()
    {
        $builtinPresets = ButtonWidget::getBuiltinPresets();
        $collection = collect();
        
        foreach ($builtinPresets as $key => $preset) {
            $collection->push((object)[
                'id' => $key,
                'name' => $preset['name'],
                'slug' => $key,
                'description' => $preset['description'],
                'category' => $preset['category'],
                'style_settings' => $preset['style'],
                'is_public' => true,
                'is_builtin' => true,
                'preview_image' => null,
                'tags' => [],
                'created_by' => null,
                'usage_count' => 0,
                'created_at' => null,
                'updated_at' => null,
                'creator' => null
            ]);
        }
        
        return $collection;
    }
}
