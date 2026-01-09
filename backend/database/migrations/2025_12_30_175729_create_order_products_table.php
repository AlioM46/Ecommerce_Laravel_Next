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
        Schema::create('order_product', function (Blueprint $table) {
            $table->id(); // surrogate primary key
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // New columns for variants
            $table->string('size', 20)->nullable();
            $table->string('color', 20)->nullable();

            $table->integer('quantity')->default(1);
            $table->decimal('price', 18, 2); // price at time of purchase

            // Unique constraint for variant combination
            $table->unique(['order_id', 'product_id', 'size', 'color']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
