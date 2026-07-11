<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personalized_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_project_id')->constrained('social_projects')->onDelete('cascade');
            $table->text('caption')->nullable();
            $table->string('url')->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personalized_pages');
    }
};
