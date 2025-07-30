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
        Schema::create('widget_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('widget_type', 100);
            $table->json('settings'); // Pre-configured widget settings
            $table->string('thumbnail', 255)->nullable(); // Template preview image
            $table->json('tags')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('usage_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0); // Average rating
            $table->integer('rating_count')->default(0);
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index(['widget_type', 'is_public', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['usage_count', 'status']);
            $table->index(['rating', 'status']);
            $table->index('created_by');
            $table->index('slug');
            
            // Foreign key constraint
            $table->foreign('widget_type')->references('type')->on('widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_templates');
    }
};