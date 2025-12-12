<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('social_projects')) {
            Schema::create('social_projects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->string('address', 255)->nullable();
                $table->string('district', 255)->nullable();
                $table->string('city', 255)->nullable();
                $table->char('state', 2)->nullable();
                $table->string('zip_code', 20)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('contact_email', 255)->nullable();
                $table->string('website_url', 255)->nullable();
                $table->string('visual_color', 20)->nullable();
                $table->boolean('verified')->default(false);
                $table->timestamp('verified_at')->nullable();
                $table->string('badge', 50)->nullable();
                $table->string('status', 50)->nullable();
                $table->string('activity_area', 255)->nullable();
                $table->text('target_audiences')->nullable();
                $table->text('needs')->nullable();
                $table->string('image_path', 255)->nullable();
                $table->timestamp('created_at')->useCurrent()->nullable();
                $table->timestamp('updated_at')->useCurrent()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('social_projects');
    }
};
