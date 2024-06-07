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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('sheet_update')->default(true);
            $table->boolean('send_sms')->default(true);
            $table->boolean('telegram_notifications')->default(true);
            $table->string('shopify_email')->nullable();
            $table->string('order_no_start')->nullable();
            $table->string('order_no_end')->nullable();
            $table->boolean('autogenerate')->default(false);
            $table->boolean('portal_active')->default(false);
            $table->text('terms')->nullable();
            $table->bigInteger('company_id')->nullable();
            $table->bigInteger('last_identifier')->default(1);
            $table->bigInteger('ou_id')->nullable();
            $table->string('order_prefix')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
