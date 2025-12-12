<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('personalized_pages')) {
            Schema::create('personalized_pages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('social_project_id');
                $table->string('url', 255);
                $table->string('caption', 255)->nullable();
                $table->integer('sort_order')->default(0);
                $table->integer('template');
                $table->foreign('social_project_id')->references('id')->on('social_projects')->onDelete('cascade');
                $table->unique('url');
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('personalized_pages');
    }
};
