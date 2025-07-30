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
        Schema::create('widget_instances', function (Blueprint $table) {
            $table->id();
            $table->string('widget_type', 100); // References widgets.type
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->string('container_id', 100)->nullable(); // Section/container ID
            $table->string('column_id', 100)->nullable(); // Column ID within container
            $table->json('position'); // Position data (order, coordinates, etc.)
            $table->json('settings'); // Widget instance settings
            $table->json('cached_output')->nullable(); // Cached rendered output
            $table->boolean('is_visible')->default(true);
            $table->timestamp('cached_at')->nullable();
            $table->string('version', 20)->default('1.0.0'); // Widget version used
            $table->json('responsive_settings')->nullable(); // Device-specific settings
            $table->integer('view_count')->default(0); // Analytics
            $table->integer('interaction_count')->default(0); // Analytics
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('last_interacted_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['page_id', 'is_visible']);
            $table->index(['widget_type', 'page_id']);
            $table->index(['container_id', 'column_id']);
            $table->index('widget_type');
            $table->index('page_id');
            
            // Foreign key constraint
            $table->foreign('widget_type')->references('type')->on('widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_instances');
    }
};