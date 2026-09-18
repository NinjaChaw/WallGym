<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('status')->default(false)->change();
            $table->unsignedInteger('sort_order')->default(1)->change();
            $table->index(['sort_order', 'id']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['sort_order', 'id']);
            $table->boolean('status')->default(true)->change();
            $table->unsignedInteger('sort_order')->default(0)->change();
        });
    }
};
