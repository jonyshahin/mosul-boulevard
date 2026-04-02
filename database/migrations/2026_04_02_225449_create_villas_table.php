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
        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('villa_type_id')->constrained('villa_types')->cascadeOnDelete();
            $table->boolean('is_sold')->default(false);
            $table->string('customer_name')->nullable();
            $table->date('sale_date')->nullable();
            $table->foreignId('current_stage_id')->nullable()->constrained('construction_stages')->nullOnDelete();
            $table->foreignId('status_option_id')->nullable()->constrained('status_options')->nullOnDelete();
            $table->foreignId('engineer_id')->nullable()->constrained('engineers')->nullOnDelete();
            $table->date('planned_start')->nullable();
            $table->date('planned_finish')->nullable();
            $table->date('actual_start')->nullable();
            $table->date('actual_finish')->nullable();
            $table->decimal('completion_pct', 5, 2)->default(0);
            $table->decimal('acc_concrete_qty', 10, 2)->default(0);
            $table->decimal('acc_steel_qty', 10, 2)->default(0);
            $table->unsignedBigInteger('structural_status_id')->nullable();
            $table->foreign('structural_status_id')->references('id')->on('status_options')->nullOnDelete();
            $table->unsignedBigInteger('finishing_status_id')->nullable();
            $table->foreign('finishing_status_id')->references('id')->on('status_options')->nullOnDelete();
            $table->unsignedBigInteger('facade_status_id')->nullable();
            $table->foreign('facade_status_id')->references('id')->on('status_options')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('villa_type_id');
            $table->index('is_sold');
            $table->index('engineer_id');
            $table->index('status_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villas');
    }
};
