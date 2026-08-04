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
        Schema::table('monitoring_summary', function (Blueprint $table) {

            $table->integer('delivering')
                  ->default(0)
                  ->after('onhold');

            $table->integer('lmhub_received')
                  ->default(0)
                  ->after('delivering');

            $table->integer('cod_total')
                  ->default(0)
                  ->after('lmhub_received');

            $table->integer('cod_delivered')
                  ->default(0)
                  ->after('cod_total');

            $table->integer('noncod_total')
                  ->default(0)
                  ->after('cod_delivered');

            $table->integer('noncod_delivered')
                  ->default(0)
                  ->after('noncod_total');
            $table->integer('transfer_in')
                ->default(0)
                ->after('noncod_delivered');

            $table->integer('transfer_out')
                ->default(0)
                ->after('transfer_in');         

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_summary', function (Blueprint $table) {

            $table->dropColumn([
                'delivering',
                'lmhub_received',
                'cod_total',
                'cod_delivered',
                'noncod_total',
                'noncod_delivered',
                'transfer_in',
                'transfer_out',
            ]);

        });
    }
};