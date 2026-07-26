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
             $table->string('status_label')->nullable();

        $table->string('status_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_summary', function (Blueprint $table) {
                    $table->dropColumn([
            'status_label',
            'status_color'
        ]);
        });
    }
};
