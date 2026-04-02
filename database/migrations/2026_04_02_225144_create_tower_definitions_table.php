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
        Schema::create('tower_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code_prefix');
            $table->unsignedInteger('total_floors')->default(20);
            $table->unsignedInteger('units_per_floor')->default(4);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tower_definitions');
    }
};
