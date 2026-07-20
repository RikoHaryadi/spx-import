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
         Schema::table('suite_data', function ($table) {

        $table->unique('shipment_id');

    });

    Schema::table('tracking_data', function ($table) {

        $table->unique('order_id');

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suite_data', function ($table) {

        $table->dropUnique(['shipment_id']);

    });

    Schema::table('tracking_data', function ($table) {

        $table->dropUnique(['order_id']);

    });
    }
};
