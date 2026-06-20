<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('social_projects', function (Blueprint $table) {
            $table->string('instagram_url')->nullable()->after('website_url');
            $table->string('facebook_url')->nullable()->after('instagram_url');
            $table->text('operating_hours')->nullable()->after('facebook_url');
            $table->string('mission', 300)->nullable()->after('operating_hours');
        });
        Schema::table('personalized_pages', function (Blueprint $table) {
            $table->text('gallery_images')->nullable()->after('caption');
        });
    }
    public function down(): void {
        Schema::table('social_projects', function (Blueprint $table) {
            $table->dropColumn(['instagram_url','facebook_url','operating_hours','mission']);
        });
        Schema::table('personalized_pages', function (Blueprint $table) {
            $table->dropColumn('gallery_images');
        });
    }
};
