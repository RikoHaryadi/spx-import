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

    $table->foreignId('hub_id')
          ->nullable()
          ->after('driver_name')
          ->constrained('hubs')
          ->nullOnDelete();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_summary', function (Blueprint $table) {
             $table->dropConstrainedForeignId('hub_id');
        });
    }
};
