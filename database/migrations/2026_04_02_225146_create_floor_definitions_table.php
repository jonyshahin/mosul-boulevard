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
        Schema::create('floor_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_definition_id')->constrained('tower_definitions')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('floor_number');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['tower_definition_id', 'floor_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floor_definitions');
    }
};
