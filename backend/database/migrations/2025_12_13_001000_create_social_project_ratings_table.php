<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('social_project_ratings')) {
            Schema::create('social_project_ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('social_project_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedTinyInteger('rating');
                $table->text('feedback_text')->nullable();
                $table->timestamps();
                $table->unique(['social_project_id', 'user_id']);
                $table->index(['social_project_id']);
                $table->index(['user_id']);
            });
        }
        if (!Schema::hasColumn('social_projects', 'rating_avg')) {
            Schema::table('social_projects', function (Blueprint $table) {
                $table->decimal('rating_avg', 3, 2)->default(0)->nullable();
                $table->unsignedInteger('rating_count')->default(0)->nullable();
            });
        }
    }
    public function down(): void
    {
        if (Schema::hasTable('social_project_ratings')) {
            Schema::dropIfExists('social_project_ratings');
        }
        Schema::table('social_projects', function (Blueprint $table) {
            if (Schema::hasColumn('social_projects', 'rating_avg')) {
                $table->dropColumn('rating_avg');
            }
            if (Schema::hasColumn('social_projects', 'rating_count')) {
                $table->dropColumn('rating_count');
            }
        });
    }
};
