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
        Schema::create('villa_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->string('wbs_code')->nullable();
            $table->string('task_name');
            $table->foreignId('status_option_id')->nullable()->constrained('status_options')->nullOnDelete();
            $table->date('planned_start')->nullable();
            $table->date('planned_finish')->nullable();
            $table->date('actual_start')->nullable();
            $table->date('actual_finish')->nullable();
            $table->decimal('completion_pct', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('villa_id');
            $table->index('status_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_tasks');
    }
};
