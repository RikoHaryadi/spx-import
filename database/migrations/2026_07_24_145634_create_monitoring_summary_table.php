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
        Schema::create('monitoring_summary', function (Blueprint $table) {
   $table->id();

        $table->date('operation_date')->index();

        $table->string('driver_id')->index();

        $table->string('driver_name');

        $table->integer('total')->default(0);

        $table->integer('delivered')->default(0);

        $table->integer('onhold')->default(0);

        $table->integer('remaining')->default(0);

        $table->decimal('progress',5,2)->default(0);

        $table->timestamps();

        $table->unique([
            'operation_date',
            'driver_id'
        ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_summary');
    }
};
