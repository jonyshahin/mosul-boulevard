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
        Schema::create('tower_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('tower_definition_id')->constrained('tower_definitions')->cascadeOnDelete();
            $table->foreignId('floor_definition_id')->nullable()->constrained('floor_definitions')->nullOnDelete();
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
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tower_definition_id');
            $table->index('floor_definition_id');
            $table->index('is_sold');
            $table->index('engineer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tower_units');
    }
};
