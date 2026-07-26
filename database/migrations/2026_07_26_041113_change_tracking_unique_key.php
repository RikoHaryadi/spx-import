<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_data', function (Blueprint $table) {

            // Hapus unique lama
            $table->dropUnique('tracking_data_order_id_unique');

            // Tambahkan unique baru
            $table->unique(
                ['order_id','hub_id'],
                'tracking_order_hub_unique'
            );

        });
    }

    public function down(): void
    {
        Schema::table('tracking_data', function (Blueprint $table) {

            $table->dropUnique('tracking_order_hub_unique');

            $table->unique('order_id');

        });
    }
};