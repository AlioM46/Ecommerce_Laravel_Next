<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 18, 2);
    $table->decimal('discount_price', 18, 2)->nullable();
    $table->string('brand')->nullable();
    $table->float('rating')->nullable();
    $table->integer('reviews_count')->nullable();


$table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->integer('in_stock');
    $table->boolean('is_active');
    $table->string('sku', 450)->nullable();
    $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
