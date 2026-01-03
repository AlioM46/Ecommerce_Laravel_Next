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
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('shipping_address_id')->constrained('addresses')->onDelete('restrict');
    $table->decimal('total_price', 18, 2);
    $table->enum('status', ['pending','paid','shipped','completed','cancelled'])->default('pending');
    $table->timestamps();
    // $table->softDeletes(); // optional, if you want
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
