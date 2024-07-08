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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->string('order_type')->nullable();
            $table->string('client_name')->nullable();;
            $table->string('address')->nullable();;
            $table->string('country')->nullable();;
            $table->string('phone')->nullable();
            $table->string('alt_phone')->nullable();
            $table->string('city')->nullable();
            $table->string('sku_no')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('boxes')->nullable();
            $table->integer('weight')->nullable();
            $table->string('status');
            $table->date('delivery_date')->nullable();
            $table->string('pod')->nullable();
            $table->text('special_instruction')->nullable();
            $table->integer('distance')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('pod_returned')->default(false);
            $table->foreignId('client_id')->nullable()->constrained();
            $table->foreignId('geofence_id')->nullable()->constrained();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('rider_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('invoice_value', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->string('tracking_no')->nullable();
            $table->string('waybill_no')->nullable();
            $table->text('customer_notes')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('shipping_charges', 10, 2)->nullable();
            $table->decimal('charges', 10, 2)->nullable();
            $table->string('delivery_status')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('mpesa_code')->nullable();
            $table->string('platform')->nullable();
            $table->text('cancel_notes')->nullable();
            $table->boolean('is_return_waiting_for_approval')->default(false);
            $table->boolean('is_salesreturn_allowed')->default(false);
            $table->boolean('is_emailed')->default(false);
            $table->boolean('is_dropshipped')->default(false);
            $table->boolean('is_cancel_item_waiting_for_approval')->default(false);
            $table->boolean('track_inventory')->default(true);
            $table->boolean('confirmed')->default(false);
            $table->boolean('delivered')->default(false);
            $table->boolean('returned')->default(false);
            $table->boolean('cancelled')->default(false);
            $table->boolean('invoiced')->default(false);
            $table->boolean('packed')->default(false);
            $table->boolean('printed')->default(false);
            $table->integer('print_count')->default(0);
            $table->boolean('sticker_printed')->default(false);
            $table->boolean('prepaid')->default(false);
            $table->boolean('paid')->default(false);
            $table->integer('return_count')->default(0);
            $table->timestamp('dispatched_on')->nullable();
            $table->timestamp('return_date')->nullable();
            $table->timestamp('delivered_on')->nullable();
            $table->timestamp('returned_on')->nullable();
            $table->timestamp('cancelled_on')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->string('print_no')->nullable();
            $table->timestamp('sticker_at')->nullable();
            $table->timestamp('recall_date')->nullable();
            $table->text('history_comment')->nullable();
            $table->text('return_notes')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('receiver_address')->nullable();
            $table->string('pickup_address')->nullable();
            $table->string('pickup_phone')->nullable();
            $table->string('pickup_shop')->nullable();
            $table->string('upsell')->nullable();
            $table->string('pickup_city')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('schedule_date')->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->boolean('geocoded')->default(false);
            $table->string('loading_no')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
