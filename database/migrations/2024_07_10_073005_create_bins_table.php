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
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->string('code', 191);
            $table->string('name', 191);
            $table->foreignId('warehouse_id')->constrained()->OnDelete('cascade');
            $table->foreignId('row_id')->constrained()->OnDelete('cascade');
            $table->foreignId('bay_id')->constrained()->OnDelete('cascade');
            $table->foreignId('level_id')->constrained()->OnDelete('cascade');
            $table->foreignId('area_id')->constrained()->OnDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bins');
    }
};
