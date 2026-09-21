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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('sku', 60)->nullable()->unique();
            $table->text('description')->nullable();
            // Null means not yet set; zero is a known price or stock quantity.
            $table->decimal('price', 12, 2)->nullable();
            $table->char('currency', 3)->nullable();
            $table->unsignedInteger('stock')->nullable();
            $table->enum('status', ['draft', 'active'])->default('draft');
            $table->string('dimensions', 120)->nullable();
            $table->string('material', 120)->nullable();
            $table->timestamps();
            $table->index(['category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
