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
        Schema::create('button_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('custom'); // custom, solid, outline, ghost, size
            $table->json('style_settings'); // Store the button style configuration
            $table->boolean('is_public')->default(false); // Can be shared with other users
            $table->boolean('is_builtin')->default(false); // System presets vs user presets
            $table->string('preview_image')->nullable(); // Optional preview image
            $table->json('tags')->nullable(); // Searchable tags
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->integer('usage_count')->default(0); // Track popularity
            $table->timestamps();
            
            $table->index(['category', 'is_public']);
            $table->index(['created_by', 'is_public']);
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('button_presets');
    }
};
