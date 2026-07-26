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
  Schema::table('users', function (Blueprint $table) {

        $table->string('nik')->nullable()->after('name');

        $table->foreignId('hub_id')
              ->nullable()
              ->after('password')
              ->constrained('hubs')
              ->nullOnDelete();

        $table->enum('role',[
            'owner',
            'manager',
            'spv',
            'viewer'
        ])->default('viewer');

        $table->boolean('is_active')
              ->default(true);

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

        $table->dropConstrainedForeignId('hub_id');

        $table->dropColumn([
            'nik',
            'role',
            'is_active'
        ]);

    });
    }
};
