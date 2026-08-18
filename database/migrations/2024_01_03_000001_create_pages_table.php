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
        Schema::create('rost_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('template')->default('default');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_homepage')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('author_id');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('show_in_menu')->default(true);
            $table->string('menu_title')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index('status');
            $table->index('is_homepage');
            $table->index('parent_id');
            $table->index('author_id');
            $table->index('show_in_menu');
            $table->index('sort_order');
            $table->index(['status', 'show_in_menu']);

            // Внешние ключи
            $table->foreign('author_id')->references('id')->on('rost_users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('rost_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rost_pages');
    }
};
