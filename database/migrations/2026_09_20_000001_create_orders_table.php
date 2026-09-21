<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            // Delivery/contact snapshots, independent of any future customer profile.
            $table->string('customer_name', 100);
            $table->string('customer_phone', 30);
            $table->string('customer_email', 150)->nullable();
            $table->string('address', 250);
            $table->string('area', 100);
            $table->string('city', 100);
            $table->string('delivery_zone', 30);
            $table->string('notes', 500)->nullable();
            $table->char('currency', 3)->default('BDT');
            $table->decimal('subtotal', 16, 2);
            $table->decimal('delivery_charge', 16, 2);
            $table->decimal('discount_amount', 16, 2)->default(0);
            $table->decimal('total', 16, 2);
            $table->string('payment_method', 30)->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
