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
        Schema::create('widget_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('widget_type', 100);
            $table->foreignId('widget_instance_id')->nullable()->constrained('widget_instances')->onDelete('cascade');
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('event_type', ['view', 'click', 'form_submit', 'drag', 'edit', 'delete']);
            $table->json('event_data')->nullable(); // Additional event context
            $table->string('user_agent', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->json('device_info')->nullable(); // Browser, OS, device type
            $table->string('referrer', 500)->nullable();
            $table->timestamp('event_time');
            $table->timestamps();

            // Indexes for analytics queries
            $table->index(['widget_type', 'event_type', 'event_time']);
            $table->index(['page_id', 'event_type', 'event_time']);
            $table->index(['user_id', 'event_type', 'event_time']);
            $table->index(['widget_instance_id', 'event_type']);
            $table->index('event_time');
            $table->index('session_id');
            
            // Foreign key constraint
            $table->foreign('widget_type')->references('type')->on('widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_analytics');
    }
};