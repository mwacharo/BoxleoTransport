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
        Schema::create('sheets', function (Blueprint $table) {
            $table->id();
            $table->string('sheet_name', 191)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('post_spreadsheet_id')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('auto_sync')->default(true);
            $table->boolean('sync_all')->default(false);
            $table->integer('sync_interval')->default(30);
            $table->dateTime('last_order_synced')->nullable();
            $table->dateTime('last_order_upload')->nullable();
            $table->dateTime('last_product_synced')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('order_prefix')->nullable();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->string('lastUpdatedOrderNumber')->nullable();
            $table->bigInteger('ou_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheets');
    }
};
