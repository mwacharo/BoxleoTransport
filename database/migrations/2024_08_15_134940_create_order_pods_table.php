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
    
            // Schema::create('order_pods', function (Blueprint $table) {
            //     $table->bigIncrements('id');
            //     $table->unsignedBigInteger('order_no');
            //     $table->string('pod_path'); // Path to the POD file (JPG or PDF)
            //     $table->timestamps();
            //     $table->softDeletes();
    
            //     // Foreign key constraint
            //     $table->foreign('order_no')->references('order_no')->on('orders')->onDelete('cascade');
            // });

            Schema::create('order_pods', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('order_no'); // Match the data type of order_no in orders table
                $table->string('pod_path'); // Path to the POD file (JPG or PDF)
                $table->timestamps();
                $table->softDeletes();
    
                // Foreign key constraint
                $table->foreign('order_no')->references('order_no')->on('orders')->onDelete('cascade');
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_pods');
    }
};
