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
        Schema::create('tower_site_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_unit_id')->constrained('tower_units')->cascadeOnDelete();
            $table->date('update_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tower_unit_id');
            $table->index('update_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tower_site_updates');
    }
};
