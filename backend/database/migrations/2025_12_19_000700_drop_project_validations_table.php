<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('project_validations')) {
            Schema::drop('project_validations');
        }
    }
    public function down(): void
    {
        // noop: tabela removida definitivamente
    }
};
