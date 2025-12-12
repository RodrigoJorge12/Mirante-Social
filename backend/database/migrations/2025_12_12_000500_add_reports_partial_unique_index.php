<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS uq_reports_open_by_user_project ON reports (reporter_user_id, social_project_id) WHERE status IN ('pending','under_review')");
    }
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS uq_reports_open_by_user_project");
    }
};
