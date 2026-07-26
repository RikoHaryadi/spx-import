<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_summary', function (Blueprint $table) {

            // hapus unique lama
            $table->dropUnique(
                'monitoring_summary_operation_date_driver_id_unique'
            );

            // buat unique baru
            $table->unique(
                [
                    'hub_id',
                    'operation_date',
                    'driver_id'
                ],
                'monitoring_summary_hub_date_driver_unique'
            );

        });
    }

    public function down(): void
    {
        Schema::table('monitoring_summary', function (Blueprint $table) {

            $table->dropUnique(
                'monitoring_summary_hub_date_driver_unique'
            );

            $table->unique(
                [
                    'operation_date',
                    'driver_id'
                ],
                'monitoring_summary_operation_date_driver_id_unique'
            );

        });
    }
};