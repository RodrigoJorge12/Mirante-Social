<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('social_project_id');
                $table->unsignedBigInteger('reporter_user_id');
                $table->string('category', 50);
                $table->text('reason');
                $table->string('status', 20)->default('pending');
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->timestamps();

                $table->index(['social_project_id']);
                $table->index(['reporter_user_id']);
                $table->index(['status']);
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
