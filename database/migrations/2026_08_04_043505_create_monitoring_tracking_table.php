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
    Schema::create('monitoring_tracking', function (Blueprint $table) {

        $table->id();

        $table->string('order_id',100)->unique();

        $table->string('driver_id',50)->nullable()->index();
        $table->string('driver_name',255)->nullable()->index();

        $table->string('initial_driver_id',50)->nullable();
        $table->string('initial_driver_name',255)->nullable();

        $table->string('current_driver_id',50)->nullable();
        $table->string('current_driver_name',255)->nullable();

        $table->foreignId('hub_id')
            ->nullable()
            ->constrained('hubs')
            ->nullOnDelete();

        $table->dateTime('received_time')->nullable();
        $table->dateTime('current_station_received_time')->nullable();
        $table->dateTime('delivering_time')->nullable();
        $table->dateTime('delivered_time')->nullable();
        $table->dateTime('on_hold_time')->nullable();

        $table->string('on_hold_reason',255)->nullable();

        $table->dateTime('reschedule_date')->nullable();

        $table->string('status',100)->nullable()->index();

        $table->date('operation_date')->nullable()->index();

        $table->string('order_account',255)->nullable();

        $table->string('payment_method',100)->nullable();

        $table->string('current_station',255)->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_tracking');
    }
};
