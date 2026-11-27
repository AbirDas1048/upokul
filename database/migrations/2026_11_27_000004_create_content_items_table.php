<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One table for every repeatable block of the landing pages (services, FAQs, reviews...).
        // `title` is the headline / list label; everything type-specific lives in `data`.
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('title', 255);
            $table->json('data')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['type', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
