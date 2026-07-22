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
        Schema::create('dashboard_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_key', 100)->unique();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('module', 100);
            $table->string('status', 20)->default('active')->index();
            $table->string('region', 10)->index();
            $table->timestamp('updated_at_source')->index();
            $table->timestamps();

            $table->index(['region', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_records');
    }
};
