<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('project_validations')) {
            Schema::create('project_validations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('social_project_id');
                $table->string('channel', 20); // email | phone
                $table->string('destination', 255);
                $table->string('code', 12);
                $table->string('status', 20)->default('pending'); // pending | verified | expired
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->integer('attempts')->default(0);
                $table->timestamps();

                $table->index(['social_project_id']);
                $table->index(['channel']);
                $table->index(['status']);
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('project_validations');
    }
};
