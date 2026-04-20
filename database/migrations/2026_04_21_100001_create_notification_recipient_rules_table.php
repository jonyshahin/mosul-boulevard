<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_recipient_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_type_id')->nullable()->constrained('request_types')->nullOnDelete();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->foreignId('recipient_user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            $table->index(['request_type_id', 'severity', 'is_active']);
            $table->unique(['request_type_id', 'severity', 'recipient_user_id'], 'nrr_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_recipient_rules');
    }
};
