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

            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            // $table->unsignedBigInteger('category_id')->nullable();
            // $table->unsignedBigInteger('subcategory_id')->nullable();
            // $table->string('parent_sku')->nullable();
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->foreignId('vendor_id')->constrained()->onDeletedelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDeletedelete('cascade');
            // $table->integer('quantity')->nullable();
            //fast  quanitity merchant brings on start of operation  
            $table->integer('opening_quantity')->nullable();
            // once an item is returned the return_quantity increases 
            // same value returned increases quantity_inhand and also  quantity_inhand
            $table->integer('return_quantity')->nullable();

            // historical value of stock(quanitty) 
            // an increase in  quantity_in result an increase in quantity_inhand with same value 
            $table->integer('quantity_inhand')->nullable();


            // receiving   of stock  increases this value
            // increases quantity_inhand and quantity_remaining with same value 
            $table->integer('quantity_in')->nullable();
            
            // upon request by merchant stock transfers 
            // reduces  quantity_remaining with same value
            $table->integer('quantity_out')->nullable();
            // quantity lost or damaged
            // reduces  quanitity_remaining
            // reduces quantity_inhand
            $table->integer('pilferages_destroyed')->nullable();
            // when quantity_remaining  is equal this value an email notification is sent to merchant infroming them to restock 
            $table->integer('reorder_point')->nullable();
            // $table->string('image');
            $table->text('description')->nullable();
            $table->decimal('unit_buying_price', 8, 2)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            // this value of stock sold  to customers 
            // ondispatch of item  the  quantity_issued increases
            // result  in reduction of quantity_remaining with same value
            $table->decimal('quantity_issued', 10, 2)->nullable();

            $table->decimal('inventory_value', 10, 2)->nullable();
            $table->decimal('sales_value', 10, 2)->nullable();

            // available for sale 
            $table->decimal('quantity_remaining', 10, 2)->nullable();
            $table->decimal('physical_count', 10, 2)->nullable();
            $table->decimal('variance', 10, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('category_id')->references('id')->on('categories');
            // $table->foreign('subcategory_id')->references('id')->on('subcategories');
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
