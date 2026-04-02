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
        Schema::create('site_update_photos', function (Blueprint $table) {
            $table->id();
            $table->string('updateable_type');
            $table->unsignedBigInteger('updateable_id');
            $table->string('photo_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('caption')->nullable();
            $table->timestamps();

            $table->index(['updateable_type', 'updateable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_update_photos');
    }
};
