<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('widget_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->string('icon', 50);
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6B7280'); // Hex color
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'sort_order']);
            $table->index('slug');
        });

        // Insert default categories
        $this->insertDefaultCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_categories');
    }

    /**
     * Insert default widget categories
     */
    private function insertDefaultCategories(): void
    {
        $categories = [
            [
                'slug' => 'basic',
                'name' => 'Basic',
                'icon' => 'square',
                'description' => 'Essential widgets for basic functionality',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'content',
                'name' => 'Content',
                'icon' => 'file-text',
                'description' => 'Text, headings, and content widgets',
                'color' => '#10B981',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'media',
                'name' => 'Media',
                'icon' => 'image',
                'description' => 'Images, videos, and media widgets',
                'color' => '#F59E0B',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'form',
                'name' => 'Form',
                'icon' => 'clipboard-list',
                'description' => 'Form elements and input widgets',
                'color' => '#EF4444',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'layout',
                'name' => 'Layout',
                'icon' => 'layout',
                'description' => 'Structural and layout widgets',
                'color' => '#8B5CF6',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'ecommerce',
                'name' => 'E-commerce',
                'icon' => 'shopping-cart',
                'description' => 'Shopping and e-commerce widgets',
                'color' => '#EC4899',
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'social',
                'name' => 'Social',
                'icon' => 'share-2',
                'description' => 'Social media and sharing widgets',
                'color' => '#06B6D4',
                'sort_order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'navigation',
                'name' => 'Navigation',
                'icon' => 'menu',
                'description' => 'Navigation and menu widgets',
                'color' => '#84CC16',
                'sort_order' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'seo',
                'name' => 'SEO',
                'icon' => 'search',
                'description' => 'SEO and marketing widgets',
                'color' => '#F97316',
                'sort_order' => 9,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'advanced',
                'name' => 'Advanced',
                'icon' => 'code',
                'description' => 'Advanced and custom widgets',
                'color' => '#6B7280',
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'third_party',
                'name' => 'Third Party',
                'icon' => 'external-link',
                'description' => 'Third-party integrations and widgets',
                'color' => '#374151',
                'sort_order' => 11,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('widget_categories')->insert($categories);
    }
};