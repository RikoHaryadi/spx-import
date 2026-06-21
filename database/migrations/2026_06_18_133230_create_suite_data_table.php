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
        Schema::create('suite_data', function (Blueprint $table) {
    $table->id();

    $table->date('date_id')->nullable();

    $table->string('shipment_id')->index();

    $table->string('lmhub_station_name')->nullable();
    $table->string('inbound_group')->nullable();

    $table->dateTime('delivered_time')->nullable();
    $table->dateTime('transported_time')->nullable();
    $table->dateTime('assigned_delivering_time')->nullable();

    $table->integer('on_hold_count')->default(0);

    $table->dateTime('assigned_time')->nullable();
    $table->dateTime('last_on_hold_timestamp')->nullable();

    $table->string('addr_zone_name')->nullable();

    $table->string('driver_id')->nullable();

    $table->string('within_cutoff_delivered')->nullable();
    $table->string('within_cutoff_assigned')->nullable();
    $table->string('within_assigned_delivering')->nullable();

    $table->boolean('is_lmhub_delivery_transfer')->default(false);

    $table->string('status')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suite_data');
    }
};
