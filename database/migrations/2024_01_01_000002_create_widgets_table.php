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
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100)->unique(); // Widget type identifier
            $table->string('name', 150); // Display name
            $table->string('icon', 50); // Icon identifier
            $table->foreignId('category_id')->constrained('widget_categories')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->json('tags')->nullable(); // Array of tags
            $table->boolean('is_pro')->default(false);
            $table->json('config')->nullable(); // Widget configuration
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->integer('usage_count')->default(0); // Track usage for analytics
            $table->timestamp('last_used_at')->nullable();
            $table->string('version', 20)->default('1.0.0');
            $table->string('author', 100)->nullable();
            $table->string('author_url', 255)->nullable();
            $table->text('changelog')->nullable();
            $table->json('requirements')->nullable(); // Dependencies, PHP version, etc.
            $table->timestamps();

            // Indexes
            $table->index(['category_id', 'status', 'sort_order']);
            $table->index(['is_pro', 'status']);
            $table->index(['usage_count', 'status']);
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};