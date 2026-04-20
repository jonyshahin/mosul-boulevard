<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_request_id')->constrained('inspection_requests')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->text('body');
            $table->enum('triggers_status', ['in_progress', 'resolved', 'verified', 'closed', 'reopened'])->nullable();
            $table->timestamps();

            $table->index('inspection_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_replies');
    }
};
