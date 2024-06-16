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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name', 191)->nullable();
            $table->longText('phone')->nullable();
            $table->longText('location')->nullable();
            $table->string('length', 191)->default('0');
            $table->string('width', 191)->default('0');
            $table->string('height', 191)->default('0');
            $table->string('non_storage', 191)->default('0');
            $table->string('capacity', 191)->default('0');
            $table->string('code', 191);
            $table->softDeletes();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
