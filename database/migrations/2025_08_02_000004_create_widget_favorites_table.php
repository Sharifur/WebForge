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
        Schema::create('widget_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('widget_type', 100);
            $table->timestamps();

            // Unique constraint to prevent duplicate favorites
            $table->unique(['user_id', 'widget_type']);
            
            // Indexes
            $table->index('user_id');
            $table->index('widget_type');
            
            // Foreign key constraint
            $table->foreign('widget_type')->references('type')->on('widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_favorites');
    }
};