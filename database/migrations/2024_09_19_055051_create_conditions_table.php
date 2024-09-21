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
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')->constrained()->OnDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->foreignId('branch_id')->nullable()->constrained();
            $table->string('condition_amount')->nullable();
            $table->string('condition_percentage')->nullable();
            $table->string('region')->nullable();
            $table->string('route')->nullable();
            $table->string('rate_3t')->nullable();
            $table->string('rate_5t')->nullable();
            $table->string('rate_7t')->nullable();
            $table->string('rate_10t')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
// vendor_service_conditions table 
// 