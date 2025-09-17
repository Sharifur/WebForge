<?php

namespace App\Http\Controllers;

use App\Models\MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MediaUploadController extends Controller
{
    /**
     * Upload and store media file
     */
    public function upload(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,webp,svg',
                'max:5120', // 5MB max
            ],
            'alt' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('image');

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $storedFilename = time() . '_' . Str::random(10) . '.' . $extension;

            // Define storage path
            $storagePath = 'media/' . date('Y/m');
            $fullPath = $storagePath . '/' . $storedFilename;

            // Store the file
            $path = $file->storeAs($storagePath, $storedFilename, 'public');

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to store file'
                ], 500);
            }

            // Get image dimensions if it's an image
            $width = null;
            $height = null;
            $metadata = [];

            if (str_starts_with($file->getMimeType(), 'image/')) {
                try {
                    $imagePath = storage_path('app/public/' . $path);
                    $imageSize = getimagesize($imagePath);

                    if ($imageSize) {
                        $width = $imageSize[0];
                        $height = $imageSize[1];

                        // Get EXIF data if available
                        if (function_exists('exif_read_data') && in_array($extension, ['jpg', 'jpeg'])) {
                            $exif = @exif_read_data($imagePath);
                            if ($exif) {
                                $metadata['exif'] = $exif;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Continue without dimensions if we can't get them
                }
            }

            // Create media record
            $media = MediaUploader::create([
                'filename' => $originalName,
                'stored_filename' => $storedFilename,
                'path' => $path,
                'url' => Storage::url($path),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'alt' => $request->input('alt', ''),
                'title' => $request->input('title', pathinfo($originalName, PATHINFO_FILENAME)),
                'description' => $request->input('description', ''),
                'caption' => $request->input('caption', ''),
                'uploaded_by' => auth('admin')->id(),
                'metadata' => !empty($metadata) ? $metadata : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'id' => $media->id,
                'url' => $media->url,
                'filename' => $media->filename,
                'stored_filename' => $media->stored_filename,
                'alt' => $media->alt,
                'title' => $media->title,
                'description' => $media->description,
                'caption' => $media->caption,
                'size' => $media->size,
                'formatted_size' => $media->formatted_size,
                'width' => $media->width,
                'height' => $media->height,
                'mime_type' => $media->mime_type,
                'extension' => $media->extension,
                'is_image' => $media->is_image,
                'created_at' => $media->created_at,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update media metadata
     */
    public function updateMetadata(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'alt' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $media = MediaUploader::findOrFail($id);

            $media->update($request->only(['alt', 'title', 'description', 'caption']));

            return response()->json([
                'success' => true,
                'message' => 'Media updated successfully',
                'media' => $media
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get media library
     */
    public function index(Request $request): JsonResponse
    {
        $query = MediaUploader::active()
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type') && $request->type === 'images') {
            $query->images();
        }

        // Search by filename
        if ($request->has('search') && $request->search) {
            $query->where('filename', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->input('per_page', 20);
        $media = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $media
        ]);
    }

    /**
     * Delete media file
     */
    public function destroy($id): JsonResponse
    {
        try {
            $media = MediaUploader::findOrFail($id);

            // Delete the file
            $media->deleteFile();

            // Delete the record
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
