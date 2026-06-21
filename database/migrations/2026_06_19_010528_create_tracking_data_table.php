<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_data', function (Blueprint $table) {

            $table->id();

            $table->string('order_id')->index();

            $table->string('driver_id')->nullable();
            $table->string('driver_name')->nullable();

            $table->dateTime('received_time')->nullable();
            $table->dateTime('current_station_received_time')->nullable();
            $table->dateTime('delivering_time')->nullable();
            $table->dateTime('delivered_time')->nullable();
            $table->dateTime('on_hold_time')->nullable();

            $table->string('on_hold_reason')->nullable();

            $table->dateTime('reschedule_date')->nullable();

            $table->string('status')->nullable();

            $table->string('order_account')->nullable();

            $table->string('payment_method')->nullable();

            $table->string('current_station')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_data');
    }
};