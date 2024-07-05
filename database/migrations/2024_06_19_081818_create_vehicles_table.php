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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('license_plate')->unique();
            $table->integer('capacity')->nullable();
            $table->string('status')->default('available');
            $table->json('address')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->json('current_location')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            // $table->foreignId('depot_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
