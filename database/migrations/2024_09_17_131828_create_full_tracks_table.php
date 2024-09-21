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
        Schema::create('full_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('region');
            $table->string('route');
            $table->decimal('rate_3t', 8, 2);
            $table->decimal('rate_5t', 8, 2);
            $table->decimal('rate_7t', 8, 2);
            $table->decimal('rate_10t', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('full_tracks');
    }
};
