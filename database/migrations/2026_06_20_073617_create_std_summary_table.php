<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('std_summary', function (Blueprint $table) {

            $table->id();

            $table->date('date_id')->nullable();

            $table->string('shipment_id')->unique();

            $table->string('hub')->nullable();

            $table->string('driver_id')->nullable();
            $table->string('driver_name')->nullable();

            $table->string('payment_method')->nullable();

            $table->string('order_account')->nullable();

            $table->text('on_hold_reason')->nullable();

            $table->string('tracking_status')->nullable();

            $table->string('fifo_status')->nullable();

            $table->dateTime('delivered_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('std_summary');
    }
};