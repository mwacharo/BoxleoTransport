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
            $table->string('sku')->unique();
            $table->string('name');
            $table->foreignId('vendor_id')->constrained()->onDeletedelete('cascade');
            // $table->foreignId('warehouse_id')->constrained()->onDeletedelete('cascade');
            // $table->foreignId('ou_id')->constrained()->onDeletedelete('cascade');
            $table->string('quantity');
            $table->integer('reorder_point')->nullable();           
             // $table->string('image');
            $table->text('description')->nullable();
            $table->decimal('buying_price', 8, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->softDeletes();
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
