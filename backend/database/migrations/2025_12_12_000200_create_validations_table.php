<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('validations')) {
            Schema::create('validations', function (Blueprint $table) {
                $table->id();
                $table->string('type', 50);
                $table->unsignedBigInteger('user_id');
                $table->string('code', 6);
                $table->string('time', 20)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
