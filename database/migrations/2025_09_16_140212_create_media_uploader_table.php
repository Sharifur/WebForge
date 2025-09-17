<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_uploader', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Original filename
            $table->string('stored_filename'); // Unique filename stored on disk
            $table->string('path'); // Storage path
            $table->string('url'); // Public URL
            $table->string('mime_type'); // image/jpeg, image/png, etc.
            $table->unsignedBigInteger('size'); // File size in bytes
            $table->unsignedInteger('width')->nullable(); // Image width in pixels
            $table->unsignedInteger('height')->nullable(); // Image height in pixels
            $table->string('alt')->nullable(); // Alt text for accessibility
            $table->string('title')->nullable(); // Image title
            $table->text('description')->nullable(); // Longer description
            $table->string('caption')->nullable(); // Image caption
            $table->unsignedBigInteger('uploaded_by')->nullable(); // User ID who uploaded
            $table->json('metadata')->nullable(); // Additional metadata (EXIF, etc.)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('mime_type');
            $table->index('uploaded_by');
            $table->index('is_active');
            $table->index(['mime_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_uploader');
    }
};
